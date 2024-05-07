<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Enums\HttpMethod;

class HttpConnectionFailed
{
    use Dispatchable;

    public function __construct(
        public ConnectionException    $connectionException,
        public ApiClientRequestMethod $apiClientRequestMethod,
        public string                 $url,
        public array                  $options = [],
        public HttpMethod|null        $httpMethod = null,
    )
    {
    }
}
