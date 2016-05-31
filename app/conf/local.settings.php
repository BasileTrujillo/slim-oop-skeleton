<?php
/**
 * Override / Define settings here
 */
return [
    'settings' => [

        //Slim Settings
        'displayErrorDetails' => true,

        //Debug Bar Setting
        'debugbar' => [
            'enabled' => true,
            // Enable or disable extra collectors
            'collectors' => [
                'config'    => true,
                'monolog'   => true,
                'pdo'       => true
            ]
        ],

        // PDO settings
        'pdo' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'test',
            'user'      => 'root',
            'passwd'    => 'root'
        ],

        //Google Analytics
        'google_analytics' => [
            'api_key' => false,
            'anonymize_ip' => false
        ]
    ]
];