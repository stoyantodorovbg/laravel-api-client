<?php

namespace Stoyantodorov\ApiClient\Interfaces;

use SensitiveParameter;

interface TokenFromConfigFactoryInterface
{
    /**
     * Instantiate TokenInterface
     * When receives $token it is set in TokenInterface instance
     * $configKey refers to token configurations in the config file
     *
     * @param string|null $token
     * @param string      $configKey
     * @return TokenInterface
     */
    public static function create(
        #[SensitiveParameter] string|null $token,
                              string $configKey = 'tokenConfigurationsBase'
    ): TokenInterface;
}
