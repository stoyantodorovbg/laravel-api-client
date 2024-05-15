<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\HttpClient;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\HttpClientRequestMethod;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class GetResponseTest extends TestCase
{
    private string $url = 'https://example-host/test';
    private array $headers = ['Authorization' => 'Bearer 123'];
    private array $fake200ResponseData = ['message' => 'Success'];

    /** @test */
    public function makes_a_request(): void
    {
        Http::fake(fn() => Http::response($this->fake200ResponseData));

        $response = resolve(HttpClientInterface::class)->request(HttpClientRequestMethod::GET, $this->url);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->fake200ResponseData, $response->json());
    }

    /** @test */
    public function makes_a_request_with_the_configured_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->baseConfig(headers: $this->headers)->request(HttpClientRequestMethod::GET, $this->url);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer 123'));
    }

    /** @test */
    public function throws_exceptions_depends_on_a_parameter(): void
    {
        Http::fake(fn() => Http::response(status: 500));

        $this->expectException(RequestException::class);
        resolve(HttpClientInterface::class)->request(HttpClientRequestMethod::GET, $this->url, throw: true);

        $response = resolve(HttpClientInterface::class)->request(HttpClientRequestMethod::GET, $this->url);
        $this->assertInstanceOf(Response::class, $response);
    }
}
