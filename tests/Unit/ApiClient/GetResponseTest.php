<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Facades\ApiClient;
use Stoyantodorov\ApiClient\Tests\TestCase;

class GetResponseTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $headers = ['Authentication' => 'Bearer 123'];
    private array $fake200ResponseData = ['message' => 'Success'];

    /** @test */
    public function make_a_request(): void
    {
        Http::fake([$this->url => Http::response($this->fake200ResponseData)]);

        $response = ApiClient::getResponse(ApiClientRequestMethod::GET, $this->url);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->fake200ResponseData, $response->json());
    }

    /** @test */
    public function make_a_request_with_the_configured_pending_request(): void
    {
        Http::fake([$this->url => Http::response()]);

        ApiClient::baseConfig(headers: $this->headers)->getResponse(ApiClientRequestMethod::GET, $this->url);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authentication', 'Bearer 123'));
    }

    /** @test */
    public function throws_exceptions_depends_on_a_parameter(): void
    {
        Http::fake([$this->url => Http::response(status: 500)]);

        $this->expectException(RequestException::class);
        ApiClient::getResponse(ApiClientRequestMethod::GET, $this->url, throw: true);

        $response = ApiClient::getResponse(ApiClientRequestMethod::GET, $this->url);
        $this->assertInstanceOf(Response::class, $response);
    }
}
