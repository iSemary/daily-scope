<?php

return [
    'enabled' => env('ELASTICSEARCH_ENABLED', false),
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'localhost:9200'),
    ],
    'retries' => 2,
    'handler' => null,
    'connectionParams' => [
        'timeout' => 2,
        'connect_timeout' => 2
    ],
    'index' => [
        'articles' => env('ELASTICSEARCH_ARTICLES_INDEX', 'articles'),
    ]
];
