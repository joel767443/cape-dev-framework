<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    /**
     * Base paths where views live.
     *
     * Templates are referenced using dot-notation, e.g.:
     * - emails.welcome -> resources/views/emails/welcome.blade.php
     */
    'paths' => [
        dirname(__DIR__) . '/resources/views',
    ],

    /**
     * Where compiled Blade templates are stored.
     */
    'compiled' => dirname(__DIR__) . '/storage/framework/views',
];

