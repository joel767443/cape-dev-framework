<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Predis\Client as PredisClient;
use Psr\Container\ContainerInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Queue\Dispatcher;
use WebApp\Queue\QueueInterface;
use WebApp\Queue\RedisQueue;

final class QueueServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            PredisClient::class => \DI\factory(function (): PredisClient {
                $dsn = (string) config('queue.redis.dsn', '');
                if ($dsn === '') {
                    $dsn = 'tcp://127.0.0.1:6379';
                }
                return new PredisClient($dsn);
            }),

            QueueInterface::class => \DI\factory(function (ContainerInterface $c): QueueInterface {
                $redis = $c->get(PredisClient::class);
                $prefix = (string) config('queue.prefix', 'webapp_queue');
                $queue = (string) config('queue.queue', 'default');
                return new RedisQueue($redis, $prefix, $queue);
            }),

            Dispatcher::class => \DI\autowire(Dispatcher::class),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
    }
}

