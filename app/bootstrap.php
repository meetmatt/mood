<?php

use Slim\Container;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container(require __DIR__ . '/../app/config.php');

$services = require __DIR__ . '/../app/services.php';
foreach ($services as $service) {
    $container->register($service);
}

return $container;