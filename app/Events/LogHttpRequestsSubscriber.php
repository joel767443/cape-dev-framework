<?php

namespace App\Events;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebApp\Events\Http\ControllerResolved;
use WebApp\Events\Http\RequestReceived;
use WebApp\Events\Http\ResponseReady;

/**
 *
 */
final class LogHttpRequestsSubscriber implements EventSubscriberInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestReceived::class => 'onRequestReceived',
            ControllerResolved::class => 'onControllerResolved',
            ResponseReady::class => 'onResponseReady',
        ];
    }

    /**
     * @param RequestReceived $event
     * @return void
     */
    public function onRequestReceived(RequestReceived $event): void
    {
        $req = $event->request;
        $this->logger->info('http.request_received', [
            'method' => $req->getMethod(),
            'path' => $req->getPathInfo(),
        ]);
    }

    /**
     * @param ControllerResolved $event
     * @return void
     */
    public function onControllerResolved(ControllerResolved $event): void
    {
        $this->logger->info('http.controller_resolved', [
            'controller' => is_string($event->controller) ? $event->controller : gettype($event->controller),
        ]);
    }

    /**
     * @param ResponseReady $event
     * @return void
     */
    public function onResponseReady(ResponseReady $event): void
    {
        $this->logger->info('http.response_ready', [
            'status' => $event->response->getStatusCode(),
        ]);
    }
}

