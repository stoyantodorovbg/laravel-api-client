<?php

namespace Stoyantodorov\ApiClient\Factories;

use Stoyantodorov\ApiClient\Data\TokenRequestData;
use Stoyantodorov\ApiClient\Data\RefreshTokenRequestData;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenFromConfigFactoryInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenInterface;
use Stoyantodorov\ApiClient\Token;

class TokenFromConfigFactory implements TokenFromConfigFactoryInterface
{
    public static function create(string|null $token, string $configKey = 'tokenConfigurationsBase'): TokenInterface
    {
        return new Token(
            httpClient: resolve(HttpClientInterface::class),
            tokenRequestData: new TokenRequestData(
                url: config('api-client.accessTokenRequest.url'),
                body: config('api-client.accessTokenRequest.body'),
                headers: config('api-client.accessTokenRequest.headers'),
                method: config('api-client.accessTokenRequest.method'),
            ),
            refreshTokenRequestData: new RefreshTokenRequestData(
                url: config('api-client.refreshTokenRequest.url'),
                body: config('api-client.refreshTokenRequest.body'),
                headers: config('api-client.refreshTokenRequest.headers'),
                method: config('api-client.refreshTokenRequest.method'),
            ),
            token: $token,
            retries: config('api-client.tokenRequestsRetries'),
        );
    }
}
