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
                              bool $hasRefreshTokenRequest = true,
                              string $configKey = 'tokenConfigurationsBase',
        #[SensitiveParameter] string|null $token = null,
    ): TokenInterface
    {
        $tokenData = new TokenData(
            url: config("api-client.{$configKey}.accessTokenRequest.url"),
            body: config("api-client.{$configKey}.accessTokenRequest.body"),
            headers: config("api-client.{$configKey}.accessTokenRequest.headers"),
            method: config("api-client.{$configKey}.accessTokenRequest.method"),
            responseNestedKeys: config("api-client.{$configKey}.accessTokenRequest.responseNestedKeys"),
        );
        $refreshTokenData = $hasRefreshTokenRequest ? new RefreshTokenData(
            url: config("api-client.{$configKey}.refreshTokenRequest.url"),
            body: config("api-client.{$configKey}.refreshTokenRequest.body"),
            headers: config("api-client.{$configKey}.refreshTokenRequest.headers"),
            method: config("api-client.{$configKey}.refreshTokenRequest.method"),
            responseNestedKeys: config("api-client.{$configKey}.accessTokenRequest.responseNestedKeys"),

        ) : null;

        return new Token(
            httpClient: resolve(HttpClientInterface::class),
            tokenData: $tokenData,
            refreshTokenData: $refreshTokenData,
            token: $token,
            retries: config("api-client.{$configKey}.tokenRequestsRetries"),
        );
    }
}
