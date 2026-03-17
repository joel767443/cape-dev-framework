<?php

namespace WebApp\Http\Middleware;

final class MiddlewareStack
{
    /**
     * @var array<int, string|MiddlewareInterface>
     */
    private array $items = [];

    /**
     * @param string|MiddlewareInterface $mw
     */
    public function push(string|MiddlewareInterface $mw): void
    {
        $this->items[] = $mw;
    }

    /**
     * @return array<int, string|MiddlewareInterface>
     */
    public function all(): array
    {
        return $this->items;
    }
}

