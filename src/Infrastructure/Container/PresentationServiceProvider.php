<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Container;

use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;
use MeetMatt\Colla\Mood\Domain\Email\TeamMailerService;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Identity\IdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use MeetMatt\Colla\Mood\Presentation\Console\EmailFeedbackLinksCommand;
use MeetMatt\Colla\Mood\Presentation\ConsoleApplication;
use MeetMatt\Colla\Mood\Presentation\Http\Feedback\FeedbackFormAction;
use MeetMatt\Colla\Mood\Presentation\Http\Feedback\FeedbackHistoryAction;
use MeetMatt\Colla\Mood\Presentation\Http\Feedback\SaveFeedbackAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\CreateTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\FindTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\IndexAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\SaveTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\Team\ShowTeamAction;
use MeetMatt\Colla\Mood\Presentation\WebApplication;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Psr\Container\ContainerInterface;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

class PresentationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[WebApplication::class] = function (Container $container) {
            $application = new WebApplication($container);

            foreach ($container['routes'] as $route) {
                $application->map([$route['method']], $route['pattern'], $route['action'])->setName($route['name']);
            }

            return $application;
        };

        $pimple[ConsoleApplication::class] = function (ContainerInterface $container) {
            $application = new ConsoleApplication('Mood', 'dev-master');

            $application->setContainer($container);
            $commands = [];
            foreach ($container['commands'] as $command) {
                $commands[] = $container[$command];
            }

            $application->addCommands($commands);
            $application->setWebApplication($container[WebApplication::class]);

            return $application;
        };

        $pimple[Twig::class] = function (Container $container) {
            $settings = $container['settings']['twig'];
            $view     = new Twig(
                $settings['templates'],
                [
                    'debug'       => $settings['debug'],
                    'auto_reload' => $settings['auto_reload'],
                    'cache'       => $settings['cache'],
                ]
            );

            /** @var RouterInterface $router */
            $router = $container['router'];
            $uri    = Uri::createFromEnvironment(new Environment($_SERVER));
            $twigExtension = new TwigExtension($router, $uri);
            $twigExtension->setBaseUrl($settings['base_url']);
            $view->addExtension($twigExtension);

            return $view;
        };

        $pimple[IndexAction::class] = function (Container $container) {
            return new IndexAction($container[Twig::class]);
        };

        $pimple[CreateTeamAction::class] = function (Container $container) {
            return new CreateTeamAction(
                $container[TeamRepositoryInterface::class],
                $container['router']
            );
        };

        $pimple[FindTeamAction::class] = function (Container $container) {
            return new FindTeamAction(
                $container['router']
            );
        };

        $pimple[ShowTeamAction::class] = function (Container $container) {
            return new ShowTeamAction(
                $container[TeamRepositoryInterface::class],
                $container[EmailRepositoryInterface::class],
                $container[Twig::class]
            );
        };

        $pimple[SaveTeamAction::class] = function (Container $container) {
            return new SaveTeamAction(
                $container[TeamRepositoryInterface::class],
                $container[EmailRepositoryInterface::class],
                $container['router']
            );
        };

        $pimple[FeedbackHistoryAction::class] = function (Container $container) {
            return new FeedbackHistoryAction(
                $container[TeamRepositoryInterface::class],
                $container[FeedbackRepositoryInterface::class],
                $container[Twig::class]
            );
        };

        $pimple[FeedbackFormAction::class] = function (Container $container) {
            return new FeedbackFormAction(
                $container[FeedbackRepositoryInterface::class],
                $container[TeamRepositoryInterface::class],
                $container[Twig::class]
            );
        };

        $pimple[SaveFeedbackAction::class] = function (Container $container) {
            return new SaveFeedbackAction(
                $container[FeedbackRepositoryInterface::class],
                $container['router']
            );
        };

        $pimple[EmailFeedbackLinksCommand::class] = function (Container $container) {
            return new EmailFeedbackLinksCommand(
                new TeamMailerService(
                    $container[TeamRepositoryInterface::class],
                    $container[EmailRepositoryInterface::class],
                    $container[FeedbackRepositoryInterface::class],
                    $container[IdGeneratorInterface::class],
                    $container[EmailSendingServiceInterface::class],
                    $container[Twig::class]
                )
            );
        };
    }
}