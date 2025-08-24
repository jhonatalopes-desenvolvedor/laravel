<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class ListenerAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa um arquivo de Listener e retorna informações sobre eventos tratados, se é enfileirável e propriedades da fila.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isListener              = false;
        $listensToEvents         = [];
        $isQueued                = false;
        $constructorDeps         = [];
        $hasHandleMethod         = false;
        $hasMultiHandleMethods   = false;
        $multiHandleMethodsCount = 0;
        $tries                   = null;
        $timeout                 = null;
        $maxExceptions           = null;
        $foundClass              = false;
        $className               = null;

        try {
            $parser = $this->parserFactory->createForNewestSupportedVersion();
            $code   = File::get($filePath);
            $stmts  = $parser->parse($code);

            if ( ! $stmts) {
                return ['error' => 'Não foi possível analisar o arquivo PHP. Pode estar vazio ou ter sintaxe inválida.'];
            }

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new NameResolver());
            $stmts = $traverser->traverse($stmts);

            foreach ($stmts as $stmt) {
                if ($stmt instanceof Stmt\Namespace_) {
                    foreach ($stmt->stmts as $subStmt) {
                        if ($subStmt instanceof Class_) {
                            $className  = (string) $subStmt->name;
                            $isListener = $this->isListenerClass($subStmt);
                            $foundClass = true;

                            if ($isListener) {
                                $isQueued                                                            = $this->implementsShouldQueue($subStmt);
                                $constructorDeps                                                     = $this->getConstructorDependencies($subStmt);
                                $hasHandleMethod                                                     = $this->classHasMethod($subStmt, 'handle');
                                [$listensToEvents, $hasMultiHandleMethods, $multiHandleMethodsCount] = $this->getHandledEventsAndMethods($subStmt);
                                [$tries, $timeout, $maxExceptions]                                   = $this->getListenerQueueProperties($subStmt);
                            }

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $className  = (string) $stmt->name;
                    $isListener = $this->isListenerClass($stmt);
                    $foundClass = true;

                    if ($isListener) {
                        $isQueued                                                            = $this->implementsShouldQueue($stmt);
                        $constructorDeps                                                     = $this->getConstructorDependencies($stmt);
                        $hasHandleMethod                                                     = $this->classHasMethod($stmt, 'handle');
                        [$listensToEvents, $hasMultiHandleMethods, $multiHandleMethodsCount] = $this->getHandledEventsAndMethods($stmt);
                        [$tries, $timeout, $maxExceptions]                                   = $this->getListenerQueueProperties($stmt);
                    }

                    break;
                }
            }

        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        if ( ! $foundClass) {
            return ['type' => 'php_class', 'is_listener' => false, 'error' => 'Arquivo PHP na pasta Listeners não contém uma declaração de classe.'];
        }

        if ( ! $isListener) {
            return ['type' => 'php_class', 'is_listener' => false];
        }

        $summary = [
            'type'                     => 'listener',
            'is_listener'              => true,
            'name'                     => $className,
            'listens_to_events'        => array_unique($listensToEvents),
            'is_queued'                => $isQueued,
            'constructor_deps'         => $constructorDeps,
            'has_handle_method'        => $hasHandleMethod,
            'has_multi_handle_methods' => $hasMultiHandleMethods,
        ];

        if ($hasMultiHandleMethods) {
            $summary['multi_handle_methods_count'] = $multiHandleMethodsCount;
        }

        if ($isQueued) {
            if ($tries !== null) {
                $summary['tries'] = $tries;
            }

            if ($timeout !== null) {
                $summary['timeout'] = $timeout;
            }

            if ($maxExceptions !== null) {
                $summary['max_exceptions'] = $maxExceptions;
            }
        }

        return $summary;
    }

    /**
     * Verifica se a classe é um Listener, procurando por métodos 'handle' ou 'handle*'.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function isListenerClass(Class_ $classNode): bool
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && (
                $stmt->name->name === 'handle' || Str::startsWith($stmt->name->name, 'handle')
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se a classe implementa a interface ShouldQueue.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function implementsShouldQueue(Class_ $classNode): bool
    {
        foreach ($classNode->implements as $interface) {
            if ((string) $interface === 'Illuminate\Contracts\Queue\ShouldQueue') {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtém as dependências injetadas no construtor da classe.
     *
     * @param Class_ $classNode
     * @return array<string>
     */
    private function getConstructorDependencies(Class_ $classNode): array
    {
        $deps = [];

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === '__construct') {
                foreach ($stmt->params as $param) {
                    if ($param->type) {
                        $deps[] = $this->formatType($param->type);
                    }
                }

                break;
            }
        }

        return $deps;
    }

    /**
     * Identifica os eventos tratados e os métodos 'handle' ou 'handle*' na classe.
     *
     * @param Class_ $classNode
     * @return array{0: array<string>, 1: bool, 2: int}
     */
    private function getHandledEventsAndMethods(Class_ $classNode): array
    {
        $listensToEvents    = [];
        $multiHandleMethods = [];

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->isPublic()) {
                if ($stmt->name->name === 'handle') {
                    if (isset($stmt->params[0]) && $stmt->params[0]->type) {
                        $listensToEvents[] = $this->formatType($stmt->params[0]->type);
                    }
                } elseif (Str::startsWith($stmt->name->name, 'handle') && mb_strlen($stmt->name->name) > 6) { // handleSomething
                    $multiHandleMethods[] = $stmt->name->name;

                    if (isset($stmt->params[0]) && $stmt->params[0]->type) {
                        $listensToEvents[] = $this->formatType($stmt->params[0]->type);
                    }
                }
            }
        }

        $hasMultiHandleMethods   = count($multiHandleMethods) > 0;
        $multiHandleMethodsCount = count($multiHandleMethods);

        return [$listensToEvents, $hasMultiHandleMethods, $multiHandleMethodsCount];
    }

    /**
     * Extrai as propriedades de fila (tries, timeout, maxExceptions) do Listener.
     *
     * @param Class_ $classNode
     * @return array{0: int|null, 1: int|null, 2: int|null}
     */
    private function getListenerQueueProperties(Class_ $classNode): array
    {
        $tries         = null;
        $timeout       = null;
        $maxExceptions = null;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $prop) {
                    $default = $prop->default;

                    $propName = $prop->name->name;

                    if ($default instanceof LNumber) {
                        if ($propName === 'tries') {
                            $tries = $default->value;
                        } elseif ($propName === 'timeout') {
                            $timeout = $default->value;
                        } elseif ($propName === 'maxExceptions') {
                            $maxExceptions = $default->value;
                        }
                    }

                }
            }
        }

        return [$tries, $timeout, $maxExceptions];
    }
}
