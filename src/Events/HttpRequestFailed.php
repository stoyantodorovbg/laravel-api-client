<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\RequestException;

class HttpRequestFailed
{
    use Dispatchable;

    public function __construct(
        public RequestException $requestException,
        public string           $url,
        public array            $options = [],
    )
    {
    }
}
