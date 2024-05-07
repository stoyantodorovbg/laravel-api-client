<?php

namespace Stoyantodorov\ApiClient\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
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
}
