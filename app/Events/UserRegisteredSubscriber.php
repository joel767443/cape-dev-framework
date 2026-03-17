<?php

namespace App\Events;

use App\Console\Jobs\SendWelcomeJob;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebApp\Queue\Dispatcher;

/**
 *
 */
final class UserRegisteredSubscriber implements EventSubscriberInterface
{
    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(private readonly Dispatcher $dispatcher)
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserRegistered::class => 'onUserRegistered',
        ];
    }

    /**
     * @param UserRegistered $event
     * @return void
     */
    public function onUserRegistered(UserRegistered $event): void
    {
        $user = $event->user;
        $this->dispatcher->dispatch(new SendWelcomeJob((string) $user->getKey(), (string) $user->email));
    }
}

