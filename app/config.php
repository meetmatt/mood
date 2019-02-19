<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'mysql' => [
            'host' => 'mysql',
            'database' => 'mood',
            'user' => 'root',
        ],
        'statsd' => [
            'host' => 'telegraf',
            'tags' => [
                'application' => 'mood',
            ],
        ],
    ],
    'routes' => require_once __DIR__ . '/routes.php',
];