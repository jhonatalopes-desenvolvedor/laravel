<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class ControllerAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa o arquivo PHP e identifica se é um Controller, retornando suas características.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isController           = false;
        $methodCount            = 0;
        $resourceControllerType = null;
        $middlewareCount        = 0;
        $usesFormRequests       = false;
        $foundClass             = false;

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
                            $isController           = $this->isControllerClass($subStmt);
                            $methodCount            = $this->getControllerMethodCount($subStmt);
                            $resourceControllerType = $this->getControllerResourceMethodType($subStmt);
                            $middlewareCount        = $this->getControllerMiddlewareCount($subStmt);
                            $usesFormRequests       = $this->controllerUsesFormRequests($subStmt);
                            $foundClass             = true;

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $isController           = $this->isControllerClass($stmt);
                    $methodCount            = $this->getControllerMethodCount($stmt);
                    $resourceControllerType = $this->getControllerResourceMethodType($stmt);
                    $middlewareCount        = $this->getControllerMiddlewareCount($stmt);
                    $usesFormRequests       = $this->controllerUsesFormRequests($stmt);
                    $foundClass             = true;

                    break;
                }
            }
        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        if ( ! $foundClass) {
            return ['type' => 'php_class', 'is_controller' => false, 'error' => 'Arquivo PHP na pasta Controllers não contém uma declaração de classe.'];
        }

        if ( ! $isController) {
            return ['type' => 'php_class', 'is_controller' => false];
        }

        return [
            'type'                     => 'controller',
            'is_controller'            => true,
            'method_count'             => $methodCount,
            'resource_controller_type' => $resourceControllerType,
            'middleware_count'         => $middlewareCount,
            'uses_form_requests'       => $usesFormRequests,
        ];
    }

    /**
     * Verifica se a classe estende uma classe base de Controllers do Laravel.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function isControllerClass(Class_ $classNode): bool
    {
        if ( ! ($classNode->extends instanceof Name)) {
            return false;
        }
        $extendedClassName = (string) $classNode->extends;

        return in_array($extendedClassName, [
            'Illuminate\Routing\Controller',
            'App\Http\Controllers\Controller',
        ]);
    }

    /**
     * Conta o número de métodos definidos no Controller.
     *
     * @param Class_ $classNode
     * @return int
     */
    private function getControllerMethodCount(Class_ $classNode): int
    {
        $count = 0;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Identifica se o Controller implementa todos ou parte dos métodos de um Resource Controller.
     *
     * @param Class_ $classNode
     * @return string|null
     */
    private function getControllerResourceMethodType(Class_ $classNode): ?string
    {
        $resourceMethods      = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $foundResourceMethods = [];

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->isPublic() && in_array($stmt->name->name, $resourceMethods)) {
                $foundResourceMethods[$stmt->name->name] = true;
            }
        }

        if (count($foundResourceMethods) === count($resourceMethods)) {
            return 'full_resource';
        } elseif (count($foundResourceMethods) > 0) {
            return 'partial_resource';
        }

        return null;
    }

    /**
     * Conta o número de middlewares aplicados ao Controller.
     *
     * @param Class_ $classNode
     * @return int
     */
    private function getControllerMiddlewareCount(Class_ $classNode): int
    {
        $count = 0;

        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $prop) {
                    if ($prop->name->name === 'middleware') {
                        $count += $this->extractMiddlewareCount($prop->default);
                    }
                }
            } elseif ($stmt instanceof ClassMethod && $stmt->name->name === '__construct') {
                $count += $this->extractMiddlewareCountFromConstructor($stmt);
            }
        }

        return $count;
    }

    /**
     * Verifica se o Controller utiliza Form Requests nos métodos públicos.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function controllerUsesFormRequests(Class_ $classNode): bool
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->isPublic()) {
                foreach ($stmt->params as $param) {
                    if ($param->type instanceof Name || $param->type instanceof Identifier) {
                        $paramType = (string) $param->type;

                        $formRequestAnalyzer = new FormRequestAnalyzer();

                        if ($formRequestAnalyzer->isFormRequestClass($paramType)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Conta a quantidade de middlewares definidos diretamente na propriedade.
     *
     * @param Node\Expr|null $defaultExpr
     * @return int
     */
    private function extractMiddlewareCount(?Node\Expr $defaultExpr): int
    {
        if ($defaultExpr instanceof Array_) {
            return count($defaultExpr->items);
        } elseif ($defaultExpr instanceof String_) {
            return 1;
        }

        return 0;
    }

    /**
     * Conta a quantidade de middlewares aplicados dentro do construtor.
     *
     * @param ClassMethod $constructorNode
     * @return int
     */
    private function extractMiddlewareCountFromConstructor(ClassMethod $constructorNode): int
    {
        $count = 0;

        if ($constructorNode->stmts !== null) {
            foreach ($constructorNode->stmts as $stmt) {
                if ( ! ($stmt instanceof Expression)) {
                    continue;
                }

                $expr = $stmt->expr;

                if ( ! ($expr instanceof MethodCall)) {
                    continue;
                }

                if ($expr->var instanceof Variable && (string) $expr->var->name === 'this') {
                    if ($expr->name instanceof Identifier && (string) $expr->name === 'middleware') {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}
