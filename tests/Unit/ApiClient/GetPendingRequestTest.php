<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

use Illuminate\Http\Client\PendingRequest;
use Stoyantodorov\ApiClient\Interfaces\ApiClientInterface;
use Stoyantodorov\ApiClient\Tests\TestCase;

class GetPendingRequestTest extends TestCase
{
    /** @test */
    public function returns_null_when_pending_request_has_not_been_set(): void
    {
        $this->assertNull(resolve(ApiClientInterface::class)->getPendingRequest());
    }

    /** @test */
    public function returns_a_pending_request_it_has_been_set(): void
    {
        $this->assertInstanceOf(PendingRequest::class, resolve(ApiClientInterface::class)->baseConfig()->getPendingRequest());
    }
}
