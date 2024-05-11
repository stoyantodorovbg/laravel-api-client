<?php

namespace Stoyantodorov\ApiClient\Factories;

use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenFromDataFactoryInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenInterface;
use Stoyantodorov\ApiClient\Token;

class TokenFromDataFactory implements TokenFromDataFactoryInterface
{
    public static function create(
        #[SensitiveParameter] TokenData        $tokenData,
        #[SensitiveParameter] RefreshTokenData $refreshTokenData,
                              int              $retries = 3,
        #[SensitiveParameter] string|null      $token = null,
    ): TokenInterface
    {
        return new Token(
            httpClient: resolve(HttpClientInterface::class),
            tokenData: $tokenData,
            refreshTokenData: $refreshTokenData,
            token: $token,
            retries: $retries,
        );
    }
}
