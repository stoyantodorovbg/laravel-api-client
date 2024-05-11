<?php

namespace Stoyantodorov\ApiClient\Interfaces;

use SensitiveParameter;

interface TokenFromConfigFactoryInterface
{
    /**
     * Instantiate TokenInterface
     * When receives $token it is set in TokenInterface instance
     * $hasRefreshTokenRequest determines instantiating RefreshTokenData
     * $configKey refers to token configurations in the config file
     *
     * @param bool        $hasRefreshTokenRequest
     * @param string      $configKey
     * @param string|null $token = null
     * @return TokenInterface
     */
    public static function create(
                              bool $hasRefreshTokenRequest = true,
                              string $configKey = 'tokenConfigurationsBase',
                              #[SensitiveParameter] string|null $token = null,
    ): TokenInterface;
}
