<?php

return [
    'settings' => [
        'displayErrorDetails' => false,

        'mysql' => [
            'host'     => 'mysql',
            'database' => 'mood',
            'user'     => 'root',
        ],

        'twig' => [
            'debug'       => true,
            'auto_reload' => true,
            'templates'   => __DIR__ . '/../template',
            'cache'       => '/tmp/cache/twig',
            'base_url'    => getenv('APPLICATION_BASE_URL'),
        ],
    ],
    'routes'   => require __DIR__ . '/routes.php',
    'commands' => require __DIR__ . '/commands.php',
];