<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Enums\HttpMethod;

class HttpResponseSucceeded
{
    use Dispatchable;

    public function __construct(
        public Response $response,
        public ApiClientRequestMethod $apiClientRequestMethod,
        public string                 $url,
        public array                  $options = [],
        public HttpMethod|null        $httpMethod = null,
    )
    {
    }
}
