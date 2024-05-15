<?php

namespace Stoyantodorov\ApiClient\Tests\Feature\Token;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Events\AccessTokenObtained;
use Stoyantodorov\ApiClient\Events\AccessTokenRefreshed;
use Stoyantodorov\ApiClient\Factories\TokenFromDataFactory;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Stoyantodorov\ApiClient\Tests\Traits\TokenTests;
use Stoyantodorov\ApiClient\Token;

class TokenFromDataFactoryTest extends TestCase
{
    use TokenTests;

    /** @test */
    public function returns_the_same_token_when_that_has_been_sent_to_the_factory(): void
    {
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($this->getPath($this->accessTokenRequestPath), $this->accessTokenRequestBody),
            token: $this->tokenValue,
        );

        $this->assertSame($this->tokenValue, $service->get());
    }

    /** @test */
    public function makes_request_to_the_right_url_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $this->assertSame($this->tokenValue, $service->get());
        Http::assertSent(fn (Request $request) => $request->url() === $path);
    }

    /** @test */
    public function sends_the_expected_body_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $service->get();
        Http::assertSent(fn (Request $request) => $request->data() === $this->accessTokenRequestBody);
    }

    /** @test */
    public function sends_the_expected_headers_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody, $this->headers),
        );

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $service->get();
        Http::assertSent(fn (Request $request) => $request->headers()['accept'][0] === $this->headers['accept']);
    }

    /** @test */
    public function sends_the_expected_method_when_retrieves_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);

        $service->get();
        Http::assertSent(fn (Request $request) => $request->method() === 'POST');
    }

    /** @test */
    public function gets_the_token_from_the_provided_json_path(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
        );

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
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);
        Event::fake();

        $service->get();
        Event::assertDispatched(AccessTokenObtained::class);
    }

    /** @test */
    public function do_not_fire_event_when_receives_a_token(): void
    {
        $path = $this->getPath($this->accessTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody, dispatchEvent: false),
        );

        Http::fake([$path => Http::response(['access_token' => $this->tokenValue])]);
        Event::fake();

        $service->get();
        Event::assertNotDispatched(AccessTokenObtained::class);
    }

    /** @test */
    public function returns_the_token_from_the_response_instead_of_the_one_received_as_factory_parameter_when_refreshes(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody),
            token: $this->tokenValue,
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        $this->assertSame($this->refreshedTokenValue, $service->get(true));
    }


    /** @test */
    public function makes_request_to_the_right_url_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $this->assertSame($this->refreshedTokenValue, $service->get(true));
        Http::assertSent(fn (Request $request) => $request->url() === $path);
    }

    /** @test */
    public function sends_the_expected_body_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $service->get(true);
        Http::assertSent(fn (Request $request) => $request->data() === $this->refreshTokenRequestBody);
    }

    /** @test */
    public function sends_the_expected_headers_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody, $this->headers),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody, $this->headers),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $service->get(true);
        Http::assertSent(fn (Request $request) => $request->headers()['accept'][0] === $this->headers['accept']);
    }

    /** @test */
    public function sends_the_expected_method_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody, $this->headers),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);

        $service->get(true);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST');
    }

    /** @test */
    public function gets_the_token_from_the_provided_json_path_when_refreshes(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        $this->assertSame($this->refreshedTokenValue, $service->get(true));

        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->accessTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody, responseNestedKeys: ['data', 'access_token']),
        );

        $this->clearExistingFakes();
        Http::fake([$path => Http::response(['data' => ['access_token' => $this->refreshedTokenValue]])]);
        $this->assertSame($this->refreshedTokenValue, $service->get(true));
    }


    /** @test */
    public function fires_certain_event_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->refreshTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        Event::fake();

        $service->get(true);
        Event::assertDispatched(AccessTokenRefreshed::class);
    }

    /** @test */
    public function do_not_fire_event_when_refreshes_a_token(): void
    {
        $path = $this->getPath($this->refreshTokenRequestPath);
        $service = TokenFromDataFactory::create(
            tokenData: new TokenData($path, $this->refreshTokenRequestBody),
            refreshTokenData: new RefreshTokenData($path, $this->refreshTokenRequestBody, dispatchEvent: false),
        );

        Http::fake([$path => Http::response(['access_token' => $this->refreshedTokenValue])]);
        Event::fake();

        $service->get(true);
        Event::assertNotDispatched(AccessTokenRefreshed::class);
    }
}
