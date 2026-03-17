<?php

namespace WebApp\Console\Support;

/**
 *
 */
final class Naming
{
    /**
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $value = str_replace(['-', '_'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        $parts = array_filter(explode(' ', $value), static fn ($p) => $p !== '');
        $parts = array_map(static fn ($p) => ucfirst(strtolower($p)), $parts);
        return implode('', $parts);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function normalizeClass(string $name): string
    {
        $name = trim($name);
        $name = trim($name, "\\/ \t\n\r\0\x0B");
        $name = str_replace('/', '\\', $name);
        return preg_replace('/\\\\+/', '\\', $name) ?? $name;
    }

    /**
     * @return array{namespace: string, class: string}
     */
    public static function splitNamespaceAndClass(string $fqcn, string $defaultNamespace): array
    {
        $fqcn = self::normalizeClass($fqcn);
        if ($fqcn === '') {
            return ['namespace' => $defaultNamespace, 'class' => ''];
        }

        if (str_contains($fqcn, '\\')) {
            $parts = explode('\\', $fqcn);
            $class = array_pop($parts) ?: '';
            $ns = implode('\\', $parts);
            return ['namespace' => $ns, 'class' => $class];
        }

        return ['namespace' => $defaultNamespace, 'class' => $fqcn];
    }
}

