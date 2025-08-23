<?php

declare(strict_types = 1);

namespace App\Mcp\Tools\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MetaDataAnalyzer
{
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMetaData(): array
    {
        $meta = [];

        $meta['root_path']           = $this->rootPath;
        $meta['php_version_runtime'] = phpversion();

        $composerJsonPath = $this->rootPath . '/composer.json';

        if (File::exists($composerJsonPath)) {
            $fullComposerData     = json_decode(File::get($composerJsonPath), true);
            $filteredComposerData = [];

            $userSelectedComposerKeys = ['name', 'description', 'keywords', 'type', 'require', 'require-dev', 'autoload', 'autoload-dev'];

            foreach ($userSelectedComposerKeys as $key) {
                if (isset($fullComposerData[$key])) {
                    if ($key === 'autoload' || $key === 'autoload-dev') {
                        if (isset($fullComposerData[$key]['psr-4'])) {
                            $filteredComposerData[$key]['psr-4'] = $fullComposerData[$key]['psr-4'];
                        }
                    } else {
                        $filteredComposerData[$key] = $fullComposerData[$key];
                    }
                }
            }
            $meta['composer.json'] = $filteredComposerData;

            if (isset($fullComposerData['require']['laravel/framework'])) {
                $meta['laravel_version'] = str_replace(['^', '~'], '', $fullComposerData['require']['laravel/framework']);
            } else {
                $meta['laravel_version'] = 'unknown';
            }

            if (isset($fullComposerData['require']['php'])) {
                preg_match('/^(\d+\.\d+)/', str_replace(['^', '~'], '', $fullComposerData['require']['php']), $matches);

                if (isset($matches[1])) {
                    $meta['php_version_required'] = $matches[1];
                }
            }
        } else {
            $meta['laravel_version'] = 'unknown';
        }

        $packageJsonPath = $this->rootPath . '/package.json';

        if (File::exists($packageJsonPath)) {
            $fullPackageData     = json_decode(File::get($packageJsonPath), true);
            $filteredPackageData = [];

            $userSelectedPackageKeys = ['name', 'version', 'description', 'devDependencies', 'dependencies'];

            foreach ($userSelectedPackageKeys as $key) {
                if (isset($fullPackageData[$key])) {
                    $filteredPackageData[$key] = $fullPackageData[$key];
                }
            }
            $meta['package.json'] = $filteredPackageData;
        }

        $meta['env'] = $this->getEnvData();

        return $meta;
    }

    /**
     * @return array<string, mixed>
     */
    private function getEnvData(): array
    {
        $envData = [];
        $envPath = $this->rootPath . '/.env';

        $allowedEnvKeys = [
            'APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'APP_TIMEZONE',
            'LOG_CHANNEL', 'DB_CONNECTION', 'SESSION_DRIVER', 'BROADCAST_CONNECTION',
            'QUEUE_CONNECTION', 'CACHE_STORE', 'MAIL_MAILER',
        ];

        if (File::exists($envPath)) {
            $lines = explode("\n", File::get($envPath));

            foreach ($lines as $line) {
                $line = trim($line);

                if (empty($line) || Str::startsWith($line, '#')) {
                    continue;
                }

                if (Str::contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);

                    if (in_array($key, $allowedEnvKeys)) {
                        $envData[$key] = $value;
                    }
                }
            }
        }

        $envSummary = [
            'app_name'         => $envData['APP_NAME'] ?? 'Not Set',
            'app_env'          => $envData['APP_ENV'] ?? 'local',
            'app_debug'        => ($envData['APP_DEBUG'] ?? 'false') === 'true',
            'db_connection'    => $envData['DB_CONNECTION'] ?? 'mysql',
            'session_driver'   => $envData['SESSION_DRIVER'] ?? 'file',
            'queue_connection' => $envData['QUEUE_CONNECTION'] ?? 'sync',
            'cache_store'      => $envData['CACHE_STORE'] ?? 'file',
            'mail_configured'  => isset($envData['MAIL_MAILER']) && $envData['MAIL_MAILER'] !== 'log',
            'redis_present'    => Str::contains(implode(' ', array_keys($envData)), 'REDIS'),
            'aws_s3_present'   => Str::contains(implode(' ', array_keys($envData)), 'AWS_BUCKET'),
        ];

        return $envSummary;
    }
}
