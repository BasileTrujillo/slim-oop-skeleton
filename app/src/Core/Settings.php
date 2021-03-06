<?php
/**
 * App settings array
 */
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,

        // Error handler
        // Default: Display call stack in orignal slim error
        'error' => [
            // Enable / disable Whoops error
            'whoops' => true,
            // Enable / disable JSON error (if 'whoops' == false)
            'json' => false
        ],

        // Assets Settings
        'assets' => [
            'css_url'  => 'assets/css',
            'js_url'   => 'assets/js',

            // Load minified CSS and JS files if exists
            'min' => true,
            'css_min_url'  => 'assets/css/min',
            'js_min_url'   => 'assets/js/min',
        ],

        // CLI Settings
        'cli' => [
            // Enable / Disable profiling display
            'profiling' => false
        ],

        //Debug Bar Setting
        'debugbar' => [
            'enabled' => false,
            // Enable or disable extra collectors
            'collectors' => [
                'config'    => true,
                'monolog'   => true,
                'pdo'       => true
            ]
        ],

        // View settings
        'view' => [
            'template_path' => __DIR__ . '/../../templates',
            'twig' => [
                'cache' => __DIR__ . '/../../../cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // Monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../../../log/',
            'filename' => 'app.log',
            'filename_cli' => 'app_cli.log'
        ],

        //Google Analytics
        'google_analytics' => [
            'api_key' => false,
            'anonymize_ip' => false
        ],

        // PDO settings
        'pdo' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'foo',
            'user'      => 'bar',
            'passwd'    => 'baz'
        ],

        // Mongo DB settings
        'mongo' => [
            'host' => '127.0.0.1',
            'port' => 27017,
            'options' => [
                //"username" => 'foo',
                //"password" => 'bar'
            ],
            'driverOptions' => [],
            'default_db' => 'database'
        ],

        // Mongo DB Authenticator settings
        'MongoAuthenticator' => [
            'database' => 'database',
            'user_collection' => 'user',
            'user_field' => 'login',
            'hash_field' => 'hash',
        ],

        // HttpBasicAuthentication Settings
        'HttpBasicAuthentication' => [
            //'relaxed' => '',
            'secure' => false

        ],

        // JwtAuthentication settings
        'JwtAuthentication' => [
            'secret' => 'secretpassword', // Change it into local.settings.php (unversionned)
            'secure' => false
        ],
    ]
];