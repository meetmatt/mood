<?php

use MeetMatt\Colla\Mood\Infrastructure\Container\InfrastructureServiceProvider;
use MeetMatt\Colla\Mood\Infrastructure\Container\PresentationServiceProvider;

return [
    new InfrastructureServiceProvider(),
    new PresentationServiceProvider(),
];