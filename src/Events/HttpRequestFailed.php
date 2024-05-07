<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Enums\HttpMethod;

class HttpRequestFailed
{
    use Dispatchable;

    public function __construct(
        public RequestException $requestException,
        public ApiClientRequestMethod $apiClientRequestMethod,
        public string                 $url,
        public array                  $options = [],
        public HttpMethod|null        $httpMethod = null,
    )
    {
    }
}
