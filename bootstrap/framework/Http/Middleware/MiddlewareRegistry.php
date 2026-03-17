<?php

namespace WebApp\Http\Middleware;

use InvalidArgumentException;

final class MiddlewareRegistry
{
    /**
     * @var array<string, class-string<MiddlewareInterface>>
     */
    private array $map = [];

    /**
     * @param string $name
     * @param class-string<MiddlewareInterface> $class
     */
    public function alias(string $name, string $class): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('Middleware alias name is required.');
        }
        $this->map[$name] = $class;
    }

    /**
     * @return class-string<MiddlewareInterface>
     */
    public function resolve(string $name): string
    {
        if (!isset($this->map[$name])) {
            throw new InvalidArgumentException("Unknown middleware alias: {$name}");
        }
        return $this->map[$name];
    }
}

