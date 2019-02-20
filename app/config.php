<?php

return [
    'settings' => [
        'displayErrorDetails' => true,

        'mysql' => [
            'host'     => 'mysql',
            'database' => 'mood',
            'user'     => 'root',
        ],

        'statsd' => [
            'host' => 'telegraf',
            'tags' => [
                'application' => 'mood',
            ],
        ],

        'twig' => [
            'debug'       => true,
            'auto_reload' => true,
            'templates'   => __DIR__ . '/../template',
            'cache'       => '/tmp/cache/twig',
        ],
    ],
    'routes'   => require_once __DIR__ . '/routes.php',
];