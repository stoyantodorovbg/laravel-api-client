<?php

namespace Stoyantodorov\ApiClient\Tests\Unit\ApiClient;

trait CommonData
{
    private string $url = 'https://dummy-host/test';
    private array $headers = ['Authorization' => 'Bearer 123'];
    private array $additionalHeaders = ['accept' => 'application/json'];
    private array $options = ['test' => '123'];
    private string $token = 'token123';
}
