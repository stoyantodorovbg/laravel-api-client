<?php

namespace Stoyantodorov\ApiClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stoyantodorov\ApiClient\ApiClient
 */
class ApiClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Stoyantodorov\ApiClient\ApiClient::class;
    }
}
