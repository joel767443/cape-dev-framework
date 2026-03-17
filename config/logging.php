<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    'path' => getenv('LOG_PATH') ?: 'storage/logs/app.log',
    'level' => getenv('LOG_LEVEL') ?: 'info',
];

