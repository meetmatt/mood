<?php

use MeetMatt\Colla\Mood\Domain\Metrics\MetricsInterface;
use Slim\App;
use Slim\Container;

$timerStart = microtime(true);

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container(require_once __DIR__ . '/../app/config.php');

$services = require_once __DIR__ . '/../app/services.php';
foreach ($services as $service) {
    $container->register($service);
}

/** @var App $app */
$app = $container[App::class];
$app->run();

fastcgi_finish_request();

$timerEnd = microtime(true);
/** @var MetricsInterface $metrics */
$metrics = $container[MetricsInterface::class];
$metrics->increment('page_views.count');
$metrics->microtiming('response_time.timing', $timerEnd - $timerStart);
$metrics->flush();

