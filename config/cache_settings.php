<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Settings for Home APIs
    |--------------------------------------------------------------------------
    |
    | This file contains cache configuration settings for the home APIs
    | including TTL (Time To Live) values and cache key patterns.
    |
    */

    'home' => [
        'top_headings' => [
            'ttl' => env('CACHE_TOP_HEADINGS_TTL', 900), // 15 minutes
            'key_prefix' => 'top_headings',
        ],
        'preferred_articles' => [
            'ttl' => env('CACHE_PREFERRED_ARTICLES_TTL', 600), // 10 minutes
            'key_prefix' => 'preferred_articles',
        ],
        'user_categories' => [
            'ttl' => env('CACHE_USER_CATEGORIES_TTL', 1800), // 30 minutes
            'key_prefix' => 'user_preferred_categories',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Invalidation Settings
    |--------------------------------------------------------------------------
    |
    | Settings for when caches should be automatically invalidated
    |
    */

    'invalidation' => [
        'on_article_update' => env('CACHE_INVALIDATE_ON_ARTICLE_UPDATE', true),
        'on_user_interest_change' => env('CACHE_INVALIDATE_ON_USER_INTEREST_CHANGE', true),
        'on_category_update' => env('CACHE_INVALIDATE_ON_CATEGORY_UPDATE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Driver Settings
    |--------------------------------------------------------------------------
    |
    | Specific settings for different cache drivers
    |
    */

    'drivers' => [
        'redis' => [
            'key_prefix' => env('REDIS_PREFIX', 'daily-scope-cache-'),
            'serialize' => true,
        ],
        'database' => [
            'table' => 'cache',
        ],
    ],
];
