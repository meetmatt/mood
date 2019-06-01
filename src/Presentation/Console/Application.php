<?php

namespace MeetMatt\Colla\Mood\Presentation\Console;

use MeetMatt\Colla\Mood\Presentation\Http\Application as WebApplication;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as SymfonyConsoleApplication;

class Application extends SymfonyConsoleApplication
{
    /** @var ContainerInterface */
    protected $container;

    /** @var WebApplication */
    private $webApplication;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function setWebApplication(WebApplication $webApplication): void
    {
        $this->webApplication = $webApplication;
    }
}