<?php

namespace WebApp\Providers;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebApp\Container\ServiceProviderInterface;

final class EventsServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            EventDispatcherInterface::class => \DI\factory(function (): EventDispatcherInterface {
                return new EventDispatcher();
            }),
        ]);
    }

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

