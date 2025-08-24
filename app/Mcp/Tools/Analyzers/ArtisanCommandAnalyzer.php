<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Throwable;

class ArtisanCommandAnalyzer extends PhpFileAnalyzer
{
    /**
     * Analisar o arquivo PHP e identificar se é um comando Artisan, retornando seus detalhes.
     *
     * @param string $filePath
     * @return array<string, mixed>
     */
    public function analyze(string $filePath): array
    {
        $isArtisanCommand = false;
        $commandName      = null;
        $description      = null;
        $argumentCount    = 0;
        $optionCount      = 0;
        $hasHandleMethod  = false;
        $usesTraits       = [];
        $foundClass       = false;

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
                            $isArtisanCommand = $this->isArtisanCommandClass($subStmt);

                            if ($isArtisanCommand) {
                                $commandName     = $this->getClassPropertyStringValue($subStmt, 'signature');
                                $description     = $this->getClassPropertyStringValue($subStmt, 'description');
                                $argumentCount   = $this->parseSignatureForCount($commandName, 'arguments');
                                $optionCount     = $this->parseSignatureForCount($commandName, 'options');
                                $hasHandleMethod = $this->classHasMethod($subStmt, 'handle');
                                $usesTraits      = $this->getClassUsedTraits($subStmt);
                            }
                            $foundClass = true;

                            break 2;
                        }
                    }
                } elseif ($stmt instanceof Class_) {
                    $isArtisanCommand = $this->isArtisanCommandClass($stmt);

                    if ($isArtisanCommand) {
                        $commandName     = $this->getClassPropertyStringValue($stmt, 'signature');
                        $description     = $this->getClassPropertyStringValue($stmt, 'description');
                        $argumentCount   = $this->parseSignatureForCount($commandName, 'arguments');
                        $optionCount     = $this->parseSignatureForCount($commandName, 'options');
                        $hasHandleMethod = $this->classHasMethod($stmt, 'handle');
                        $usesTraits      = $this->getClassUsedTraits($stmt);
                    }
                    $foundClass = true;

                    break;
                }
            }

        } catch (Error $e) {
            return ['error' => 'Erro de sintaxe PHP: ' . Str::limit($e->getMessage(), 100)];
        } catch (Throwable $e) {
            return ['error' => 'Erro inesperado ao analisar: ' . Str::limit($e->getMessage(), 100)];
        }

        if ( ! $foundClass) {
            return ['type' => 'php_class', 'is_artisan_command' => false, 'error' => 'Arquivo PHP na pasta Commands não contém uma declaração de classe.'];
        }

        if ( ! $isArtisanCommand) {
            return ['type' => 'php_class', 'is_artisan_command' => false];
        }

        return [
            'type'               => 'artisan_command',
            'is_artisan_command' => true,
            'command_name'       => $commandName,
            'description'        => $description,
            'argument_count'     => $argumentCount,
            'option_count'       => $optionCount,
            'has_handle_method'  => $hasHandleMethod,
            'uses_traits'        => $usesTraits,
        ];
    }

    /**
     * Verifica se a classe estende a classe base de comandos Artisan.
     *
     * @param Class_ $classNode
     * @return bool
     */
    private function isArtisanCommandClass(Class_ $classNode): bool
    {
        if ( ! ($classNode->extends instanceof Name)) {
            return false;
        }
        $extendedClassName = (string) $classNode->extends;

        return in_array($extendedClassName, [
            'Illuminate\Console\Command',
        ]);
    }

    /**
     * Conta o número de argumentos ou opções definidos na assinatura do comando.
     *
     * @param string|null $signature
     * @param string $type
     * @return int
     */
    private function parseSignatureForCount(?string $signature, string $type): int
    {
        if (empty($signature)) {
            return 0;
        }

        $count = 0;

        if ($type === 'arguments') {
            preg_match_all('/\{([a-zA-Z0-9_]+)(?:::[^\}]+)?(?:(?:\?|\*)?)?\}/', $signature, $matches);
            $count = count($matches[1]);
        } elseif ($type === 'options') {
            preg_match_all('/--([a-zA-Z0-9_-]+)(?:[=\[]?.*?)?/', $signature, $matches);
            $count = count($matches[1]);
        }

        return $count;
    }
}
