<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\Factories;

use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Factories\TokenFromDataFactory;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Stoyantodorov\ApiClient\Tests\Traits\TokenTests;

class TokenFromDataFactoryTest extends TestCase
{
    use TokenTests;

    private string $customConfigKey = 'customKey';

    /** @test */
    public function create_method_returns_token_interface(): void
    {
        $this->expectNotToPerformAssertions();
        $tokenData = new TokenData(
            url: $this->getPath($this->accessTokenRequestPath),
            body: $this->accessTokenRequestBody,
        );
        $refreshTokenData = new RefreshTokenData(
            url: $this->getPath($this->refreshTokenRequestPath),
            body: $this->refreshTokenRequestBody,
        );

        TokenFromDataFactory::create($tokenData, $refreshTokenData);
    }
}
