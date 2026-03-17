<?php

return [
    'subscribers' => [
        \App\Events\LogHttpRequestsSubscriber::class,
        \App\Events\UserRegisteredSubscriber::class,
    ],
];

