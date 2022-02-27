<?php

return [
    'api_url' => env('SCANNER_API_URL', 'http://scanner'),
    'type' => 'lumen',

    // уникальный id сканера
    'unique_id' => env('SCANNER_UNIQUE_ID', ''),

    'auth_token' => env('SCANNER_AUTH_TOKEN', ''),

    // сколько сканер берет задач
    'limit' => env('SCANNER_LIMIT', 10),
];
