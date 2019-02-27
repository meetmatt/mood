<?php

namespace MeetMatt\Colla\Mood\Presentation;

use MeetMatt\Colla\Mood\Domain\Metrics\MetricsInterface;
use Slim\App;

class WebApplication extends App
{
    public function run($silent = false)
    {
        $timerStart = microtime(true);

        $response = parent::run($silent);

        fastcgi_finish_request();

        $timerEnd = microtime(true);

        /** @var MetricsInterface $metrics */
        $metrics = $this->getContainer()->get(MetricsInterface::class);
        $metrics->increment('page_views.count');
        $metrics->microtiming('response_time.timing', $timerEnd - $timerStart);
        $metrics->flush();

        return $response;
    }

}