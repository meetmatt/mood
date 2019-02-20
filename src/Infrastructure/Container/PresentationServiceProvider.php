<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Container;

use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use MeetMatt\Colla\Mood\Presentation\Http\CreateTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\FindTeamAction;
use MeetMatt\Colla\Mood\Presentation\Http\IndexAction;
use MeetMatt\Colla\Mood\Presentation\Http\ShowTeamAction;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

class PresentationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
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
            $view->addExtension(new TwigExtension($router, $uri));

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
                $container[Twig::class]
            );
        };
    }
}