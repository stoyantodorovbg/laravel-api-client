<?php

namespace Stoyantodorov\ApiClient\Data;

use SensitiveParameter;

readonly class RefreshTokenData
{
    public function __construct(
                              public string $url,
        #[SensitiveParameter] public array  $body,
        #[SensitiveParameter] public array  $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                              public array  $responseNestedKeys = ['access_token'],
                              public string $method = 'post',
                              public bool   $dispatchEvent = true,
    )
    {
    }
}
