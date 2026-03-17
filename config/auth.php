<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: (getenv('APP_KEY') ?: ''),
        'issuer' => getenv('JWT_ISSUER') ?: 'webapp',
        'ttl' => (int) (getenv('JWT_TTL') ?: 3600),
    ],
];

