<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => in_array(strtolower((string) getenv('APP_DEBUG')), ['1', 'true', 'yes', 'on'], true),
];

