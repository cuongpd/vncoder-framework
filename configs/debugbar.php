<?php

return [
    'enabled' => true,
    'except' => [
        'api/*',
        '*ajax*',
        '*autsh*',
        '*callback*',
        'backend/*.html'
    ],

    'storage' => [
        'enabled'    => false,
        'driver'     => 'file', // redis, file, pdo, socket, custom
        'path'       => storage_path('framework/debugbar'), // For file driver
        'connection' => null,   // Leave null for default connection (Redis/PDO)
        'provider'   => '', // Instance of StorageInterface for custom driver
        'hostname'   => '127.0.0.1', // Hostname to use with the "socket" driver
        'port'       => 2304, // Port to use with the "socket" driver
    ],

    // Supported: "phpstorm", "vscode", "vscode-insiders", "vscode-remote", "vscode-insiders-remote", "vscodium", "textmate", "emacs", "sublime", "atom", "nova", "macvim", "idea", "netbeans", "xdebug", "espresso"
    'editor' => 'phpstorm',

    'remote_sites_path' => env('DEBUGBAR_REMOTE_SITES_PATH', ''),
    'local_sites_path' => env('DEBUGBAR_LOCAL_SITES_PATH', ''),

    'include_vendors' => false,
    'capture_ajax' => false,
    'add_ajax_timing' => false,
    'error_handler' => false,
    'clockwork' => false,

    'collectors' => [
        'phpinfo'         => true,  // Php version
        'messages'        => true,  // Messages
        'time'            => true,  // Time Datalogger
        'memory'          => true,  // Memory usage
        'exceptions'      => true,  // Exception displayer
        'log'             => true,  // Logs from Monolog (merged in messages if enabled)
        'db'              => true,  // Show database (PDO) queries and bindings
        'views'           => true,  // Views with their data
        'route'           => true,  // Current route information
        'auth'            => false, // Display Laravel authentication status
        'gate'            => false,  // Display Laravel Gate checks
        'session'         => true,  // Display session data
        'symfony_request' => true,  // Only one can be enabled..
        'mail'            => true,  // Catch mail messages
        'laravel'         => false, // Laravel version and environment
        'events'          => false, // All events fired
        'default_request' => false, // Regular or special Symfony request logger
        'logs'            => false, // Add the latest log messages
        'files'           => false, // Show the included files
        'config'          => false, // Display config settings
        'cache'           => true, // Display cache events
        'models'          => true,  // Display models
        'livewire'        => false,  // Display Livewire (when available)
    ],

    'options' => [
        'auth' => [
            'show_name' => true,   // Also show the users name/email in the debugbar
        ],
        'db' => [
            'with_params'       => true,   // Render SQL with the parameters substituted
            'backtrace'         => true,   // Use a backtrace to find the origin of the query in your files.
            'backtrace_exclude_paths' => [],   // Paths to exclude from backtrace. (in addition to defaults)
            'timeline'          => false,  // Add the queries to the timeline
            'duration_background'  => true,   // Show shaded background on each query relative to how long it took to execute.
            'explain' => [                 // Show EXPLAIN output on queries
                'enabled' => false,
                'types' => ['SELECT'],     // Deprecated setting, is always only SELECT || ['SELECT', 'INSERT', 'UPDATE', 'DELETE']
            ],
            'hints'             => false,    // Show hints for common mistakes
            'show_copy'         => false,    // Show copy button next to the query
        ],
        'mail' => [
            'full_log' => false,
        ],
        'views' => [
            'timeline' => false,  // Add the views to the timeline (Experimental)
            'data' => false,    //Note: Can slow down the application, because the data can be quite large..
        ],
        'route' => [
            'label' => true,  // show complete route on bar
        ],
        'logs' => [
            'file' => null,
        ],
        'cache' => [
            'values' => true, // collect cache values
        ],
    ],

    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_domain' => null,
    'theme' => 'dark', // auto, light, dark
    'debug_backtrace_limit' => 50,
];
