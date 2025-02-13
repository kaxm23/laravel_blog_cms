<?php

return [
    'hosts' => [
        [
            'host' => env('ELASTICSEARCH_HOST', 'localhost'),
            'port' => env('ELASTICSEARCH_PORT', 9200),
            'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
        ],
    ],
    'indices' => [
        'settings' => [
            'number_of_shards' => 1,
            'number_of_replicas' => 1,
            'analysis' => [
                'analyzer' => [
                    'custom_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase', 'stop', 'snowball']
                    ]
                ]
            ]
        ]
    ]
];