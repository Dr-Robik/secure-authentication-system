<?php

return [

    'secret' => env('JWT_SECRET'),

    'keys' => [
        'public' => env('JWT_PUBLIC_KEY'),
        'private' => env('JWT_PRIVATE_KEY'),
        'passphrase' => env('JWT_PASSPHRASE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | TOKEN TTL (IMPORTANT)
    |--------------------------------------------------------------------------
    | Token expires after X minutes
    | Shorter = more secure
    */
    'ttl' => (int) env('JWT_TTL', 60), // reduced to 60 minutes

    /*
    |--------------------------------------------------------------------------
    | REFRESH WINDOW
    |--------------------------------------------------------------------------
    | User can refresh token within this time
    */
    'refresh_iat' => env('JWT_REFRESH_IAT', true), //  enables rolling sessions
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 1440), //  1 day instead of 2 weeks

    /*
    |--------------------------------------------------------------------------
    | ALGORITHM
    |--------------------------------------------------------------------------
    */
    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | REQUIRED CLAIMS
    |--------------------------------------------------------------------------
    */
    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],

    'persistent_claims' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | LOCK SUBJECT (SECURITY)
    |--------------------------------------------------------------------------
    | Prevents token confusion between models
    */
    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | CLOCK SKEW
    |--------------------------------------------------------------------------
    */
    'leeway' => (int) env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | TOKEN BLACKLIST
    |--------------------------------------------------------------------------
    | This allows:
    | - logout() to invalidate token
    | - stolen token becomes useless
    */
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | BLACKLIST GRACE PERIOD
    |--------------------------------------------------------------------------
    | Prevents issues with parallel requests
    */
    'blacklist_grace_period' => (int) env('JWT_BLACKLIST_GRACE_PERIOD', 2),

    /*
    |--------------------------------------------------------------------------
    | DEBUGGING (KEEP TRUE FOR NOW)
    |--------------------------------------------------------------------------
    */
    'show_black_list_exception' => env('JWT_SHOW_BLACKLIST_EXCEPTION', true),

    /*
    |--------------------------------------------------------------------------
    | COOKIES
    |--------------------------------------------------------------------------
    */
    'decrypt_cookies' => false,
    'cookie_key_name' => 'token',

    /*
    |--------------------------------------------------------------------------
    | PROVIDERS
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'jwt' => PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth' => PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => PHPOpenSourceSaver\JWTAuth\Providers\Storage\Illuminate::class,
    ],
];
