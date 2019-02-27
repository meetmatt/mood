<?php

use MeetMatt\Colla\Mood\Presentation\ConsoleApplication;

$container = require __DIR__ . '/bootstrap.php';

/** @var ConsoleApplication $app */
$app = $container[ConsoleApplication::class];
$app->run();