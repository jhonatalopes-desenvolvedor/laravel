<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class EnumAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa o arquivo PHP e retorna informações sobre o enum, seus casos e métodos.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $caseCount   = 0;
        $methodCount = 0;
        $isBacked    = false;
        $backedType  = null;
        $foundEnum   = false;

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
                        if ($subStmt instanceof Enum_) {
                            [$caseCount, $methodCount, $isBacked, $backedType] = $this->countEnumElements($subStmt);
                            $foundEnum                                         = true;

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Enum_) {
                    [$caseCount, $methodCount, $isBacked, $backedType] = $this->countEnumElements($stmt);
                    $foundEnum                                         = true;

                    break;
                }
            }

        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        if ( ! $foundEnum) {
            return ['error' => 'Arquivo PHP na pasta Enums não contém uma declaração de enum.'];
        }

        $summary = [
            'type'         => 'enum',
            'is_backed'    => $isBacked,
            'case_count'   => $caseCount,
            'method_count' => $methodCount,
        ];

        if ($isBacked && $backedType) {
            $summary['backed_type'] = $backedType;
        }

        return $summary;
    }

    /**
     * Conta o número de casos e métodos de um enum e identifica se é um enum com valor associado (backed).
     *
     * @param Enum_ $enumNode
     * @return array{0: int, 1: int, 2: bool, 3: string|null}
     */
    private function countEnumElements(Enum_ $enumNode): array
    {
        $caseCount   = 0;
        $methodCount = 0;
        $isBacked    = ($enumNode->scalarType !== null);
        $backingType = null;

        if ($isBacked) {
            $backingType = $this->formatType($enumNode->scalarType);
        }

        foreach ($enumNode->stmts as $enumStmt) {
            if ($enumStmt instanceof EnumCase) {
                $caseCount++;
            } elseif ($enumStmt instanceof ClassMethod) {
                $methodCount++;
            }
        }

        return [$caseCount, $methodCount, $isBacked, $backingType];
    }
}
