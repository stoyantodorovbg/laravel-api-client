<?php

namespace Stoyantodorov\ApiClient\Tests\Feature\Token;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Events\AccessTokenObtained;
use Stoyantodorov\ApiClient\Events\AccessTokenRefreshed;
use Stoyantodorov\ApiClient\Factories\TokenFromConfigFactory;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Stoyantodorov\ApiClient\Tests\Traits\TokenTests;
use Stoyantodorov\ApiClient\Token;

class TokenFromConfigFactoryTest extends TestCase
{
    use TokenTests;

    /** @test */
    public function returns_the_same_token_when_that_has_been_sent_to_the_factory(): void
    {
        $this->tokenConfigurationsBase($this->getPath($this->accessTokenRequestPath), false);
        $service = TokenFromConfigFactory::create(
            hasRefreshTokenRequest: false,
            token: $this->tokenValue,
        );

        $this->assertSame($this->tokenValue, $service->get());
    }

    /** @test */
    public function makes_request_to_the_right_url_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();
        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $this->assertSame($this->tokenValue, $service->get());
        Http::assertSent(fn (Request $request) => $request->url() === $path);
    }

    /** @test */
    public function sends_the_expected_body_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();
        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $service->get();
        Http::assertSent(fn (Request $request) => $request->data() === $this->accessTokenRequestBody);
    }

    /** @test */
    public function sends_the_expected_headers_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();
        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $service->get();
        Http::assertSent(fn (Request $request) => $request->headers()['accept'][0] === $this->headers['accept']);
    }

    /** @test */
    public function sends_the_expected_method_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $service->get();
        Http::assertSent(fn (Request $request) => $request->method() === 'POST');
    }

    /** @test */
    public function gets_the_token_from_the_provided_json_path(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();
        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $this->assertSame($this->tokenValue, $service->get());

        $service = new Token(
            httpClient: resolve(HttpClientInterface::class),
            tokenData: new TokenData($path, $this->accessTokenRequestBody, responseNestedKeys: ['data', 'access_token']),
        );

        $this->clearExistingFakes();
        Http::fake([$path => Http::response(['data' => ['access_token' => $this->tokenValue]])]);
        $this->assertSame($this->tokenValue, $service->get());
    }

    /** @test */
    public function fires_certain_event_when_receives_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, true);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);
        Event::fake();

        $service->get();
        Event::assertDispatched(AccessTokenObtained::class);
    }

    /** @test */
    public function do_not_fire_any_events_when_receives_such_configuration(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);
        Event::fake();

        $service->get();
        Event::assertNotDispatched(AccessTokenObtained::class);
    }

    /** @test */
    public function returns_the_token_from_the_response_instead_of_the_one_received_as_factory_parameter_when_refreshes(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $this->refreshTokenConfigurationsBase($path, false);
        $customToken = 'customToken';
        $service = TokenFromConfigFactory::create(token: $customToken);

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        $token = $service->get(true);
        $this->assertSame($this->refreshedTokenValue, $token);
        $this->assertNotSame($customToken, $token);
    }


    /** @test */
    public function makes_request_to_the_right_url_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $this->refreshTokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $this->assertSame($this->refreshedTokenValue, $service->get(true));
        Http::assertSent(fn (Request $request) => $request->url() === $path);
    }

    /** @test */
    public function sends_the_expected_body_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $this->refreshTokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $service->get(true);
        Http::assertSent(fn (Request $request) => $request->data() === $this->refreshTokenRequestBody);
    }

    /** @test */
    public function sends_the_expected_headers_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $this->refreshTokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $service->get(true);
        Http::assertSent(fn (Request $request) => $request->headers()['accept'][0] === $this->headers['accept']);
    }

    /** @test */
    public function sends_the_expected_method_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $this->refreshTokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $service->get(true);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST');
    }

    /** @test */
    public function gets_the_token_from_the_provided_json_path_when_refreshes(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, false);
        $this->refreshTokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        $this->assertSame($this->refreshedTokenValue, $service->get(true));

        $this->refreshTokenConfigurationsBase($path, false, ['data', 'access_token']);
        $service = TokenFromConfigFactory::create();

        $this->clearExistingFakes();
        Http::fake([$path => Http::response(['data' => ['access_token' => $this->refreshedTokenValue]])]);
        $this->assertSame($this->refreshedTokenValue, $service->get(true));
    }

    /** @test */
    public function fires_certain_event_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, true);
        $this->refreshTokenConfigurationsBase($path, true);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        Event::fake();

        $service->get(true);
        Event::assertDispatched(AccessTokenRefreshed::class);
    }

    /** @test */
    public function do_not_fire_event_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $this->tokenConfigurationsBase($path, true);
        $this->refreshTokenConfigurationsBase($path, false);
        $service = TokenFromConfigFactory::create();

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        Event::fake();

        $service->get(true);
        Event::assertNotDispatched(AccessTokenRefreshed::class);
    }

    private function tokenConfigurationsBase(string $path, bool $dispatchEvent): void
    {
        config(['api-client.tokenConfigurationsBase.accessTokenRequest' => [
            'url' => $path,
            'body' => $this->accessTokenRequestBody,
            'headers' => $this->headers,
            'responseNestedKeys' => $this->accessTokenResponseNestedKeys,
            'method' => $this->method,
            'dispatchEvent' => $dispatchEvent,
        ]]);
    }

    private function refreshTokenConfigurationsBase(string $path, bool $dispatchEvent, array|null $nestedKeys = null): void
    {
        config(['api-client.tokenConfigurationsBase.refreshTokenRequest' => [
            'url' => $path,
            'body' => $this->refreshTokenRequestBody,
            'headers' => $this->headers,
            'responseNestedKeys' => $nestedKeys ?? $this->refreshTokenResponseNestedKeys,
            'method' => $this->method,
            'dispatchEvent' => $dispatchEvent,
        ]]);
    }
}
