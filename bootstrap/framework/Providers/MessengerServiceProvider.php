<?php

namespace WebApp\Providers;

use App\Messenger\Handlers\LogMessageHandler;
use App\Messenger\Messages\LogMessage;
use DI\ContainerBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
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
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\TransportInterface;
use WebApp\Container\ServiceProviderInterface;
use function DI\autowire;
use function DI\factory;

/**
 *
 */
final class MessengerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            // Example handler (also doubles as a smoke-test target).
            LogMessageHandler::class => autowire(LogMessageHandler::class),

            TransportInterface::class => factory(function (): TransportInterface {
                $transport = (string) config('queue.messenger.transport', 'sync');

                if ($transport === 'in_memory') {
                    return new InMemoryTransport();
                }

                // sync (default): no queue; messages are handled immediately.
                return new class implements TransportInterface {
                    /**
                     * @param Envelope $envelope
                     * @return Envelope
                     */
                    public function send(Envelope $envelope): Envelope
                    {
                        return $envelope;
                    }

                    /**
                     * @return iterable
                     */
                    public function get(): iterable
                    {
                        return [];
                    }

                    /**
                     * @param Envelope $envelope
                     * @return void
                     */
                    public function ack(Envelope $envelope): void
                    {
                    }

                    /**
                     * @param Envelope $envelope
                     * @return void
                     */
                    public function reject(Envelope $envelope): void
                    {
                    }
                };
            }),

            ReceiverInterface::class => factory(function (ContainerInterface $c): ReceiverInterface {
                $transport = $c->get(TransportInterface::class);
                if ($transport instanceof ReceiverInterface) {
                    return $transport;
                }

                // If transport isn't a receiver (sync), provide an empty receiver.
                return new class implements ReceiverInterface {
                    /**
                     * @return iterable
                     */
                    public function get(): iterable
                    {
                        return [];
                    }

                    /**
                     * @param Envelope $envelope
                     * @return void
                     */
                    public function ack(Envelope $envelope): void
                    {
                    }

                    /**
                     * @param Envelope $envelope
                     * @return void
                     */
                    public function reject(Envelope $envelope): void
                    {
                    }
                };
            }),

            MessageBusInterface::class => factory(function (ContainerInterface $c): MessageBusInterface {
                $transport = (string) config('queue.messenger.transport', 'sync');

                /** @var TransportInterface $sender */
                $sender = $c->get(TransportInterface::class);

                $senderContainer = new class($sender) implements ContainerInterface {
                    /**
                     * @param SenderInterface $sender
                     */
                    public function __construct(private readonly SenderInterface $sender)
                    {
                    }

                    /**
                     * @param string $id
                     * @return SenderInterface
                     */
                    public function get(string $id)
                    {
                        return $this->sender;
                    }

                    /**
                     * @param string $id
                     * @return bool
                     */
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

    /**
     * @param ContainerInterface $container
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(ContainerInterface $container): void
    {
        // Ensure event dispatcher exists for Worker usage (commands will fetch it).
        $container->get(EventDispatcherInterface::class);
    }
}

