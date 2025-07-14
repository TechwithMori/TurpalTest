<?php

return [
    'heavenly_tours' => [
        'base_url' => env('HEAVENLY_TOURS_BASE_URL', 'https://mock.turpal.com'),
        'api_key' => env('HEAVENLY_TOURS_API_KEY', ''),
        'timeout' => env('HEAVENLY_TOURS_TIMEOUT', 30),
        'cache_duration' => env('HEAVENLY_TOURS_CACHE_DURATION', 3600),
        'enabled' => env('HEAVENLY_TOURS_ENABLED', true),
    ],

    'default_source' => env('DEFAULT_EXPERIENCE_SOURCE', 'local'),
];
