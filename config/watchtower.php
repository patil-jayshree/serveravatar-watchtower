<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'application_name' => env('APP_NAME', 'ServerAvatar Watchtower'),

    /*
    |--------------------------------------------------------------------------
    | Short Name
    |--------------------------------------------------------------------------
    */
    'short_name' => env('APP_SHORT_NAME', 'Watchtower'),

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    */
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | API Prefix
    |--------------------------------------------------------------------------
    */
    'api_prefix' => 'api/v1',

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    */
    'default_theme' => 'light',

    /*
    |--------------------------------------------------------------------------
    | Support Email
    |--------------------------------------------------------------------------
    */
    'support_email' => env('SUPPORT_EMAIL', 'support@serveravatar.com'),

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */
    'branding' => [
        // Browser Title
        'browser_title' => 'ServerAvatar Watchtower — Monitor. Debug. Ship.',

        // Meta Tags
        'meta' => [
            'title' => 'ServerAvatar Watchtower — Monitor. Debug. Ship.',
            'description' => 'A powerful observability platform for modern development teams. Monitor requests, track exceptions, analyze database queries, and gain AI-powered insights.',
            'keywords' => 'monitoring, observability, error tracking, performance, debugging, developer tools, SaaS',
        ],

        // OpenGraph
        'og' => [
            'title' => 'ServerAvatar Watchtower',
            'description' => 'A powerful observability platform for modern development teams.',
            'type' => 'website',
            'image' => '/og-image.png',
        ],

        // Twitter Card
        'twitter' => [
            'card' => 'summary_large_image',
            'site' => '@serveravatar',
            'creator' => '@serveravatar',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo URLs
    |--------------------------------------------------------------------------
    */
    'logos' => [
        'dark' => '/logos/brand-logo.png',
        'light' => '/logos/brand-logo.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Slow Request Threshold (ms)
    |--------------------------------------------------------------------------
    |
    | Requests with duration >= this threshold (in milliseconds) will be
    | flagged as slow in the Performance monitoring dashboard.
    |
    */
    'slow_request_threshold' => (int) env('WATCHTOWER_SLOW_REQUEST_THRESHOLD', 1000),

    /*
    |--------------------------------------------------------------------------
    | Slow Command Threshold (ms)
    |--------------------------------------------------------------------------
    |
    | Commands with duration >= this threshold (in milliseconds) will be
    | flagged as slow in the Commands dashboard.
    |
    */
    'command_monitoring' => [
        'slow_threshold_ms' => (int) env('WATCHTOWER_SLOW_COMMAND_THRESHOLD', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'copyright' => '© 2026 ServerAvatar Watchtower. All rights reserved.',
        'links' => [
            'documentation' => false,
            'privacy' => false,
            'terms' => false,
        ],
    ],

];
