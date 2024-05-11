<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\HttpClient;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\HttpClientRequestMethod;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;
use Stoyantodorov\ApiClient\Tests\Traits\CommonData;

class BaseConfigTest extends TestCase
{
    use CommonData;


    /** @test */
    public function sets_headers(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->baseConfig(headers: $this->headers)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer 123'));
    }

    /** @test */
    public function sets_user_agent_from_config(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->baseConfig()->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('User-Agent', config('app.name')));
    }

    /** @test */
    public function sets_user_agent_from_sent_parameter(): void
    {
        Http::fake();

        $userAgent = 'Custom Agent';
        resolve(HttpClientInterface::class)->baseConfig(userAgent: $userAgent)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('User-Agent', $userAgent));
    }

    /** @test */
    public function resets_pending_request_that_hase_been_set_when_receives_a_new_one(): void
    {
        Http::fake();

        $client = resolve(HttpClientInterface::class)->setPendingRequest(Http::withToken($this->token));
        $client->baseConfig(headers: ['accept' => 'application/json'])
            ->sendRequest(HttpClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('accept', 'application/json') && ! array_key_exists('Authorization', $request->headers())
        );
    }

    /** @test */
    public function sets_pending_request(): void
    {
        Http::fake();

        resolve(HttpClientInterface::class)->baseConfig(headers: ['accept' => 'application/json'], pendingRequest: Http::withToken($this->token))
            ->sendRequest(HttpClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('accept', 'application/json') && $request->hasHeader('Authorization', "Bearer {$this->token}")
        );
    }
}
