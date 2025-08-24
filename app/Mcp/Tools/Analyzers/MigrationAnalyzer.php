<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class MigrationAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa um arquivo de Migração do Laravel.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $summary = [
            'type'               => 'migration',
            'operation'          => 'unknown',
            'table_name'         => null,
            'columns_in_up'      => 0,
            'foreign_keys_in_up' => 0,
            'indexes_in_up'      => 0,
            'rollback_action'    => 'unknown',
            'is_anonymous'       => false,
        ];

        try {
            $parser = $this->parserFactory->createForNewestSupportedVersion();
            $code   = File::get($filePath);
            $stmts  = $parser->parse($code);

            if ( ! $stmts) {
                return ['error' => 'Não foi possível analisar o arquivo PHP.'];
            }

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new NameResolver());
            $stmts = $traverser->traverse($stmts);

            $classNode = $this->findMigrationClassNode($stmts);

            if ($classNode) {
                $summary['is_anonymous'] = $classNode->isAnonymous();

                foreach ($classNode->stmts as $stmt) {
                    if ( ! ($stmt instanceof ClassMethod)) {
                        continue;
                    }

                    if ($stmt->name->name === 'up') {
                        $this->analyzeSchemaMethod($stmt, $summary, 'up');
                    } elseif ($stmt->name->name === 'down') {
                        $this->analyzeSchemaMethod($stmt, $summary, 'down');
                    }
                }
            } else {
                return ['error' => 'Nenhuma classe de Migration encontrada no arquivo.'];
            }

        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        return $summary;
    }

    /**
     * Encontra o nó da classe de Migração no array de nós do parser.
     *
     * @param array<Node> $nodes
     * @return Class_|null
     */
    private function findMigrationClassNode(array $nodes): ?Class_
    {
        foreach ($nodes as $node) {
            if ($node instanceof Stmt\Namespace_) {
                $classNode = $this->findMigrationClassNode($node->stmts);

                if ($classNode !== null) {
                    return $classNode;
                }
            }

            if ($node instanceof Class_ && $node->extends instanceof Node\Name) {
                if ((string) $node->extends === 'Illuminate\Database\Migrations\Migration') {
                    return $node;
                }
            }

            if ($node instanceof Stmt\Return_ && $node->expr instanceof Node\Expr\New_) {
                /** @var Node\Expr\New_ $newExpr */
                $newExpr = $node->expr;

                if ($newExpr->class instanceof Class_) {
                    /** @var Class_ $newClass */
                    $newClass = $newExpr->class;

                    if ($newClass->extends instanceof Node\Name && (string) $newClass->extends === 'Illuminate\Database\Migrations\Migration') {
                        return $newClass;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Analisa os métodos 'up' e 'down' da Migração para extrair informações do esquema.
     *
     * @param ClassMethod $methodNode
     * @param array<string, mixed> $summary
     * @param string $direction 'up' or 'down'
     */
    private function analyzeSchemaMethod(ClassMethod $methodNode, array &$summary, string $direction): void
    {
        if ($methodNode->stmts === null) {
            return;
        }

        foreach ($methodNode->stmts as $stmt) {
            if ( ! ($stmt instanceof Stmt\Expression && ($stmt->expr instanceof StaticCall || $stmt->expr instanceof MethodCall))) {
                continue;
            }

            $expr = $stmt->expr;

            if ($expr instanceof StaticCall) {
                /** @var StaticCall $expr */
                $exprClass = $expr->class;

                /** @var Node\Identifier $exprName */
                $exprName = $expr->name;

                $className = (string) $exprClass;
                $callName  = (string) $exprName;

                if ($className === 'Schema' || $className === 'Illuminate\Support\Facades\Schema') {
                    if ($direction === 'up') {
                        $summary['operation'] = match ($callName) {
                            'create' => 'create_table',
                            'table'  => 'modify_table',
                            'drop', 'dropIfExists' => 'drop_table',
                            default => 'unknown',
                        };

                        if (isset($expr->args[0]->value) && $expr->args[0]->value instanceof String_) {
                            /** @var String_ $arg0 */
                            $arg0                  = $expr->args[0]->value;
                            $summary['table_name'] = $arg0->value;
                        }
                    }

                    if ($direction === 'down') {
                        $summary['rollback_action'] = match ($callName) {
                            'drop', 'dropIfExists' => 'drop_table',
                            'table' => 'modify_table',
                            default => 'unknown',
                        };
                    }

                    if (isset($expr->args[1]->value) && $expr->args[1]->value instanceof Closure) {
                        /** @var Closure $closure */
                        $closure = $expr->args[1]->value;

                        /** @var Stmt[] $closureStmts */
                        $closureStmts = $closure->stmts ?? [];

                        if ($direction === 'up') {
                            $this->countOperationsInClosure($closureStmts, $summary);
                        }
                    }
                }
            }
        }
    }

    /**
     * Conta operações de esquema dentro de uma closure (métodos de coluna, chaves estrangeiras, índices).
     *
     * @param array<Stmt> $stmts
     * @param array<string, mixed> $summary
     */
    private function countOperationsInClosure(array $stmts, array &$summary): void
    {
        foreach ($stmts as $stmt) {
            if ( ! ($stmt instanceof Stmt\Expression && $stmt->expr instanceof MethodCall)) {
                continue;
            }

            $summary['columns_in_up']++;

            $currentCall = $stmt->expr;

            while ($currentCall instanceof MethodCall) {
                $methodName = (string) $currentCall->name;

                if (in_array($methodName, ['foreign', 'constrained'], true)) {
                    $summary['foreign_keys_in_up']++;

                    break;
                }

                if (in_array($methodName, ['index', 'unique', 'primary', 'spatialIndex'], true)) {
                    $summary['indexes_in_up']++;
                }

                $currentCall = $currentCall->var;
            }
        }
    }
}
