<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class EventAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa o arquivo PHP e retorna informações sobre o evento, suas propriedades públicas, métodos de broadcast e dependências do construtor.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isEvent                = false;
        $publicPropertiesCount  = 0;
        $publicPropertiesTypes  = [];
        $isBroadcastable        = false;
        $hasBroadcastOnMethod   = false;
        $hasBroadcastWithMethod = false;
        $hasBroadcastAsMethod   = false;
        $constructorDeps        = [];
        $foundClass             = false;
        $className              = null;

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
                            $isEvent    = $this->isEventClass($subStmt);
                            $foundClass = true;

                            if ($isEvent) {
                                [$publicPropertiesCount, $publicPropertiesTypes] = $this->getPublicPropertiesData($subStmt);
                                $isBroadcastable                                 = $this->implementsShouldBroadcast($subStmt);
                                $hasBroadcastOnMethod                            = $this->classHasMethod($subStmt, 'broadcastOn');
                                $hasBroadcastWithMethod                          = $this->classHasMethod($subStmt, 'broadcastWith');
                                $hasBroadcastAsMethod                            = $this->classHasMethod($subStmt, 'broadcastAs');
                                $constructorDeps                                 = $this->getConstructorDependencies($subStmt);
                            }

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $className  = (string) $stmt->name;
                    $isEvent    = $this->isEventClass($stmt);
                    $foundClass = true;

                    if ($isEvent) {
                        [$publicPropertiesCount, $publicPropertiesTypes] = $this->getPublicPropertiesData($stmt);
                        $isBroadcastable                                 = $this->implementsShouldBroadcast($stmt);
                        $hasBroadcastOnMethod                            = $this->classHasMethod($stmt, 'broadcastOn');
                        $hasBroadcastWithMethod                          = $this->classHasMethod($stmt, 'broadcastWith');
                        $hasBroadcastAsMethod                            = $this->classHasMethod($stmt, 'broadcastAs');
                        $constructorDeps                                 = $this->getConstructorDependencies($stmt);
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
            return ['type' => 'php_class', 'is_event' => false, 'error' => 'Arquivo PHP na pasta Events não contém uma declaração de classe.'];
        }

        if ( ! $isEvent) {
            return ['type' => 'php_class', 'is_event' => false];
        }

        return [
            'type'                      => 'event',
            'is_event'                  => true,
            'name'                      => $className,
            'public_properties_count'   => $publicPropertiesCount,
            'public_properties_types'   => array_unique($publicPropertiesTypes),
            'is_broadcastable'          => $isBroadcastable,
            'has_broadcast_on_method'   => $hasBroadcastOnMethod,
            'has_broadcast_with_method' => $hasBroadcastWithMethod,
            'has_broadcast_as_method'   => $hasBroadcastAsMethod,
            'constructor_deps'          => $constructorDeps,
        ];
    }

    /**
     * Determina se a classe é um evento observável pelo sistema de broadcast do Laravel.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function isEventClass(Class_ $classNode): bool
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property && $stmt->isPublic()) {
                return true;
            }

            if ($stmt instanceof ClassMethod && $stmt->name->name === '__construct') {
                foreach ($stmt->params as $param) {
                    if ($param->flags & Class_::MODIFIER_PUBLIC) {
                        return true;
                    }
                }
            }

            if ($stmt instanceof TraitUse) {
                foreach ($stmt->traits as $trait) {
                    if ((string) $trait === 'Illuminate\Foundation\Events\Dispatchable') {
                        return true;
                    }
                }
            }

            if ($stmt instanceof ClassMethod && $stmt->name->name === 'broadcastOn') {
                return true;
            }
        }

        foreach ($classNode->implements as $interface) {
            if ((string) $interface === 'Illuminate\Contracts\Broadcasting\ShouldBroadcast') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna a quantidade e os tipos das propriedades públicas da classe.
     *
     * @param Class_ $classNode
     * @return array{0: int, 1: string[]}
     */
    private function getPublicPropertiesData(Class_ $classNode): array
    {
        $count = 0;
        $types = [];

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property && $stmt->isPublic()) {
                $count += count($stmt->props);

                if ($stmt->type) {
                    $types[] = $this->formatType($stmt->type);
                }
            }

            if ($stmt instanceof ClassMethod && $stmt->name->name === '__construct') {
                foreach ($stmt->params as $param) {
                    if ($param->flags & Class_::MODIFIER_PUBLIC) {
                        $count++;

                        if ($param->type) {
                            $types[] = $this->formatType($param->type);
                        }
                    }
                }
            }
        }

        return [$count, $types];
    }

    /**
     * Verifica se a classe implementa a interface ShouldBroadcast.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function implementsShouldBroadcast(Class_ $classNode): bool
    {
        foreach ($classNode->implements as $interface) {
            if ((string) $interface === 'Illuminate\Contracts\Broadcasting\ShouldBroadcast') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna os tipos das dependências injetadas no construtor da classe.
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
}
