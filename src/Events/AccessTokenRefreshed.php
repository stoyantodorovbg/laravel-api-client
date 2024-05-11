<?php

namespace Stoyantodorov\ApiClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;

class AccessTokenRefreshed
{
    use Dispatchable;

    public function __construct(public Response $response)
    {

    }
}
