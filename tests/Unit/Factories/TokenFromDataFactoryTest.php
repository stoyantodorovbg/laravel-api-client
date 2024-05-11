<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\Factories;

use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Interfaces\TokenFromDataFactoryInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Stoyantodorov\ApiClient\Tests\Traits\TokenRequestsData;
use Throwable;

class TokenFromDataFactoryTest extends TestCase
{
    use TokenRequestsData;

    private string $customConfigKey = 'customKey';

    /** @test */
    public function create_method_returns_token_interface(): void
    {
        $this->expectNotToPerformAssertions();
        $tokenData = new TokenData(
            url: $this->getPath($this->accessTokenRequestPath),
            body: $this->accessTokenRequestBody,
            headers: $this->headers,
            method: $this->method,
            responseNestedKeys: $this->accessTokenResponseNestedKeys,
        );
        $refreshTokenData = new RefreshTokenData(
            url: $this->getPath($this->refreshTokenRequestPath),
            body: $this->refreshTokenRequestBody,
            headers: $this->headers,
            method: $this->method,
            responseNestedKeys: $this->refreshTokenResponseNestedKeys,
        );

        resolve(TokenFromDataFactoryInterface::class)->create($tokenData, $refreshTokenData);
    }
}
