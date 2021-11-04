<?php

return [
    'name' => 'Movies',
    'api_url' => env('MOVIES_API_URL', 'https://api.themoviedb.org/3'),
    'api_key' => env('MOVIES_API_KEY', ''),
    'interval_minutes' => env('MOVIES_INTERVAL_MINUTES', 30),
    'seed_count' => env('MOVIES_SEED_COUNT', 20),
];
