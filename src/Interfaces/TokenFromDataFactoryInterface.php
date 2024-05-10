<?php

namespace Stoyantodorov\ApiClient\Interfaces;

use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\RefreshTokenRequestData;
use Stoyantodorov\ApiClient\Data\TokenRequestData;

interface TokenFromDataFactoryInterface
{
    /**
     * Instantiate TokenInterface
     * When receives $token it is set in TokenInterface instance
     *
     * @param string|null             $token
     * @param TokenRequestData        $tokenRequestData
     * @param RefreshTokenRequestData $refreshTokenRequestData
     * @param int                     $retries
     * @return TokenInterface
     */
    public static function create(
        string|null $token,
        #[SensitiveParameter] TokenRequestData $tokenRequestData,
        #[SensitiveParameter] RefreshTokenRequestData $refreshTokenRequestData,
                              int $retries = 3,
    ): TokenInterface;
}
