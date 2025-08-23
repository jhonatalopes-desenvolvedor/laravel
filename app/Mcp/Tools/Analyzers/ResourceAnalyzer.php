<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class ResourceAnalyzer extends PhpFileAnalyzer
{
    /**
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isResource            = false;
        $resourceType          = null;
        $attributeCount        = 0;
        $includedRelations     = [];
        $usesConditionals      = false;
        $additionalMethodCount = 0;
        $wrapsResource         = null;
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
                            $className  = $subStmt->name?->name;
                            $isResource = $this->isResourceClass($subStmt);
                            $foundClass = true;

                            if ($isResource) {
                                $resourceType = $this->getResourceType($subStmt);

                                if ($resourceType === 'single_resource') {
                                    [$attributeCount, $includedRelations, $usesConditionals] = $this->analyzeSingleResource($subStmt);
                                } elseif ($resourceType === 'collection_resource') {
                                    $wrapsResource = $this->getCollectionWrapsResource($subStmt);
                                }

                                $additionalMethodCount = $this->countAdditionalMethods($subStmt);
                            }

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $className  = $stmt->name?->name;
                    $isResource = $this->isResourceClass($stmt);
                    $foundClass = true;

                    if ($isResource) {
                        $resourceType = $this->getResourceType($stmt);

                        if ($resourceType === 'single_resource') {
                            [$attributeCount, $includedRelations, $usesConditionals] = $this->analyzeSingleResource($stmt);
                        } elseif ($resourceType === 'collection_resource') {
                            $wrapsResource = $this->getCollectionWrapsResource($stmt);
                        }

                        $additionalMethodCount = $this->countAdditionalMethods($stmt);
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
            return ['type' => 'php_class', 'is_api_resource' => false, 'error' => 'Arquivo PHP na pasta Resources não contém uma declaração de classe.'];
        }

        if ( ! $isResource) {
            return ['type' => 'php_class', 'is_api_resource' => false];
        }

        $summary = [
            'type'                    => 'api_resource',
            'is_api_resource'         => true,
            'name'                    => $className,
            'resource_type'           => $resourceType,
            'additional_method_count' => $additionalMethodCount,
        ];

        if ($resourceType === 'single_resource') {
            $summary['attribute_count']    = $attributeCount;
            $summary['included_relations'] = $includedRelations;
            $summary['uses_conditionals']  = $usesConditionals;
        } elseif ($resourceType === 'collection_resource' && $wrapsResource) {
            $summary['wraps_resource'] = $wrapsResource;
        }

        return $summary;
    }

    /**
     * @param Class_ $classNode
     * @return bool
     */
    private function isResourceClass(Class_ $classNode): bool
    {
        if ( ! ($classNode->extends instanceof Name)) {
            return false;
        }
        $extendedClassName = (string) $classNode->extends;

        return in_array($extendedClassName, [
            'Illuminate\Http\Resources\Json\JsonResource',
            'Illuminate\Http\Resources\Json\ResourceCollection',
        ], true);
    }

    /**
     * @param Class_ $classNode
     * @return string|null
     */
    private function getResourceType(Class_ $classNode): ?string
    {
        if ( ! ($classNode->extends instanceof Name)) {
            return null;
        }
        $extendedClassName = (string) $classNode->extends;

        return match ($extendedClassName) {
            'Illuminate\Http\Resources\Json\JsonResource'       => 'single_resource',
            'Illuminate\Http\Resources\Json\ResourceCollection' => 'collection_resource',
            default                                             => null,
        };
    }

    /**
     * @param Class_ $classNode
     * @return array{0: int, 1: array<int, string>, 2: bool}
     */
    private function analyzeSingleResource(Class_ $classNode): array
    {
        $attributeCount    = 0;
        $includedRelations = [];
        $usesConditionals  = false;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === 'toArray') {
                if ($stmt->stmts !== null) {
                    foreach ($stmt->stmts as $subStmt) {
                        if ($subStmt instanceof Return_ && $subStmt->expr instanceof Array_) {
                            $items          = $subStmt->expr->items ?? [];
                            $attributeCount = count($items);

                            foreach ($items as $item) {
                                if ($item->value instanceof MethodCall) {
                                    $methodCall = $item->value;

                                    if ($methodCall->name instanceof Identifier && ($methodCall->name->name === 'when' || $methodCall->name->name === 'whenLoaded')) {
                                        $usesConditionals = true;

                                        if (isset($methodCall->args[1]) && $methodCall->args[1]->value instanceof New_) {
                                            $newExpr = $methodCall->args[1]->value;

                                            if ($newExpr->class instanceof Name) {
                                                $includedRelations[] = (string) $newExpr->class;
                                            }
                                        }
                                    }
                                } elseif ($item->value instanceof New_) {
                                    if ($item->value->class instanceof Name) {
                                        $includedRelations[] = (string) $item->value->class;
                                    }
                                }
                            }

                            break;
                        }
                    }
                }
            }
        }

        return [$attributeCount, array_unique($includedRelations), $usesConditionals];
    }

    /**
     * @param Class_ $classNode
     * @return string|null
     */
    private function getCollectionWrapsResource(Class_ $classNode): ?string
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Stmt\Property) {
                foreach ($stmt->props as $prop) {
                    if ($prop->name->name === 'collects') {
                        if ($prop->default instanceof Node\Scalar\String_) {
                            return $prop->default->value;
                        }

                        if ($prop->default instanceof New_ && $prop->default->class instanceof Name) {
                            return (string) $prop->default->class;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param Class_ $classNode
     * @return int
     */
    private function countAdditionalMethods(Class_ $classNode): int
    {
        $count           = 0;
        $excludedMethods = ['toArray', '__construct', '__invoke'];

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->isPublic() && ! in_array($stmt->name->name, $excludedMethods, true)) {
                $count++;
            }
        }

        return $count;
    }
}
