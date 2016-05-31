<?php
/**
 * App settings array
 */
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,

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
            'path' => __DIR__ . '/../../../log/app.log',
        ],

        //Google Analytics
        'google_analytics' => [
            'api_key' => false,
            'anonymize_ip' => false
        ]
    ]
];