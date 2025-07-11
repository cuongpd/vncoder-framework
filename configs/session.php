<?php

return [

    'driver' => env('SESSION_DRIVER', 'file'), // "file", "cookie", "database", "apc", "memcached", "redis", "dynamodb", "array"
    'lifetime' => env('SESSION_LIFETIME', 10080),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION', null), // database or redis
    'table' => '__sessions',
    'store' => env('SESSION_STORE', null), // "apc", "dynamodb", "memcached", "redis"
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'vn_session_cookie'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => true,
    'same_site' => 'lax', // "lax", "strict", "none", null

];
