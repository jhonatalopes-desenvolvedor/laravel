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

class JobAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa o arquivo PHP e retorna informações sobre um Job, incluindo métodos, propriedades, interfaces implementadas e configurações de fila.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isJob                 = false;
        $implements            = [];
        $constructorDeps       = [];
        $publicPropertiesCount = 0;
        $hasHandleMethod       = false;
        $hasFailedMethod       = false;
        $hasDisplayNameMethod  = false;
        $hasTagsMethod         = false;
        $tries                 = null;
        $timeout               = null;
        $maxExceptions         = null;
        $foundClass            = false;
        $className             = null;

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
                            $isJob      = $this->isJobClass($subStmt);
                            $foundClass = true;

                            if ($isJob) {
                                $implements                        = $this->getClassImplementedInterfaces($subStmt);
                                $constructorDeps                   = $this->getConstructorDependencies($subStmt);
                                $publicPropertiesCount             = $this->getPublicPropertiesCount($subStmt);
                                $hasHandleMethod                   = $this->classHasMethod($subStmt, 'handle');
                                $hasFailedMethod                   = $this->classHasMethod($subStmt, 'failed');
                                $hasDisplayNameMethod              = $this->classHasMethod($subStmt, 'displayName');
                                $hasTagsMethod                     = $this->classHasMethod($subStmt, 'tags');
                                [$tries, $timeout, $maxExceptions] = $this->getJobProperties($subStmt);
                            }

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $className  = (string) $stmt->name;
                    $isJob      = $this->isJobClass($stmt);
                    $foundClass = true;

                    if ($isJob) {
                        $implements                        = $this->getClassImplementedInterfaces($stmt);
                        $constructorDeps                   = $this->getConstructorDependencies($stmt);
                        $publicPropertiesCount             = $this->getPublicPropertiesCount($stmt);
                        $hasHandleMethod                   = $this->classHasMethod($stmt, 'handle');
                        $hasFailedMethod                   = $this->classHasMethod($stmt, 'failed');
                        $hasDisplayNameMethod              = $this->classHasMethod($stmt, 'displayName');
                        $hasTagsMethod                     = $this->classHasMethod($stmt, 'tags');
                        [$tries, $timeout, $maxExceptions] = $this->getJobProperties($stmt);
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
            return ['type' => 'php_class', 'is_job' => false, 'error' => 'Arquivo PHP na pasta Jobs não contém uma declaração de classe.'];
        }

        if ( ! $isJob) {
            return ['type' => 'php_class', 'is_job' => false];
        }

        $summary = [
            'type'                    => 'job',
            'is_job'                  => true,
            'name'                    => $className,
            'implements'              => $implements,
            'constructor_deps'        => $constructorDeps,
            'public_properties_count' => $publicPropertiesCount,
            'has_handle_method'       => $hasHandleMethod,
            'has_failed_method'       => $hasFailedMethod,
            'has_display_name_method' => $hasDisplayNameMethod,
            'has_tags_method'         => $hasTagsMethod,
        ];

        if ($tries !== null) {
            $summary['tries'] = $tries;
        }

        if ($timeout !== null) {
            $summary['timeout'] = $timeout;
        }

        if ($maxExceptions !== null) {
            $summary['max_exceptions'] = $maxExceptions;
        }

        return $summary;
    }

    /**
     * Verifica se a classe implementa ShouldQueue, identificando-a como Job.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function isJobClass(Class_ $classNode): bool
    {
        foreach ($classNode->implements as $interface) {
            if ((string) $interface === 'Illuminate\Contracts\Queue\ShouldQueue') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna todas as interfaces implementadas pela classe.
     *
     * @param Class_ $classNode
     * @return array<string>
     */
    private function getClassImplementedInterfaces(Class_ $classNode): array
    {
        $interfaces = [];

        foreach ($classNode->implements as $interface) {
            $interfaces[] = (string) $interface;
        }

        return $interfaces;
    }

    /**
     * Retorna os tipos das dependências do construtor da classe.
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
     * Conta a quantidade de propriedades públicas da classe.
     *
     * @param Class_ $classNode
     * @return int
     */
    private function getPublicPropertiesCount(Class_ $classNode): int
    {
        $count = 0;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property && $stmt->isPublic()) {
                $count += count($stmt->props);
            }
        }

        return $count;
    }

    /**
     * Retorna valores padrão de propriedades específicas do Job (tries, timeout, maxExceptions).
     *
     * @param Class_ $classNode
     * @return array{0: int|null, 1: int|null, 2: int|null}
     */
    private function getJobProperties(Class_ $classNode): array
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
