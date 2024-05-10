<?php

namespace Stoyantodorov\ApiClient\Interfaces;

interface TokenInterface
{
    /**
     * Get Access Token
     * When it's missing request it
     * When $refresh is true make a request to refresh the token
     *
     * @param bool $refresh = false
     * @return string
     */
    public function get(bool $refresh = false): string;
}
