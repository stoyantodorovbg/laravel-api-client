<?php

namespace Stoyantodorov\ApiClient\Tests\Feature\ApiClient;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Events\HttpRequestFailed;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class FailedRequestEventTest extends TestCase
{
    private string $url = 'https://example-host/test';
    private array $options = ['test' => '123'];

    /** @test */
    public function dispatches_depending_on_config(): void
    {
        Http::fake(fn() => Http::response(status: 500));
        Event::fake();
        config(['api-client.events.onRequestException' => true]);

        resolve(HttpClientInterface::class)->post($this->url, $this->options);

        Event::assertDispatched(fn(HttpRequestFailed $event) =>
            $event->url === $this->url &&
            $event->options === $this->options &&
            $event->requestException->response->status() === 500
        );

        Event::fake();
        config(['api-client.events.onRequestException' => false]);

        resolve(HttpClientInterface::class)->post($this->url, $this->options);

        Event::assertNotDispatched(HttpRequestFailed::class);
    }

    /** @test */
    public function dispatches_depending_on_setter(): void
    {
        Http::fake(fn() => Http::response(status: 500));
        Event::fake();
        config(['api-client.events.onRequestException' => true]);

        resolve(HttpClientInterface::class)->fireEventOnRequestException(false)->post($this->url, $this->options);

        Event::assertNotDispatched(HttpRequestFailed::class);

        Event::fake();
        config(['api-client.events.onRequestException' => false]);

        resolve(HttpClientInterface::class)->fireEventOnRequestException(true)->post($this->url, $this->options);
        Event::assertDispatched(fn(HttpRequestFailed $event) =>
            $event->url === $this->url &&
            $event->options === $this->options &&
            $event->requestException->response->status() === 500
        );
    }
}
