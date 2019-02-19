<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Container;

use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\App;

class InfrastructureServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[App::class] = function (Container $container) {
            $slim = new App($container);

            foreach ($container['routes'] as $route) {
                $slim->map([$route['method']], $route['pattern'], $route['action']);
            }

            $slim->add($container[ErrorResponseMiddleware::class]);

            return $slim;
        };

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

        $pimple[ErrorResponseMiddleware::class] = function () {
            return new ErrorResponseMiddleware();
        };

        $pimple[RandomIdGeneratorInterface::class] = function () {
            return new RandomIdGenerator();
        };

    }
}