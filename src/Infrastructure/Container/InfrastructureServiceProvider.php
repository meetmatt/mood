<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Container;

use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Identity\RandomIdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Metrics\MetricsInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use MeetMatt\Colla\Mood\Infrastructure\Cryptography\RandomIdGenerator;
use MeetMatt\Colla\Mood\Infrastructure\Email\NoopEmailSendingService;
use MeetMatt\Colla\Mood\Infrastructure\Metrics\DogStatsdMetrics;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\EmailRepository;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\FeedbackRepository;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\TeamRepository;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class InfrastructureServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[EasyDB::class] = function (Container $container) {
            $settings = $container['settings']['mysql'];

            return Factory::create(
                sprintf(
                    'mysql:host=%s;dbname=%s',
                    $settings['host'],
                    $settings['database']
                ),
                $settings['user'],
                $settings['password'] ?? null
            );
        };

        $pimple[MetricsInterface::class] = function (Container $container) {
            $settings = $container['settings']['statsd'];

            return new DogStatsdMetrics(
                $settings['host'],
                $settings['port'] ?? 8125,
                $settings['tags'] ?? []
            );
        };

        $pimple[RandomIdGeneratorInterface::class] = function () {
            return new RandomIdGenerator();
        };

        $pimple[TeamRepositoryInterface::class] = function (Container $container) {
            return new TeamRepository(
                $container[EasyDB::class],
                $container[RandomIdGeneratorInterface::class]
            );
        };

        $pimple[FeedbackRepositoryInterface::class] = function (Container $container) {
            return new FeedbackRepository(
                $container[EasyDB::class]
            );
        };

        $pimple[EmailRepositoryInterface::class] = function (Container $container) {
            return new EmailRepository(
                $container[EasyDB::class]
            );
        };

        $pimple[EmailSendingServiceInterface::class] = function () {
            return new NoopEmailSendingService();
        };
    }
}