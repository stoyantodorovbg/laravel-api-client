<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Enums\PendingRequestMethod;
use Stoyantodorov\ApiClient\Facades\ApiClient;
use Stoyantodorov\ApiClient\Tests\TestCase;

class BaseConfigTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $options = ['test' => '123'];
    private array $headers = ['Authorization' => 'Bearer 123', 'accept' => 'application/json'];
    private string $token = 'token123';

    /** @test */
    public function sets_headers(): void
    {
        Http::fake();

        ApiClient::baseConfig(headers: $this->headers)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('Authorization', 'Bearer 123') && $request->hasHeader('accept', 'application/json')
        );
    }

    /** @test */
    public function sets_user_agent_from_config(): void
    {
        Http::fake();

        ApiClient::baseConfig()->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('User-Agent', config('app.name')));
    }

    /** @test */
    public function sets_user_agent_from_sent_parameter(): void
    {
        Http::fake();

        $userAgent = 'Custom Agent';
        ApiClient::baseConfig(userAgent: $userAgent)->send(HttpMethod::GET, $this->url, HttpRequestFormat::QUERY, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('User-Agent', $userAgent));
    }

    /** @test */
    public function keeps_the_pending_request_that_has_been_set_already_when_appropriate_parameter_is_sent(): void
    {
        Http::fake();

        $client = ApiClient::addPendingRequestMethod(PendingRequestMethod::WITH_TOKEN, [$this->token]);
        $client->baseConfig(headers: ['accept' => 'application/json'], newPendingRequest: false)
            ->sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('accept', 'application/json') && $request->hasHeader('Authorization', "Bearer {$this->token}")
        );
    }

    /** @test */
    public function sets_new_pending_request_by_default(): void
    {
        Http::fake();

        $client = ApiClient::addPendingRequestMethod(PendingRequestMethod::WITH_TOKEN, [$this->token]);
        $client->baseConfig(['accept' => 'application/json'])
            ->sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('accept', 'application/json') && ! array_key_exists('Authorization', $request->headers())
        );
    }
}
