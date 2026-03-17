<?php

return [
    'pagination' => [
        'perPage' => (int) (getenv('ORM_PER_PAGE') ?: 20),
    ],
];

