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

class DeleteTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $headers = ['Authentication' => 'Bearer 123'];
    private array $options = ['test' => '123'];

    /** @test */
    public function catches_request_exception(): void
    {
        Http::fake(fn() => Http::response(status: 500));

        $response = ApiClient::delete($this->url);
        $this->assertInstanceOf(Response::class, $response);
    }


    /** @test */
    public function catches_connection_exception(): void
    {
        Http::fake([$this->url => fn ($request) => new RejectedPromise(new ConnectException('Foo', $request->toPsrRequest()))]);

        $response = ApiClient::delete($this->url);
        $this->AssertNull($response);
    }

    /** @test */
    public function makes_a_request_with_the_configured_pending_request(): void
    {
        Http::fake([$this->url => Http::response()]);

        ApiClient::baseConfig(headers: $this->headers)->delete($this->url);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authentication', 'Bearer 123'));
    }

    /** @test */
    public function uses_method_delete(): void
    {
        Http::fake([$this->url => Http::response()]);

        ApiClient::delete($this->url);
        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
    }

    /** @test */
    public function sends_provided_data(): void
    {
        Http::fake([$this->url => Http::response()]);

        ApiClient::delete($this->url, $this->options);
        Http::assertSent(fn (Request $request) => $request->data() === $this->options);
    }
}
