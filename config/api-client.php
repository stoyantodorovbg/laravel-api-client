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
            'body' => [],
            'headers' => [],
            'responseNestedKeys' => ['access_token'],
            'method' => 'post',
            'dispatchEvent' => true,
        ],
        'refreshTokenRequest' => [
            'url' => '',
            'body' => [],
            'headers' => [],
            'responseNestedKeys' => ['access_token'],
            'method' => 'post',
            'dispatchEvent' => true,
        ],
        'tokenRequestsRetries' => 3,
    ],
];
