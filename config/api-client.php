<?php

return [
    'events' => [
        'onSuccess' => true,
        'onRequestException' => true,
        'onConnectionException' => true,
    ],
    'logs' => [
        'onRequestException' => true,
        'onConnectionException' => true,
    ],
    'tokenConfigurationsBase' => [
        'accessTokenRequest' => [
            'url' => '',
            'method' => 'post',
            'body' => [],
            'headers' => [],
            'responseNestedKeys' => ['access_token']
        ],
        'refreshTokenRequest' => [
            'url' => '',
            'method' => 'post',
            'body' => [],
            'headers' => [],
            'responseNestedKeys' => ['access_token']
        ],
        'tokenRequestsRetries' => 3,
    ]
];
