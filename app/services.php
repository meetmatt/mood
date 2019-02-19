<?php

use MeetMatt\Colla\Mood\Infrastructure\Container\DomainServiceProvider;
use MeetMatt\Colla\Mood\Infrastructure\Container\InfrastructureServiceProvider;
use MeetMatt\Colla\Mood\Infrastructure\Container\PresentationServiceProvider;

return [
    new InfrastructureServiceProvider(),
    new DomainServiceProvider(),
    new PresentationServiceProvider(),
];