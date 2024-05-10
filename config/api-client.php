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
        ],
        'refreshTokenRequest' => [
            'url' => '',
            'method' => 'post',
            'body' => [],
            'headers' => [],
        ],
        'tokenRequestsRetries' => 3,
    ]
];
