<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
return [
    'pagination' => [
        'perPage' => (int) (getenv('ORM_PER_PAGE') ?: 20),
    ],
];

