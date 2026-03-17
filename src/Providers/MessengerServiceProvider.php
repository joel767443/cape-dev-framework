<?php

namespace WebApp\Providers;

use App\Messenger\Handlers\LogMessageHandler;
use App\Messenger\Messages\LogMessage;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\TransportInterface;
use WebApp\Container\ServiceProviderInterface;

final class MessengerServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            // Example handler (also doubles as a smoke-test target).
            LogMessageHandler::class => \DI\autowire(LogMessageHandler::class),

            TransportInterface::class => \DI\factory(function (): TransportInterface {
                $transport = (string) config('queue.messenger.transport', 'sync');

                if ($transport === 'in_memory') {
                    return new InMemoryTransport();
                }

                // sync (default): no queue; messages are handled immediately.
                return new class implements TransportInterface {
                    public function send(Envelope $envelope): Envelope
                    {
                        return $envelope;
                    }

                    public function get(): iterable
                    {
                        return [];
                    }

                    public function ack(Envelope $envelope): void
                    {
                    }

                    public function reject(Envelope $envelope): void
                    {
                    }
                };
            }),

            ReceiverInterface::class => \DI\factory(function (ContainerInterface $c): ReceiverInterface {
                $transport = $c->get(TransportInterface::class);
                if ($transport instanceof ReceiverInterface) {
                    return $transport;
                }

                // If transport isn't a receiver (sync), provide an empty receiver.
                return new class implements ReceiverInterface {
                    public function get(): iterable
                    {
                        return [];
                    }
                    public function ack(Envelope $envelope): void
                    {
                    }
                    public function reject(Envelope $envelope): void
                    {
                    }
                };
            }),

            MessageBusInterface::class => \DI\factory(function (ContainerInterface $c): MessageBusInterface {
                $transport = (string) config('queue.messenger.transport', 'sync');

                /** @var TransportInterface $sender */
                $sender = $c->get(TransportInterface::class);

                $senderContainer = new class($sender) implements ContainerInterface {
                    public function __construct(private readonly \Symfony\Component\Messenger\Transport\Sender\SenderInterface $sender)
                    {
                    }

                    public function get(string $id)
                    {
                        return $this->sender;
                    }

                    public function has(string $id): bool
                    {
                        return true;
                    }
                };

                $sendersLocator = new SendersLocator(
                    // Map message class to sender(s) when using an async-ish transport.
                    $transport === 'in_memory' ? [LogMessage::class => ['async']] : [],
                    $senderContainer
                );

                $handlersLocator = new HandlersLocator([
                    LogMessage::class => [
                        new HandlerDescriptor([$c->get(LogMessageHandler::class), '__invoke']),
                    ],
                ]);

                $middleware = [
                    new SendMessageMiddleware($sendersLocator),
                    new HandleMessageMiddleware($handlersLocator),
                ];

                return new MessageBus($middleware);
            }),
        ]);
    }

    public function boot(ContainerInterface $container): void
    {
        // Ensure event dispatcher exists for Worker usage (commands will fetch it).
        $container->get(EventDispatcherInterface::class);
    }
}

