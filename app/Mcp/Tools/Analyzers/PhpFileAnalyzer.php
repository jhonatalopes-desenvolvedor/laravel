<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\UnionType;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Throwable;

abstract class PhpFileAnalyzer
{
    /**
     * Summary of parserFactory
     *
     * @var ParserFactory
     */
    protected ParserFactory $parserFactory;

    /**
     * Fábrica para criar parsers do PHP-Parser.
     *
     * @var PrettyPrinter
     */
    protected PrettyPrinter $prettyPrinter;

    public function __construct()
    {
        $this->parserFactory = new ParserFactory();
        $this->prettyPrinter = new PrettyPrinter();
    }

    /**
     * Impressora padrão para formatar nós do AST.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    abstract public function analyze(string $filePath): array;

    /**
     * Retorna resumo geral da classe, interface ou trait contida no arquivo.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function getGeneralClassSummary(string $filePath): array
    {
        $summary = [
            'type'             => 'php_file',
            'found_definition' => false,
        ];

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
                        if ($subStmt instanceof Class_ || $subStmt instanceof Interface_ || $subStmt instanceof Trait_) {
                            $this->extractClassLikeElements($subStmt, $summary);
                            $summary['found_definition'] = true;

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_ || $stmt instanceof Interface_ || $stmt instanceof Trait_) {
                    $this->extractClassLikeElements($stmt, $summary);
                    $summary['found_definition'] = true;

                    break;
                }
            }

        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        if ( ! $summary['found_definition']) {
            return ['type' => 'php_file'];
        }

        unset($summary['found_definition']);

        return $summary;
    }

    /**
     * Extrai elementos de classes, interfaces ou traits.
     *
     * @param Class_|Interface_|Trait_ $node
     * @param array<string, mixed> $summary
     */
    protected function extractClassLikeElements(Stmt $node, array &$summary): void
    {
        $summary['name']             = (string) $node->name;
        $summary['extends']          = [];
        $summary['implements']       = [];
        $summary['uses_traits']      = [];
        $summary['method_count']     = 0;
        $summary['property_count']   = 0;
        $summary['constructor_deps'] = [];

        if ($node instanceof Class_) {
            $summary['type'] = 'class';

            if ($node->extends instanceof Name) {
                $summary['extends'][] = (string) $node->extends;
            }

            foreach ($node->implements as $interface) {
                $summary['implements'][] = (string) $interface;
            }
        } elseif ($node instanceof Interface_) {
            $summary['type'] = 'interface';

            foreach ($node->extends as $interface) {
                $summary['extends'][] = (string) $interface;
            }
        } elseif ($node instanceof Trait_) {
            $summary['type'] = 'trait';
        }

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof TraitUse) {
                foreach ($stmt->traits as $trait) {
                    $summary['uses_traits'][] = (string) $trait;
                }
            } elseif ($stmt instanceof ClassMethod) {
                $summary['method_count']++;

                if ($stmt->name->name === '__construct' && ! empty($stmt->params)) {
                    foreach ($stmt->params as $param) {
                        if ($param->type) {
                            $summary['constructor_deps'][] = $this->formatType($param->type);
                        }
                    }
                }
            } elseif ($stmt instanceof Property) {
                $summary['property_count'] += count($stmt->props);
            }
        }
    }

    /**
     * Formata o tipo de nó para string.
     *
     * @param Node|null $typeNode
     * @return string
     */
    protected function formatType(?Node $typeNode): string
    {
        if ($typeNode === null) {
            return '';
        }

        if ($typeNode instanceof NullableType) {
            return '?' . $this->formatType($typeNode->type);
        }

        if ($typeNode instanceof UnionType) {
            return implode('|', array_map(fn ($t) => $this->formatType($t), $typeNode->types));
        }

        if ($typeNode instanceof IntersectionType) {
            return implode('&', array_map(fn ($t) => $this->formatType($t), $typeNode->types));
        }

        if ($typeNode instanceof Name || $typeNode instanceof Identifier) {
            return (string) $typeNode;
        }

        return (string) $this->prettyPrinter->prettyPrint([$typeNode]);
    }

    /**
     * Verifica se a classe ou trait possui determinado método.
     *
     * @param Class_|Interface_|Trait_ $classLikeNode
     * @param string $methodName
     * @return bool
     */
    protected function classHasMethod(Stmt $classLikeNode, string $methodName): bool
    {
        foreach ($classLikeNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === $methodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna o valor string de uma propriedade de classe.
     *
     * @param Class_ $classNode
     * @param string $propertyName
     * @return string|null
     */
    protected function getClassPropertyStringValue(Class_ $classNode, string $propertyName): ?string
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $prop) {
                    if ($prop->name->name === $propertyName && $prop->default instanceof String_) {
                        return $prop->default->value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Retorna todos os traits usados por uma classe.
     *
     * @param Class_ $classNode
     * @return array<string>
     */
    protected function getClassUsedTraits(Class_ $classNode): array
    {
        $traits = [];

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof TraitUse) {
                foreach ($stmt->traits as $trait) {
                    $traits[] = (string) $trait;
                }
            }
        }

        return $traits;
    }
}
