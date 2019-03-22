<?php

use MeetMatt\Colla\Mood\Presentation\Http\Application;

require_once __DIR__ . '/../app/bootstrap.php';

/** @var Application $app */
$app = $container[Application::class];
$app->run();