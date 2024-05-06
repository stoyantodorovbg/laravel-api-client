<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\RejectedPromise;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Enums\PendingRequestMethod;
use Stoyantodorov\ApiClient\Facades\ApiClient;
use Stoyantodorov\ApiClient\Tests\TestCase;

class SendTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $headers = ['Authentication' => 'Bearer 123'];
    private array $options = ['test' => '123'];

    /** @test */
    public function catches_request_exception(): void
    {
        Http::fake(fn() => Http::response(status: 500));

        $response = ApiClient::send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        $this->assertInstanceOf(Response::class, $response);
    }


    /** @test */
    public function catches_connection_exception(): void
    {
        Http::fake([$this->url => fn ($request) => new RejectedPromise(new ConnectException('Foo', $request->toPsrRequest()))]);

        $response = ApiClient::send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        $this->AssertNull($response);
    }

    /** @test */
    public function makes_a_request_with_the_configured_pending_request(): void
    {
        Http::fake();

        ApiClient::baseConfig(headers: $this->headers)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authentication', 'Bearer 123'));
    }

    /** @test */
    public function sends_to_the_expected_url(): void
    {
        Http::fake();

        ApiClient::send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->url() === $this->url);
    }

    /** @test */
    public function sends_provided_data(): void
    {
        Http::fake();

        ApiClient::send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '123'));
    }

    /** @test */
    public function uses_the_expected_method(): void
    {
        Http::fake();

        ApiClient::send(HttpMethod::HEAD, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'HEAD');

        ApiClient::send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'GET');

        ApiClient::send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST');

        ApiClient::send(HttpMethod::PATCH, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH');

        ApiClient::send(HttpMethod::PUT, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'PUT');

        ApiClient::send(HttpMethod::DELETE, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');

        ApiClient::send(HttpMethod::CONNECT, $this->url, HttpRequestFormat::QUERY, []);
        Http::assertSent(fn (Request $request) => $request->method() === 'CONNECT');

        ApiClient::send(HttpMethod::OPTIONS, $this->url, HttpRequestFormat::QUERY, []);
        Http::assertSent(fn (Request $request) => $request->method() === 'OPTIONS');

        ApiClient::send(HttpMethod::TRACE, $this->url, HttpRequestFormat::QUERY, []);
        Http::assertSent(fn (Request $request) => $request->method() === 'TRACE');
    }

    /** @test */
    public function sends_multipart_data(): void
    {
        Http::fake();

        ApiClient::addPendingRequestMethod(PendingRequestMethod::ATTACH, ['attachment',  'test'])
            ->send(HttpMethod::POST, $this->url, HttpRequestFormat::MULTIPART, $this->options);
        Http::assertSent(fn (Request $request) => $request->isMultipart());
    }

    /** @test */
    public function sends_form_data(): void
    {
        Http::fake();

        ApiClient::send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->isForm());
    }

    /** @test */
    public function sends_json_data(): void
    {
        Http::fake();

        ApiClient::send(HttpMethod::POST, $this->url, HttpRequestFormat::JSON, $this->options);
        Http::assertSent(fn (Request $request) => $request->isJson());
    }
}
