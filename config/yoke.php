<?php

return [
    'tenant' => [
        'scheme' => 'auth',
        'guard' => 'tenant'
    ],
    'user' => [
        'scheme' => 'auth',
        'guard' => 'user'
    ],
    'descriptor' => App\Yoke\Descriptor::class
];
