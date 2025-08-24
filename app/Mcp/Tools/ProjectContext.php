<?php

declare(strict_types = 1);

namespace App\Mcp\Tools;

use App\Mcp\Tools\Analyzers\ArtisanCommandAnalyzer;
use App\Mcp\Tools\Analyzers\ControllerAnalyzer;
use App\Mcp\Tools\Analyzers\EnumAnalyzer;
use App\Mcp\Tools\Analyzers\EventAnalyzer;
use App\Mcp\Tools\Analyzers\FormRequestAnalyzer;
use App\Mcp\Tools\Analyzers\JobAnalyzer;
use App\Mcp\Tools\Analyzers\JsAnalyzer;
use App\Mcp\Tools\Analyzers\ListenerAnalyzer;
use App\Mcp\Tools\Analyzers\MetaDataAnalyzer;
use App\Mcp\Tools\Analyzers\MigrationAnalyzer;
use App\Mcp\Tools\Analyzers\ModelAnalyzer;
use App\Mcp\Tools\Analyzers\PhpFileAnalyzer;
use App\Mcp\Tools\Analyzers\ResourceAnalyzer;
use App\Mcp\Tools\Analyzers\VueAnalyzer;
use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ProjectContext
{
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @var string
     */
    private string $rootPath;

    /**
     * @var array<int, string>
     */
    private array $excludedPaths = [];

    private MetaDataAnalyzer $metaDataAnalyzer;

    private EnumAnalyzer $enumAnalyzer;

    private ModelAnalyzer $modelAnalyzer;

    private ControllerAnalyzer $controllerAnalyzer;

    private FormRequestAnalyzer $formRequestAnalyzer;

    private ArtisanCommandAnalyzer $artisanCommandAnalyzer;

    private ResourceAnalyzer $resourceAnalyzer;

    private JobAnalyzer $jobAnalyzer;

    private ListenerAnalyzer $listenerAnalyzer;

    private EventAnalyzer $eventAnalyzer;

    private MigrationAnalyzer $migrationAnalyzer;

    private PhpFileAnalyzer $generalPhpFileAnalyzer;

    /**
     * Inicializa o contexto do projeto, escaneando arquivos e coletando metadados.
     *
     * @param string $rootPath
     * @param array<int, string> $excludedPaths
     */
    public function __construct(string $rootPath, array $excludedPaths = [])
    {
        $this->rootPath      = rtrim($rootPath, '/');
        $this->excludedPaths = array_merge([
            '.env',
            '.env.example',
            'vendor/',
            'node_modules/',
            'storage/',
            'bootstrap/cache/',
            'public/hot',
            'public/build',
            '*.log',
            '*.bak',
            '*.sqlite',
            'npm-debug.log',
            'yarn-error.log',
            '.idea/',
            '.vscode/',
            '.DS_Store',
            '.git/',
            'app/Mcp',
            'resources/js/Mcp',
        ], $excludedPaths);

        $this->metaDataAnalyzer       = new MetaDataAnalyzer($this->rootPath);
        $this->migrationAnalyzer      = new MigrationAnalyzer();
        $this->enumAnalyzer           = new EnumAnalyzer();
        $this->modelAnalyzer          = new ModelAnalyzer();
        $this->controllerAnalyzer     = new ControllerAnalyzer();
        $this->formRequestAnalyzer    = new FormRequestAnalyzer();
        $this->artisanCommandAnalyzer = new ArtisanCommandAnalyzer();
        $this->resourceAnalyzer       = new ResourceAnalyzer();
        $this->jobAnalyzer            = new JobAnalyzer();
        $this->listenerAnalyzer       = new ListenerAnalyzer();
        $this->eventAnalyzer          = new EventAnalyzer();
        $this->generalPhpFileAnalyzer = new class() extends PhpFileAnalyzer
        {
            /**
             * Retorna um array vazio, pois este analisador genérico não realiza análises específicas.
             *
             * @param string $filePath
             * @return array<string, mixed>
             */
            public function analyze(string $filePath): array
            {
                return [];
            }
        };

        $this->data = $this->buildProjectContext();
    }

    /**
     * Retorna os dados do contexto do projeto, filtrados opcionalmente.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function get(array $filters = []): array
    {
        if (empty($filters)) {
            return $this->data;
        }

        $result = [];

        if (in_array('meta', $filters) || (isset($filters['meta']) && $filters['meta'] === true)) {
            if (isset($this->data['meta'])) {
                $result['meta'] = $this->data['meta'];
            }
        }

        if (in_array('trees', $filters)) {
            if (isset($this->data['trees'])) {
                $result['trees'] = $this->data['trees'];
            }
        } elseif (isset($filters['trees']) && is_array($filters['trees'])) {
            if (isset($this->data['trees'])) {
                $result['trees'] = $this->filterTrees($this->data['trees'], $filters['trees']);
            }
        }

        return $result;
    }

    /**
     * Filtra recursivamente a árvore de diretórios com base em padrões de inclusão.
     *
     * @param array<string, mixed> $fullTree
     * @param array<int|string, mixed> $patterns
     * @param string $currentPath
     * @return array<string, mixed>
     */
    private function filterTrees(array $fullTree, array $patterns, string $currentPath = ''): array
    {
        $filteredTree = [];

        if (in_array('*', $patterns)) {
            return $fullTree;
        }

        foreach ($fullTree as $name => $content) {
            $path  = ltrim($currentPath . '/' . $name, '/');
            $isDir = is_array($content) && ! isset($content['size']);

            $shouldIncludeEntireNode        = false;
            $specificFilePatternsForThisDir = [];

            foreach ($patterns as $key => $patternValue) {
                if (is_numeric($key) && is_string($patternValue)) {
                    if ($path === $patternValue) {
                        $shouldIncludeEntireNode = true;

                        break;
                    } elseif (str_ends_with($patternValue, '/*')) {
                        $dirPrefix = rtrim($patternValue, '/*');

                        if ($path === $dirPrefix || str_starts_with($path, $dirPrefix . '/')) {
                            $shouldIncludeEntireNode = true;

                            break;
                        }
                    }
                } elseif (is_string($key) && is_array($patternValue)) {
                    if ($path === $key && $isDir) {
                        $specificFilePatternsForThisDir = $patternValue;

                        break;
                    }
                }
            }

            if ($shouldIncludeEntireNode) {
                $filteredTree[$name] = $content;
            } elseif ( ! empty($specificFilePatternsForThisDir)) {
                if ($isDir) {
                    $filteredFiles = [];

                    foreach ($content as $fileName => $fileContent) {
                        if (in_array($fileName, $specificFilePatternsForThisDir)) {
                            $filteredFiles[$fileName] = $fileContent;
                        }
                    }

                    if ( ! empty($filteredFiles)) {
                        $filteredTree[$name] = $filteredFiles;
                    }
                }
            } elseif ($isDir) {
                $subFilteredTree = $this->filterTrees($content, $patterns, $path);

                if ( ! empty($subFilteredTree)) {
                    $filteredTree[$name] = $subFilteredTree;
                }
            }
        }

        return $filteredTree;
    }

    /**
     * Constrói o contexto completo do projeto, incluindo metadados e a árvore de arquivos.
     *
     * @return array<string, mixed>
     */
    private function buildProjectContext(): array
    {
        $context = [
            'meta'  => $this->metaDataAnalyzer->getMetaData(),
            'trees' => $this->scanProjectTree(),
        ];

        return $context;
    }

    /**
     * Escaneia a árvore de diretórios do projeto, analisa arquivos PHP, Vue e JS.
     *
     * @return array<string, mixed>
     */
    private function scanProjectTree(): array
    {
        $tree   = [];
        $finder = new Finder();

        $vueFilePaths = [];
        $jsFilePaths  = [];

        $finder->in($this->rootPath)
            ->ignoreDotFiles(false)
            ->depth('>= 0')
            ->sortByName();

        foreach ($finder as $item) {
            $relativePath = $item->getRelativePathname();

            if ($this->isExcluded($relativePath, $item->isDir())) {
                continue;
            }

            $pathParts    = explode('/', $relativePath);
            $currentLevel = &$tree;

            foreach ($pathParts as $index => $part) {
                if ($index === count($pathParts) - 1) {
                    if ($item->isDir()) {
                        if ( ! isset($currentLevel[$part])) {
                            $currentLevel[$part] = [];
                        }
                    } else {
                        $fileInfo = [
                            'size'       => $item->getSize(),
                            'lines'      => $this->countFileLines($item->getPathname()),
                            'updated_at' => date('Y-m-d H:i:s', $item->getMTime()),
                        ];

                        $summary = null;

                        if (Str::endsWith($relativePath, '.vue')) {

                            $vueFilePaths[$relativePath] = $item->getPathname();
                            $summary                     = ['type' => 'vue_sfc'];
                        } elseif (Str::endsWith($relativePath, '.js')) {
                            $jsFilePaths[$relativePath] = $item->getPathname();
                            $summary                    = ['type' => 'javascript_module'];
                        } elseif (Str::endsWith($relativePath, '.php')) {
                            if (Str::startsWith($relativePath, 'database/migrations/')) {
                                $summary = $this->migrationAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Enums/')) {
                                $summary = $this->enumAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Models/')) {
                                $summary = $this->modelAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Http/Controllers/')) {
                                $summary = $this->controllerAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Http/Requests/')) {
                                $summary = $this->formRequestAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Console/Commands/')) {
                                $summary = $this->artisanCommandAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Http/Resources/')) {
                                $summary = $this->resourceAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Jobs/')) {
                                $summary = $this->jobAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Listeners/')) {
                                $summary = $this->listenerAnalyzer->analyze($item->getPathname());
                            } elseif (Str::startsWith($relativePath, 'app/Events/')) {
                                $summary = $this->eventAnalyzer->analyze($item->getPathname());
                            } else {
                                $summary = $this->generalPhpFileAnalyzer->getGeneralClassSummary($item->getPathname());
                            }
                        }

                        if ($summary) {

                            $fileInfo = array_merge($fileInfo, $summary);
                        }
                        $currentLevel[$part] = $fileInfo;
                    }
                } else {
                    if ( ! isset($currentLevel[$part]) || ! is_array($currentLevel[$part])) {
                        $currentLevel[$part] = [];
                    }
                    $currentLevel = &$currentLevel[$part];
                }
            }
            unset($currentLevel);
        }

        if ( ! empty($vueFilePaths)) {
            $vueAnalysisResults = (new VueAnalyzer())->analyzeBatch(array_values($vueFilePaths));
            $this->mergeFrontendResultsIntoTree($tree, $vueFilePaths, $vueAnalysisResults);
        }

        if ( ! empty($jsFilePaths)) {
            $jsAnalysisResults = (new JsAnalyzer())->analyzeBatch(array_values($jsFilePaths));
            $this->mergeFrontendResultsIntoTree($tree, $jsFilePaths, $jsAnalysisResults);
        }

        return $tree;
    }

    /**
     * Mescla os resultados da análise de frontend (Vue/JS) na árvore de diretórios.
     *
     * @param array<string, mixed> $tree
     * @param array<string, string> $pathMap
     * @param array<string, mixed> $analysisResults
     */
    private function mergeFrontendResultsIntoTree(array &$tree, array $pathMap, array $analysisResults): void
    {
        foreach ($pathMap as $relativePath => $absolutePath) {

            if ( ! isset($analysisResults[$absolutePath])) {
                continue;
            }

            $result       = $analysisResults[$absolutePath];
            $pathParts    = explode('/', $relativePath);
            $currentLevel = &$tree;

            foreach ($pathParts as $index => $part) {
                if ( ! isset($currentLevel[$part])) {
                    break;
                }

                if ($index < count($pathParts) - 1) {
                    $currentLevel = &$currentLevel[$part];
                } else {
                    if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {
                        $currentLevel[$part] = array_merge($currentLevel[$part], $result['data']);
                    } else {
                        $currentLevel[$part]['error'] = $result['message'] ?? 'Unknown frontend analysis error';
                    }
                }
            }
        }
        unset($currentLevel);
    }

    /**
     * Conta as linhas de um arquivo, ignorando linhas vazias.
     *
     * @param string $filePath
     * @return int
     */
    private function countFileLines(string $filePath): int
    {
        try {
            return count(file($filePath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES));
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Verifica se um caminho relativo deve ser excluído da análise.
     *
     * @param string $relativePath
     * @param bool $isDir
     * @return bool
     */
    private function isExcluded(string $relativePath, bool $isDir): bool
    {
        $normalizedPath = str_replace('\\', '/', $relativePath);

        foreach ($this->excludedPaths as $pattern) {
            $normalizedPattern = str_replace('\\', '/', $pattern);

            if ($normalizedPath === $normalizedPattern) {
                return true;
            }

            if (Str::endsWith($normalizedPattern, '/')) {
                $dirPattern = rtrim($normalizedPattern, '/');

                if ($normalizedPath === $dirPattern || Str::startsWith($normalizedPath, $dirPattern . '/')) {
                    return true;
                }
            }

            if ($isDir && $normalizedPath === $normalizedPattern) {
                return true;
            }

            if (Str::startsWith($normalizedPath, $normalizedPattern . '/')) {
                return true;
            }

            if (fnmatch($normalizedPattern, $normalizedPath, FNM_PATHNAME)) {
                return true;
            }

            if ( ! Str::contains($normalizedPattern, '/') && fnmatch($normalizedPattern, basename($normalizedPath))) {
                return true;
            }
        }

        return false;
    }
}
