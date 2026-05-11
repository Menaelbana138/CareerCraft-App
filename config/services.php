<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth
    | Used by GoogleIdTokenService to verify mobile Google Login tokens.
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Internal AI Service (Flask)
    | If AI_SERVICE_URL is set, Laravel calls Flask instead of OpenAI.
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'url'     => env('AI_SERVICE_URL'),
        'timeout' => env('AI_SERVICE_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI (fallback when Flask AI service is not running)
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'key'   => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
    ],

];
