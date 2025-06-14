<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173', // Frontend React kamu
        'http://127.0.0.1:5173', // Alternatif jika frontend pakai IP ini
        'http://ukirebackend.test', // Virtual host Laragon untuk backend ini
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
