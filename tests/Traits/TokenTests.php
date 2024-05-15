<?php

namespace Stoyantodorov\ApiClient\Tests\Traits;

trait TokenTests
{
    private string $tokenValue = 'testTokenValue';
    private string $refreshedTokenValue = 'refreshedTokenValue';
    private string $host = 'https://dummy-host/test';
    private array $headers = ['accept' => 'application/vnd.cloudlx.v1+json'];
    private string $method = 'post';
    private string $accessTokenRequestPath = 'token';
    private array $accessTokenRequestBody = [
        'client_id'     => 'testClient',
        'client_secret' => 'testSecret',
        'grant_type'    => 'client_credentials',
    ];
    private array $accessTokenResponseNestedKeys = ['access_token'];
    private string $refreshTokenRequestPath = 'refresh-token';
    private array $refreshTokenRequestBody = [
        'refresh_token' => 'testRefreshToken',
        'grant_type'    => 'refresh_token',
    ];
    private array $refreshTokenResponseNestedKeys = ['access_token'];

    private function getPath(string $path): string
    {
        return "{$this->host}/{$path}";
    }
}
