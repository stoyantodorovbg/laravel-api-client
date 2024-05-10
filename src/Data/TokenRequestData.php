<?php

namespace Stoyantodorov\ApiClient\Data;

use SensitiveParameter;

readonly class TokenRequestData
{
    public function __construct(
                              public string $url,
        #[SensitiveParameter] public array  $body,
        #[SensitiveParameter] public array  $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                              public string $method = 'post',
    )
    {
    }
}
