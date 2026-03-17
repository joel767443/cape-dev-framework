<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Predis\Client as PredisClient;
use Psr\Container\ContainerInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Queue\Dispatcher;
use WebApp\Queue\QueueInterface;
use WebApp\Queue\RedisQueue;
use function DI\autowire;
use function DI\factory;

/**
 *
 */
final class QueueServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            PredisClient::class => factory(function (): PredisClient {
                $dsn = (string) config('queue.redis.dsn', '');
                if ($dsn === '') {
                    $dsn = 'tcp://127.0.0.1:6379';
                }
                return new PredisClient($dsn);
            }),

            QueueInterface::class => factory(function (ContainerInterface $c): QueueInterface {
                $redis = $c->get(PredisClient::class);
                $prefix = (string) config('queue.prefix', 'webapp_queue');
                $queue = (string) config('queue.queue', 'default');
                return new RedisQueue($redis, $prefix, $queue);
            }),

            Dispatcher::class => autowire(Dispatcher::class),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void
    {
    }
}

