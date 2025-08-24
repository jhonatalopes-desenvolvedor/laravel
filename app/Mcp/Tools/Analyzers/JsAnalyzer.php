<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class JsAnalyzer
{
    /**
     * Summary of nodePath
     *
     * @var string|null $scriptPath
     */
    private ?string $nodePath;

    /**
     * Summary of scriptPath
     *
     * @var string
     */
    private string $scriptPath;

    public function __construct()
    {
        $this->nodePath   = (new ExecutableFinder())->find('node');
        $this->scriptPath = base_path('resources/js/Mcp/JsAnalyzer.js');
    }

    /**
     * Analisa um conjunto de arquivos JavaScript usando o script externo e retorna os resultados da análise em formato estruturado.
     *
     * @param array<int, string> $filePaths
     * @return array<string, mixed>
     */
    public function analyzeBatch(array $filePaths): array
    {
        if (empty($filePaths)) {
            return [];
        }

        if ($this->nodePath === null) {
            return array_fill_keys($filePaths, ['status' => 'error', 'message' => 'Node.js executable not found in PATH.']);
        }

        if ( ! file_exists($this->scriptPath)) {
            return array_fill_keys($filePaths, ['status' => 'error', 'message' => "Analysis script not found at {$this->scriptPath}"]);
        }

        $jsonPaths = json_encode(array_values($filePaths));

        if ($jsonPaths === false) {
            return array_fill_keys($filePaths, ['status' => 'error', 'message' => 'Failed to encode file paths to JSON.']);
        }

        $process = new Process([$this->nodePath, $this->scriptPath, $jsonPaths]);
        $process->setTimeout(300);
        $process->setWorkingDirectory(base_path());

        try {
            $process->mustRun();
            $output = json_decode($process->getOutput(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return array_fill_keys($filePaths, ['status' => 'error', 'message' => 'Failed to decode JSON from Node.js batch script. Raw output: ' . $process->getOutput()]);
            }

            return $output;

        } catch (Throwable $e) {
            return array_fill_keys($filePaths, [
                'status'       => 'error',
                'message'      => 'Node.js batch script execution failed: ' . $e->getMessage(),
                'error_output' => $process->getErrorOutput(),
            ]);
        }
    }
}
