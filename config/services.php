<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'api' => [
        'base_url' => env('API_BASE_URL'),
    ],

    'pusher' => [
        'key'    => env('PUSHER_APP_KEY', '6c9a41907726c1faf16a'),
        'secret' => env('PUSHER_APP_SECRET', ''),
        'app_id' => env('PUSHER_APP_ID', ''),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
            'useTLS'  => true,
        ],
    ],
];
