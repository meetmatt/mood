<?php

use MeetMatt\Colla\Mood\Presentation\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

/** @var Application $app */
$app = $container[Application::class];
$app->run();