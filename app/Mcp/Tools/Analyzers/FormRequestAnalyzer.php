<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class FormRequestAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisa o arquivo PHP e retorna informações sobre a FormRequest, incluindo quantidade de regras, métodos customizados e tipo de autorização.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isFormRequest          = false;
        $ruleCount              = 0;
        $hasCustomMessages      = false;
        $hasCustomAttributes    = false;
        $authorizationLogicType = 'not_implemented';
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
                            $isFormRequest          = $this->isFormRequestClass((string) $subStmt->extends);
                            $ruleCount              = $this->getFormRequestRuleCount($subStmt);
                            $hasCustomMessages      = $this->formRequestHasCustomMethod($subStmt, 'messages');
                            $hasCustomAttributes    = $this->formRequestHasCustomMethod($subStmt, 'attributes');
                            $authorizationLogicType = $this->getFormRequestAuthorizationLogicType($subStmt);
                            $foundClass             = true;

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $isFormRequest          = $this->isFormRequestClass((string) $stmt->extends);
                    $ruleCount              = $this->getFormRequestRuleCount($stmt);
                    $hasCustomMessages      = $this->formRequestHasCustomMethod($stmt, 'messages');
                    $hasCustomAttributes    = $this->formRequestHasCustomMethod($stmt, 'attributes');
                    $authorizationLogicType = $this->getFormRequestAuthorizationLogicType($stmt);
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
            return ['type' => 'php_class', 'is_form_request' => false, 'error' => 'Arquivo PHP na pasta Requests não contém uma declaração de classe.'];
        }

        if ( ! $isFormRequest) {
            return ['type' => 'php_class', 'is_form_request' => false];
        }

        return [
            'type'                  => 'form_request',
            'is_form_request'       => true,
            'rule_count'            => $ruleCount,
            'has_custom_messages'   => $hasCustomMessages,
            'has_custom_attributes' => $hasCustomAttributes,
            'authorization_logic'   => $authorizationLogicType,
        ];
    }

    /**
     * Verifica se a classe estende uma FormRequest base do Laravel ou customizada.
     *
     * @param string $extendedClassName
     * @return bool
     */
    public function isFormRequestClass(string $extendedClassName): bool
    {
        $baseFormRequests = [
            'Illuminate\Foundation\Http\FormRequest',
            'App\Http\Requests\BaseFormRequest',
        ];

        foreach ($baseFormRequests as $base) {
            if ($extendedClassName === $base || is_subclass_of($extendedClassName, $base)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Conta a quantidade de regras definidas no método rules().
     *
     * @param Class_ $classNode
     * @return int
     */
    private function getFormRequestRuleCount(Class_ $classNode): int
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === 'rules') {
                if ($stmt->stmts !== null) {
                    foreach ($stmt->stmts as $subStmt) {
                        if ($subStmt instanceof Return_ && $subStmt->expr instanceof Array_) {
                            return count($subStmt->expr->items);
                        }
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Verifica se a FormRequest implementa métodos customizados (messages ou attributes).
     *
     * @param Class_ $classNode
     * @param string $methodName
     * @return bool
     */
    private function formRequestHasCustomMethod(Class_ $classNode, string $methodName): bool
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === $methodName) {
                if ($stmt->stmts !== null && count($stmt->stmts) > 0) {
                    foreach ($stmt->stmts as $subStmt) {
                        if ($subStmt instanceof Return_ && $subStmt->expr instanceof Array_ && empty($subStmt->expr->items)) {
                            continue;
                        }

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Analisa o tipo de lógica de autorização definida no método authorize().
     *
     * @param Class_ $classNode
     * @return string
     */
    private function getFormRequestAuthorizationLogicType(Class_ $classNode): string
    {
        foreach ($classNode->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === 'authorize') {
                if ($stmt->stmts === null || empty($stmt->stmts)) {
                    return 'not_implemented';
                }

                foreach ($stmt->stmts as $subStmt) {
                    if ($subStmt instanceof Return_) {
                        $expr = $subStmt->expr;

                        if ($expr instanceof \PhpParser\Node\Expr\ConstFetch) {
                            $name = mb_strtolower((string) $expr->name);

                            if ($name === 'true') {
                                return 'always_true';
                            }

                            if ($name === 'false') {
                                return 'always_false';
                            }
                        }

                        if ($expr instanceof MethodCall) {
                            $var = $expr->var;

                            if ($var instanceof MethodCall &&
                                $var->name instanceof Identifier && (string) $var->name === 'user' &&
                                $expr->name instanceof Identifier && (string) $expr->name === 'can') {
                                return 'policy_or_gate_check';
                            }
                        } elseif ($expr instanceof \PhpParser\Node\Expr\StaticCall) {
                            if ($expr->class instanceof Name && (string) $expr->class === 'Gate' &&
                                $expr->name instanceof Identifier && (string) $expr->name === 'allows') {
                                return 'policy_or_gate_check';
                            }
                        }

                        return 'custom_logic';
                    }
                }

                return 'custom_logic';
            }
        }

        return 'not_implemented';
    }
}
