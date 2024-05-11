<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;

class AccessTokenReceived
{
    use Dispatchable;

    public function __construct(public Response $response)
    {
    }
}
