<?php

declare(strict_types = 1);

return [

    /*
    |--------------------------------------------------------------------------
    | MCP Server Information
    |--------------------------------------------------------------------------
    */

    'server' => [
        'name'         => env('MCP_SERVER_NAME', 'Laravel MCP'),
        'version'      => env('MCP_SERVER_VERSION', '1.0.0'),
        'instructions' => env('MCP_SERVER_INSTRUCTIONS'),
    ],

    /*
    |--------------------------------------------------------------------------
    | MCP Discovery Configuration
    |--------------------------------------------------------------------------
    */

    'discovery' => [
        'base_path'    => base_path(),
        'directories'  => array_filter(explode(',', env('MCP_DISCOVERY_DIRECTORIES', 'app/Mcp'))),
        'exclude_dirs' => [
            'vendor',
            'tests',
            'storage',
            'public',
            'resources',
            'bootstrap',
            'config',
            'database',
            'routes',
            'node_modules',
            '.git',
        ],
        'definitions_file' => base_path('routes/mcp.php'),
        'auto_discover'    => (bool) env('MCP_AUTO_DISCOVER', true),
        'save_to_cache'    => (bool) env('MCP_DISCOVERY_SAVE_TO_CACHE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | MCP Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'store' => env('MCP_CACHE_STORE', config('cache.default')),
    ],

    /*
    |--------------------------------------------------------------------------
    | MCP Transport Configuration
    |--------------------------------------------------------------------------
    */

    'transports' => [
        'stdio' => [
            'enabled' => (bool) env('MCP_STDIO_ENABLED', true),
        ],

        'http_dedicated' => [
            'enabled'              => (bool) env('MCP_HTTP_DEDICATED_ENABLED', true),
            'legacy'               => (bool) env('MCP_HTTP_DEDICATED_LEGACY', false),
            'host'                 => env('MCP_HTTP_DEDICATED_HOST', '127.0.0.1'),
            'port'                 => (int) env('MCP_HTTP_DEDICATED_PORT', 8090),
            'path_prefix'          => env('MCP_HTTP_DEDICATED_PATH_PREFIX', 'mcp'),
            'ssl_context_options'  => [],
            'enable_json_response' => (bool) env('MCP_HTTP_DEDICATED_JSON_RESPONSE', true),
            'event_store'          => null, // FQCN or null
        ],

        'http_integrated' => [
            'enabled'              => (bool) env('MCP_HTTP_INTEGRATED_ENABLED', true),
            'legacy'               => (bool) env('MCP_HTTP_INTEGRATED_LEGACY', false),
            'route_prefix'         => env('MCP_HTTP_INTEGRATED_ROUTE_PREFIX', 'mcp'),
            'middleware'           => ['api'],
            'domain'               => env('MCP_HTTP_INTEGRATED_DOMAIN'),
            'sse_poll_interval'    => (int) env('MCP_HTTP_INTEGRATED_SSE_POLL_SECONDS', 1),
            'cors_origin'          => env('MCP_HTTP_INTEGRATED_CORS_ORIGIN', '*'),
            'enable_json_response' => (bool) env('MCP_HTTP_INTEGRATED_JSON_RESPONSE', true),
            'event_store'          => null, // FQCN or null
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Management Configuration
    |--------------------------------------------------------------------------
    */

    'session' => [
        'driver'     => env('MCP_SESSION_DRIVER', 'cache'), // 'file', 'cache', 'database', 'redis', 'memcached', 'dynamodb', 'array'
        'ttl'        => (int) env('MCP_SESSION_TTL', 3600),
        'store'      => env('MCP_SESSION_CACHE_STORE', config('cache.default')),
        'path'       => env('MCP_SESSION_FILE_PATH', storage_path('framework/mcp_sessions')),
        'connection' => env('MCP_SESSION_DB_CONNECTION', config('database.default')),
        'table'      => env('MCP_SESSION_DB_TABLE', 'mcp_sessions'),
        'lottery'    => [2, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Limit
    |--------------------------------------------------------------------------
    */

    'pagination_limit' => env('MCP_PAGINATION_LIMIT', 50),

    /*
    |--------------------------------------------------------------------------
    | MCP Capabilities Configuration
    |--------------------------------------------------------------------------
    |
    | The following capabilities are supported:
    | - tools - Whether the server offers tools.
    | - toolsListChanged - Whether the server supports sending a notification when the list of tools changes.
    | - resources - Whether the server offers resources.
    | - resourcesSubscribe - Whether the server supports resource subscriptions.
    | - resourcesListChanged - Whether the server supports sending a notification when the list of resources changes.
    | - prompts - Whether the server offers prompts.
    | - promptsListChanged - Whether the server supports sending a notification when the list of prompts changes.
    | - logging - Whether the server supports sending log messages to the client.
    | - completions - Whether the server supports argument autocompletion suggestions.
    | - experimental - Experimental, non-standard capabilities that the server supports.
    |

    */
    'capabilities' => [
        'tools'            => (bool) env('MCP_CAP_TOOLS_ENABLED', true),
        'toolsListChanged' => (bool) env('MCP_CAP_TOOLS_LIST_CHANGED', true),

        'resources'            => (bool) env('MCP_CAP_RESOURCES_ENABLED', true),
        'resourcesSubscribe'   => (bool) env('MCP_CAP_RESOURCES_SUBSCRIBE', true),
        'resourcesListChanged' => (bool) env('MCP_CAP_RESOURCES_LIST_CHANGED', true),

        'prompts'            => (bool) env('MCP_CAP_PROMPTS_ENABLED', true),
        'promptsListChanged' => (bool) env('MCP_CAP_PROMPTS_LIST_CHANGED', true),

        'logging' => (bool) env('MCP_CAP_LOGGING_ENABLED', true),

        'completions' => (bool) env('MCP_CAP_COMPLETIONS_ENABLED', true),

        'experimental' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'channel' => env('MCP_LOG_CHANNEL', config('logging.default')),
        'level'   => env('MCP_LOG_LEVEL', 'info'),
    ],
];
