<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\RejectedPromise;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Facades\ApiClient;
use Stoyantodorov\ApiClient\Tests\TestCase;

class SendRequestTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $headers = ['Authentication' => 'Bearer 123'];
    private array $options = ['test' => '123'];

    /** @test */
    public function catches_request_exception(): void
    {
        Http::fake(fn() => Http::response(status: 500));

        $response = ApiClient::sendRequest(ApiClientRequestMethod::GET, $this->url);
        $this->assertInstanceOf(Response::class, $response);
    }


    /** @test */
    public function catches_connection_exception(): void
    {
        Http::fake([$this->url => fn ($request) => new RejectedPromise(new ConnectException('Foo', $request->toPsrRequest()))]);

        $response = ApiClient::sendRequest(ApiClientRequestMethod::GET, $this->url);
        $this->AssertNull($response);
    }

    /** @test */
    public function makes_a_request_with_the_configured_pending_request(): void
    {
        Http::fake();

        ApiClient::baseConfig(headers: $this->headers)->sendRequest(ApiClientRequestMethod::GET, $this->url);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authentication', 'Bearer 123'));
    }

    /** @test */
    public function sends_to_the_expected_url(): void
    {
        Http::fake();

        ApiClient::sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) => $request->url() === $this->url);
    }

    /** @test */
    public function makes_a_request_with_provided_method(): void
    {
        Http::fake();

        ApiClient::sendRequest(ApiClientRequestMethod::GET, $this->url);
        Http::assertSent(fn (Request $request) => $request->method() === 'GET');
    }

    /** @test */
    public function makes_a_request_with_provided_data(): void
    {
        Http::fake();

        ApiClient::sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) => $request->data() === $this->options);

        ApiClient::sendRequest(ApiClientRequestMethod::GET, $this->url, $this->options);
        Http::assertSent(fn (Request $request) => $request->data() === $this->options);
    }
}