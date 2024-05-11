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
     * @param TokenData        $tokenData
     * @param RefreshTokenData $refreshTokenData
     * @param int              $retries
     * @param string|null $token = null
     * @return TokenInterface
     */
    public static function create(
        #[SensitiveParameter] TokenData        $tokenData,
        #[SensitiveParameter] RefreshTokenData $refreshTokenData,
                              int              $retries = 3,
        string|null                            $token = null,
    ): TokenInterface;
}
