<?php

namespace WebApp\Config;

use InvalidArgumentException;

final class ConfigRepository
{
    /**
     * @param array<string, mixed> $items
     */
    public function __construct(private array $items = [])
    {
    }

    /**
     * @param mixed $default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $key = trim($key);
        if ($key === '') {
            throw new InvalidArgumentException('Config key is required.');
        }

        $segments = explode('.', $key);
        $value = $this->items;

        foreach ($segments as $seg) {
            if (!is_array($value) || !array_key_exists($seg, $value)) {
                return $default;
            }
            $value = $value[$seg];
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMany(array $values): void
    {
        $this->items = array_replace_recursive($this->items, $values);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }
}

