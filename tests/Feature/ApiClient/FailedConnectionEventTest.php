<?php

namespace Stoyantodorov\ApiClient\Tests\Feature\ApiClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Events\HttpConnectionFailed;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class FailedConnectionEventTest extends TestCase
{
    private string $url = 'https://example-host/test';
    private array $options = ['test' => '123'];

    /** @test */
    public function dispatches_depending_on_config(): void
    {
        Http::fake(fn($request) => throw new ConnectionException());
        Event::fake();
        config(['api-client.events.onConnectionException' => true]);

        resolve(HttpClientInterface::class)->post($this->url, $this->options);

        Event::assertDispatched(fn(HttpConnectionFailed $event) =>
            $event->url === $this->url && $event->options === $this->options
        );

        Event::fake();
        config(['api-client.events.onConnectionException' => false]);

        resolve(HttpClientInterface::class)->post($this->url, $this->options);
        Event::assertNotDispatched(HttpConnectionFailed::class);
    }

    /** @test */
    public function dispatches_depending_on_setter(): void
    {
        Http::fake(fn($request) => throw new ConnectionException());
        Event::fake();
        config(['api-client.events.onConnectionException' => true]);

        resolve(HttpClientInterface::class)->fireEventOnConnectionException(false)->post($this->url, $this->options);

        Event::assertNotDispatched(HttpConnectionFailed::class);

        Event::fake();
        config(['api-client.events.onConnectionException' => false]);

        resolve(HttpClientInterface::class)->fireEventOnConnectionException(true)->post($this->url, $this->options);
        Event::assertDispatched(fn(HttpConnectionFailed $event) =>
            $event->url === $this->url && $event->options === $this->options
        );
    }
}
