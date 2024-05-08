<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\HttpClient;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\RejectedPromise;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class SendTest extends TestCase
{
    use CommonData;

    /** @test */
    public function catches_request_exception(): void
    {
        Http::fake(fn() => Http::response(status: 500));

        $response = resolve(HttpClientInterface::class)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        $this->assertInstanceOf(Response::class, $response);
    }


    /** @test */
    public function catches_connection_exception(): void
    {
        Http::fake([$this->url => fn ($request) => new RejectedPromise(new ConnectException('Foo', $request->toPsrRequest()))]);

        $response = resolve(HttpClientInterface::class)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        $this->AssertNull($response);
    }

    /** @test */
    public function makes_a_request_with_the_configured_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->baseConfig(headers: $this->headers)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer 123'));
    }

    /** @test */
    public function sends_to_the_expected_url(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->url() === $this->url);
    }

    /** @test */
    public function sends_provided_data(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '123'));
    }

    /** @test */
    public function uses_the_expected_method(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::HEAD, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'HEAD');

        resolve(HttpClientInterface::class)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'GET');

        resolve(HttpClientInterface::class)->send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST');

        resolve(HttpClientInterface::class)->send(HttpMethod::PATCH, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH');

        resolve(HttpClientInterface::class)->send(HttpMethod::PUT, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'PUT');

        resolve(HttpClientInterface::class)->send(HttpMethod::DELETE, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');

        resolve(HttpClientInterface::class)->send(HttpMethod::CONNECT, $this->url, HttpRequestFormat::QUERY, []);
        Http::assertSent(fn (Request $request) => $request->method() === 'CONNECT');

        resolve(HttpClientInterface::class)->send(HttpMethod::OPTIONS, $this->url, HttpRequestFormat::QUERY, []);
        Http::assertSent(fn (Request $request) => $request->method() === 'OPTIONS');

        resolve(HttpClientInterface::class)->send(HttpMethod::TRACE, $this->url, HttpRequestFormat::QUERY, []);
        Http::assertSent(fn (Request $request) => $request->method() === 'TRACE');
    }

    /** @test */
    public function sends_multipart_data(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(
            httpMethod: HttpMethod::POST,
            url: $this->url,
            format: HttpRequestFormat::MULTIPART,
            options: $this->options,
            pendingRequest: Http::attach('attachment',  'test')
        );
        Http::assertSent(fn (Request $request) => $request->isMultipart());
    }

    /** @test */
    public function sends_form_data(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);
        Http::assertSent(fn (Request $request) => $request->isForm());
    }

    /** @test */
    public function sends_json_data(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::POST, $this->url, HttpRequestFormat::JSON, $this->options);
        Http::assertSent(fn (Request $request) => $request->isJson());
    }

    /** @test */
    public function sends_query_parameters(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '123'));
    }

    /** @test */
    public function sets_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options);

        resolve(HttpClientInterface::class)->send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options, Http::withToken($this->token));
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', "Bearer {$this->token}"));
    }

    /** @test */
    public function resets_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->setPendingRequest(Http::withHeaders($this->additionalHeaders))
            ->send(HttpMethod::POST, $this->url, HttpRequestFormat::FORM_PARAMS, $this->options, Http::withToken($this->token));
        Http::assertSent(fn (Request $request) =>
        $request->hasHeader('Authorization', "Bearer {$this->token}")) && ! array_key_exists('accept', $request->headers());
    }
}
