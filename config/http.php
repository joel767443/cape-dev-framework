<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    // Optional base URI for outbound requests, e.g. https://api.example.com
    'base_uri' => getenv('HTTP_BASE_URI') ?: '',

    // Default timeout (seconds).
    'timeout' => (float) (getenv('HTTP_TIMEOUT') ?: 10),
];

