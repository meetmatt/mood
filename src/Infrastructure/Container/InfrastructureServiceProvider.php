<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Container;

use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Identity\IdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Report\ReportRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use MeetMatt\Colla\Mood\Infrastructure\Identity\UuidGenerator;
use MeetMatt\Colla\Mood\Infrastructure\Email\PhpMailEmailSendingService;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\EmailRepository;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\FeedbackRepository;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\ReportRepository;
use MeetMatt\Colla\Mood\Infrastructure\Mysql\TeamRepository;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class InfrastructureServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[EasyDB::class] = function (Container $container): EasyDB {
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

        $pimple[IdGeneratorInterface::class] = function (): IdGeneratorInterface {
            return new UuidGenerator();
        };

        $pimple[TeamRepositoryInterface::class] = function (Container $container): TeamRepositoryInterface {
            return new TeamRepository(
                $container[EasyDB::class],
                $container[IdGeneratorInterface::class]
            );
        };

        $pimple[FeedbackRepositoryInterface::class] = function (Container $container): FeedbackRepositoryInterface {
            return new FeedbackRepository(
                $container[EasyDB::class]
            );
        };

        $pimple[ReportRepositoryInterface::class] = function (Container $container): ReportRepositoryInterface {
            return new ReportRepository(
                $container[EasyDB::class]
            );
        };

        $pimple[EmailRepositoryInterface::class] = function (Container $container): EmailRepositoryInterface {
            return new EmailRepository(
                $container[EasyDB::class]
            );
        };

        $pimple[EmailSendingServiceInterface::class] = function (): EmailSendingServiceInterface {
            return new PhpMailEmailSendingService();
        };
    }
}
