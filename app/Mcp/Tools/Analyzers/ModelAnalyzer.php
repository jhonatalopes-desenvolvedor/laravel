<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class ModelAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa um arquivo de Modelo Eloquent.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isEloquentModel   = false;
        $tableName         = null;
        $fillableCount     = 0;
        $guardedCount      = 0;
        $castsCount        = 0;
        $relationshipCount = 0;
        $traitCount        = 0;
        $scopeCount        = 0;
        $foundClass        = false;
        $className         = null;

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
                            $className         = (string) $subStmt->name;
                            $isEloquentModel   = $this->isEloquentModelClass($subStmt);
                            $tableName         = $this->getModelTableName($subStmt, $className);
                            $fillableCount     = $this->getModelPropertyArrayCount($subStmt, 'fillable');
                            $guardedCount      = $this->getModelPropertyArrayCount($subStmt, 'guarded');
                            $castsCount        = $this->getModelPropertyArrayCount($subStmt, 'casts');
                            $relationshipCount = $this->getModelRelationshipCount($subStmt);
                            $traitCount        = count($this->getClassUsedTraits($subStmt));
                            $scopeCount        = $this->getModelScopeCount($subStmt);
                            $foundClass        = true;

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $className         = (string) $stmt->name;
                    $isEloquentModel   = $this->isEloquentModelClass($stmt);
                    $tableName         = $this->getModelTableName($stmt, $className);
                    $fillableCount     = $this->getModelPropertyArrayCount($stmt, 'fillable');
                    $guardedCount      = $this->getModelPropertyArrayCount($stmt, 'guarded');
                    $castsCount        = $this->getModelPropertyArrayCount($stmt, 'casts');
                    $relationshipCount = $this->getModelRelationshipCount($stmt);
                    $traitCount        = count($this->getClassUsedTraits($stmt));
                    $scopeCount        = $this->getModelScopeCount($stmt);
                    $foundClass        = true;

                    break;
                }
            }

        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        if ( ! $foundClass) {
            return ['type' => 'php_class', 'is_eloquent_model' => false, 'error' => 'Arquivo PHP na pasta Models não contém uma declaração de classe.'];
        }

        if ( ! $isEloquentModel) {
            return ['type' => 'php_class', 'is_eloquent_model' => false];
        }

        return [
            'type'               => 'eloquent_model',
            'is_eloquent_model'  => true,
            'table_name'         => $tableName,
            'fillable_count'     => $fillableCount,
            'guarded_count'      => $guardedCount,
            'casts_count'        => $castsCount,
            'relationship_count' => $relationshipCount,
            'trait_count'        => $traitCount,
            'scope_count'        => $scopeCount,
        ];
    }

    /**
     * Verifica se a classe é um Modelo Eloquent.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function isEloquentModelClass(Class_ $classNode): bool
    {
        if ( ! ($classNode->extends instanceof Name)) {
            return false;
        }
        $extendedClassName = (string) $classNode->extends;

        return in_array($extendedClassName, [
            'Illuminate\Database\Eloquent\Model',
            'Illuminate\Foundation\Auth\User',
            'App\Models\BaseModel',
        ]);
    }

    /**
     * Obtém o nome da tabela do modelo.
     *
     * @param Class_ $classNode
     * @param string $className
     * @return string
     */
    private function getModelTableName(Class_ $classNode, string $className): string
    {
        $tableName = $this->getClassPropertyStringValue($classNode, 'table');

        if ($tableName !== null) {
            return $tableName;
        }

        return Str::snake(Str::plural($className));
    }

    /**
     * Conta a quantidade de elementos em uma propriedade de array do modelo (fillable, guarded, casts).
     *
     * @param Class_ $classNode
     * @param string $propertyName
     * @return int
     */
    private function getModelPropertyArrayCount(Class_ $classNode, string $propertyName): int
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $prop) {
                    if ($prop->name->name === $propertyName && $prop->default instanceof Array_) {
                        return count($prop->default->items);
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Conta o número de métodos de relacionamento definidos no modelo.
     *
     * @param Class_ $classNode
     * @return int
     */
    private function getModelRelationshipCount(Class_ $classNode): int
    {
        $count = 0;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $this->isRelationshipMethod($stmt)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Conta o número de métodos de escopo local definidos no modelo.
     *
     * @param Class_ $classNode
     * @return int
     */
    private function getModelScopeCount(Class_ $classNode): int
    {
        $count = 0;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->isPublic() && Str::startsWith($stmt->name->name, 'scope') && mb_strlen($stmt->name->name) > 5) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Verifica se um método de classe é um método de relacionamento Eloquent.
     *
     * @param ClassMethod $methodNode
     * @return bool
     */
    private function isRelationshipMethod(ClassMethod $methodNode): bool
    {
        if ($methodNode->returnType === null) {
            return false;
        }

        $returnType = $this->formatType($methodNode->returnType);

        $relationshipTypes = [
            'Illuminate\Database\Eloquent\Relations\HasOne',
            'Illuminate\Database\Eloquent\Relations\HasMany',
            'Illuminate\Database\Eloquent\Relations\BelongsTo',
            'Illuminate\Database\Eloquent\Relations\BelongsToMany',
            'Illuminate\Database\Eloquent\Relations\MorphTo',
            'Illuminate\Database\Eloquent\Relations\MorphOne',
            'Illuminate\Database\Eloquent\Relations\MorphMany',
            'Illuminate\Database\Eloquent\Relations\MorphToMany',
            'Illuminate\Database\Eloquent\Relations\HasOneThrough',
            'Illuminate\Database\Eloquent\Relations\HasManyThrough',
        ];

        return in_array($returnType, $relationshipTypes);
    }
}
