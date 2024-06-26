<?php

namespace Stoyantodorov\ApiClient\Tests\Traits;

trait CommonData
{
    private string $url = 'https://example-host/test';
    private array $headers = ['Authorization' => 'Bearer 123'];
    private array $additionalHeaders = ['accept' => 'application/json'];
    private array $options = ['test' => '123'];
    private string $token = 'token123';
}
