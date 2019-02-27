<?php

use MeetMatt\Colla\Mood\Presentation\WebApplication;

require_once __DIR__ . '/../app/bootstrap.php';

/** @var WebApplication $app */
$app = $container[WebApplication::class];
$app->run();