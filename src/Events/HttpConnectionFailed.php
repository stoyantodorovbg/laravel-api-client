<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\ConnectionException;

class HttpConnectionFailed
{
    use Dispatchable;

    public function __construct(
        public ConnectionException $connectionException,
        public string              $url,
        public array               $options = [],
    )
    {
    }
}
