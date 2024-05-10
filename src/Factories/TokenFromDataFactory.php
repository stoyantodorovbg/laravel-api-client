<?php

namespace Stoyantodorov\ApiClient\Factories;

use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\TokenRequestData;
use Stoyantodorov\ApiClient\Data\RefreshTokenRequestData;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenFromDataFactoryInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenInterface;
use Stoyantodorov\ApiClient\Token;

class TokenFromDataFactory implements TokenFromDataFactoryInterface
{
    public static function create(
        string|null $token,
        #[SensitiveParameter] TokenRequestData $tokenRequestData,
        #[SensitiveParameter] RefreshTokenRequestData $refreshTokenRequestData,
        int $retries = 3,
    ): TokenInterface
    {
        return new Token(
            httpClient: resolve(HttpClientInterface::class),
            tokenRequestData: $tokenRequestData,
            refreshTokenRequestData: $refreshTokenRequestData,
            token: $token,
            retries: $retries,
        );
    }
}
