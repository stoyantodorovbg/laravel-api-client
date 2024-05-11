<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\Factories;

use Stoyantodorov\ApiClient\Interfaces\TokenFromConfigFactoryInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Throwable;

class TokenFromConfigFactoryTest extends TestCase
{
    private string $customConfigKey = 'customKey';

    /** @test */
    public function create_method_returns_token_interface(): void
    {
        $this->expectNotToPerformAssertions();
        resolve(TokenFromConfigFactoryInterface::class)->create();
    }

    /** @test */
    public function create_method_throws_error_when_receives_a_wrong_config_key(): void
    {
        $this->expectException(Throwable::class);
        resolve(TokenFromConfigFactoryInterface::class)->create(configKey: $this->customConfigKey);

    }

    /** @test */
    public function create_method_get_config_values_depending_on_the_received_key(): void
    {
        $this->expectNotToPerformAssertions();
        config([
            "api-client.{$this->customConfigKey}" => [
                'accessTokenRequest' => [
                    'url' => '',
                    'method' => 'post',
                    'body' => [],
                    'headers' => [],
                    'responseNestedKeys' => ['access_token']
                ],
                'refreshTokenRequest' => [
                    'url' => '',
                    'method' => 'post',
                    'body' => [],
                    'headers' => [],
                    'responseNestedKeys' => ['access_token']
                ],
                'tokenRequestsRetries' => 3,
            ]
        ]);
        resolve(TokenFromConfigFactoryInterface::class)->create(configKey: $this->customConfigKey);
    }
}
