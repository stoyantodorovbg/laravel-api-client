<?php

namespace Stoyantodorov\ApiClient\Tests\Feature\ApiClient;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Events\HttpResponseSucceeded;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class SuccessfulResponseEventTest extends TestCase
{
    private string $url = 'https://dummy-host/test';
    private array $options = ['test' => '123'];

    /** @test */
    public function dispatches_depending_on_config(): void
    {
        Http::fake();
        Event::fake();
        config(['api-client.events.onSuccess' => true]);

        resolve(HttpClientInterface::class)->post($this->url, $this->options);

        Event::assertDispatched(fn(HttpResponseSucceeded $event) =>
            $event->url === $this->url &&
            $event->options === $this->options &&
            $event->response->status() === 200
        );

        Event::fake();
        config(['api-client.events.onSuccess' => false]);

        resolve(HttpClientInterface::class)->post($this->url, $this->options);

        Event::assertNotDispatched(HttpResponseSucceeded::class);
    }

    /** @test */
    public function dispatches_depending_on_setter(): void
    {
        Http::fake();
        Event::fake();
        config(['api-client.events.onSuccess' => true]);

        resolve(HttpClientInterface::class)->fireEventOnSuccess(false)->post($this->url, $this->options);

        Event::assertNotDispatched(HttpResponseSucceeded::class);

        Event::fake();
        config(['api-client.events.onSuccess' => false]);

        resolve(HttpClientInterface::class)->fireEventOnSuccess(true)->post($this->url, $this->options);
        Event::assertDispatched(fn(HttpResponseSucceeded $event) =>
            $event->url === $this->url &&
            $event->options === $this->options &&
            $event->response->status() === 200
        );
    }
}
