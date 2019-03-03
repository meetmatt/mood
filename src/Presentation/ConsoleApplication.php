<?php

namespace MeetMatt\Colla\Mood\Presentation;

use MeetMatt\Colla\Mood\Domain\Metrics\MetricsInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleApplication extends Application
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

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $timerStart = microtime(true);

        $exitCode = parent::run();

        $timerEnd = microtime(true);

        /** @var MetricsInterface $metrics */
        $metrics = $this->container->get(MetricsInterface::class);
        $metrics->increment('commands.count');
        $metrics->microtiming('command_time.timing', $timerEnd - $timerStart);
        $metrics->flush();

        return $exitCode;
    }
}