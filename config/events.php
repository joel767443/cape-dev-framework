<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    'subscribers' => [
        \App\Events\LogHttpRequestsSubscriber::class,
        \App\Events\UserRegisteredSubscriber::class,
    ],
];

