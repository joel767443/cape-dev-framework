<?php

use WebApp\Config\ConfigRepository;

if (!function_exists('config')) {
    /**
     * @param mixed $default
     */
    function config(string $key, mixed $default = null): mixed
    {
        if (!isset($GLOBALS['__webapp_config']) || !$GLOBALS['__webapp_config'] instanceof ConfigRepository) {
            return $default;
        }

        return $GLOBALS['__webapp_config']->get($key, $default);
    }
}

