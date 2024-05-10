<?php

namespace Stoyantodorov\ApiClient\Interfaces;

use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Data\TokenData;

interface TokenFromDataFactoryInterface
{
    /**
     * Instantiate TokenInterface
     * When receives $token it is set in TokenInterface instance
     *
     * @param string|null      $token
     * @param TokenData        $tokenRequestData
     * @param RefreshTokenData $refreshTokenRequestData
     * @param int              $retries
     * @return TokenInterface
     */
    public static function create(
        string|null                            $token,
        #[SensitiveParameter] TokenData        $tokenRequestData,
        #[SensitiveParameter] RefreshTokenData $refreshTokenRequestData,
                              int              $retries = 3,
    ): TokenInterface;
}
