<?php

return [
    'token' => env('SERVICE_TOKEN'),

    'base' => [
        'url' => env('BASE_SERVICE_URL'),
        'token' => env('BASE_SERVICE_TOKEN'),
        'retry' => 3,
        'retry_timeout' => 30
    ]
];
