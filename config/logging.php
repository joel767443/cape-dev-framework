<?php

return [
    'path' => getenv('LOG_PATH') ?: 'storage/logs/app.log',
    'level' => getenv('LOG_LEVEL') ?: 'info',
];

