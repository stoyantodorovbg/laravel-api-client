<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Enums\PendingRequestMethod;
use Stoyantodorov\ApiClient\Interfaces\ApiClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class AddPendingRequestMethodTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $options = ['test' => '123'];
    private string $token = 'token123';

    /** @test */
    public function adds_method_to_pending_request(): void
    {
        Http::fake();

        resolve(ApiClientInterface::class)->addPendingRequestMethod(PendingRequestMethod::WITH_TOKEN, [$this->token])
            ->sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', "Bearer {$this->token}"));

    }

    /** @test */
    public function keeps_the_pending_request_that_has_been_set_already(): void
    {
        Http::fake();

        $client = resolve(ApiClientInterface::class)->addPendingRequestMethod(PendingRequestMethod::WITH_TOKEN, [$this->token]);
        $client->addPendingRequestMethod(PendingRequestMethod::WITH_HEADER, ['accept', 'application/json'])
            ->sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('accept', 'application/json') && array_key_exists('Authorization', $request->headers())
        );
    }

    /** @test */
    public function refreshes_pending_request_when_appropriate_parameter_is_sent(): void
    {
        Http::fake();

        $client = resolve(ApiClientInterface::class)->addPendingRequestMethod(PendingRequestMethod::WITH_TOKEN, [$this->token]);
        $client->addPendingRequestMethod(PendingRequestMethod::WITH_HEADER, ['accept', 'application/json'], true)
            ->sendRequest(ApiClientRequestMethod::POST, $this->url, $this->options);
        Http::assertSent(fn (Request $request) =>
            $request->hasHeader('accept', 'application/json') && ! array_key_exists('Authorization', $request->headers())
        );

    }
}
