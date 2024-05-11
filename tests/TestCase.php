<?php

namespace Stoyantodorov\ApiClient\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionObject;
use Stoyantodorov\ApiClient\ApiClientServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ApiClientServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    protected function clearExistingFakes(): void
    {
        $reflection = new ReflectionObject(Http::getFacadeRoot());
        $reflection->getProperty('stubCallbacks')->setValue(Http::getFacadeRoot(), collect());
    }
}
