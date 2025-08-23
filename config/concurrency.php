<?php

declare(strict_types = 1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Concurrency Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "process", "fork", "sync"
    |
    */

    'default' => env('CONCURRENCY_DRIVER', 'process'),

];
