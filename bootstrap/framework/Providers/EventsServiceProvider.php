<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebApp\Container\ServiceProviderInterface;
use function DI\factory;

/**
 *
 */
final class EventsServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            EventDispatcherInterface::class => factory(function (): EventDispatcherInterface {
                return new EventDispatcher();
            }),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(ContainerInterface $container): void
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get(EventDispatcherInterface::class);

        $subscribers = config('events.subscribers', []);
        if (!is_array($subscribers) || $subscribers === []) {
            return;
        }

        foreach ($subscribers as $subscriberClass) {
            if (!is_string($subscriberClass) || trim($subscriberClass) === '') {
                continue;
            }

            $subscriber = $container->get($subscriberClass);
            if ($subscriber instanceof EventSubscriberInterface) {
                $dispatcher->addSubscriber($subscriber);
            }
        }
    }
}

