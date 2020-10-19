<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'X-pagination-current_page',
        'X-pagination-last_page',
        'X-pagination-per_page',
        'X-pagination-from',
        'X-pagination-to',
        'X-pagination-total',
        'X-pagination-first_page_url',
        'X-pagination-last_page_url',
        'X-pagination-prev_page_url',
        'X-pagination-next_page_url',
    ],

    'max_age' => 0,

    'supports_credentials' => true,

];
