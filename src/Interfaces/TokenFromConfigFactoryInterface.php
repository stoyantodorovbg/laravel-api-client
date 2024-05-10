<?php

namespace Stoyantodorov\ApiClient\Interfaces;

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
    public static function create(string|null $token, string $configKey = 'tokenConfigurationsBase'): TokenInterface;
}
