<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\HttpClient;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\RejectedPromise;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Stoyantodorov\ApiClient\Tests\Traits\CommonData;

class DeleteTest extends TestCase
{
    use CommonData;

    /** @test */
    public function catches_request_exception(): void
    {
        Http::fake(fn() => Http::response(status: 500));

        $response = resolve(HttpClientInterface::class)->delete($this->url);
        $this->assertInstanceOf(Response::class, $response);
    }


    /** @test */
    public function catches_connection_exception(): void
    {
        Http::fake([$this->url => fn ($request) => new RejectedPromise(new ConnectException('Foo', $request->toPsrRequest()))]);

        $response = resolve(HttpClientInterface::class)->delete($this->url);
        $this->AssertNull($response);
    }

    /** @test */
    public function makes_a_request_with_the_configured_pending_request(): void
    {
        Http::fake([$this->url => Http::response()]);

        resolve(HttpClientInterface::class)->baseConfig(headers: $this->headers)->delete($this->url);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer 123'));
    }

    /** @test */
    public function uses_method_delete(): void
    {
        Http::fake([$this->url => Http::response()]);

        resolve(HttpClientInterface::class)->delete($this->url);
        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
    }

    /** @test */
    public function sends_provided_data(): void
    {
        Http::fake([$this->url => Http::response()]);

        resolve(HttpClientInterface::class)->delete($this->url, $this->options);
        Http::assertSent(fn (Request $request) => $request->data() === $this->options);
    }

    /** @test */
    public function sets_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->delete($this->url, $this->options, Http::withToken($this->token));
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', "Bearer {$this->token}"));
    }

    /** @test */
    public function resets_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->setPendingRequest(Http::withHeaders($this->additionalHeaders))
            ->delete($this->url, $this->options, Http::withToken($this->token));
        Http::assertSent(fn (Request $request) =>
        $request->hasHeader('Authorization', "Bearer {$this->token}")) && ! array_key_exists('accept', $request->headers());
    }
}
