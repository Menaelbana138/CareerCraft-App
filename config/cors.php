<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS — CareerCraft Backend
    |--------------------------------------------------------------------------
    | CORS_ALLOWED_ORIGINS: قائمة مفصولة بفواصل، مثال:
    |   https://your-app.vercel.app,https://www.example.com
    | لو المتغير غير موجود أو فاضي → يُعامل كـ * (مناسب للتطوير؛ قيّدي في الإنتاج).
    | تطبيقات الموبايل الأصلية لا تستخدم CORS في المتصفح.
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) (env('CORS_ALLOWED_ORIGINS') ?: '*'))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
