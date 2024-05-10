<?php

namespace Stoyantodorov\ApiClient\Factories;

use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenFromConfigFactoryInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenInterface;
use Stoyantodorov\ApiClient\Token;

class TokenFromConfigFactory implements TokenFromConfigFactoryInterface
{
    public static function create(
        #[SensitiveParameter] string|null $token,
                              string $configKey = 'tokenConfigurationsBase'
    ): TokenInterface
    {
        return new Token(
            httpClient: resolve(HttpClientInterface::class),
            tokenData: new TokenData(
                url: config('api-client.accessTokenRequest.url'),
                body: config('api-client.accessTokenRequest.body'),
                headers: config('api-client.accessTokenRequest.headers'),
                method: config('api-client.accessTokenRequest.method'),
                responseNestedKeys: config('api-client.accessTokenRequest.responseNestedKeys'),
            ),
            refreshTokenData: new RefreshTokenData(
                url: config('api-client.refreshTokenRequest.url'),
                body: config('api-client.refreshTokenRequest.body'),
                headers: config('api-client.refreshTokenRequest.headers'),
                method: config('api-client.refreshTokenRequest.method'),
                responseNestedKeys: config('api-client.accessTokenRequest.responseNestedKeys'),

            ),
            token: $token,
            retries: config('api-client.tokenRequestsRetries'),
        );
    }
}
