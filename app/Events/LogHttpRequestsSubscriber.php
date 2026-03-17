<?php

namespace App\Events;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebApp\Events\Http\ControllerResolved;
use WebApp\Events\Http\RequestReceived;
use WebApp\Events\Http\ResponseReady;

final class LogHttpRequestsSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestReceived::class => 'onRequestReceived',
            ControllerResolved::class => 'onControllerResolved',
            ResponseReady::class => 'onResponseReady',
        ];
    }

    public function onRequestReceived(RequestReceived $event): void
    {
        $req = $event->request;
        $this->logger->info('http.request_received', [
            'method' => $req->getMethod(),
            'path' => $req->getPathInfo(),
        ]);
    }

    public function onControllerResolved(ControllerResolved $event): void
    {
        $this->logger->info('http.controller_resolved', [
            'controller' => is_string($event->controller) ? $event->controller : gettype($event->controller),
        ]);
    }

    public function onResponseReady(ResponseReady $event): void
    {
        $this->logger->info('http.response_ready', [
            'status' => $event->response->getStatusCode(),
        ]);
    }
}

