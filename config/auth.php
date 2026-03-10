<?php

return [
    'defaults' => [
        'guard' => 'usuarios',
        'passwords' => 'usuarios',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        // Guard principal do SAPRO
        'usuarios' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        // Provider usando nosso model Usuario
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
