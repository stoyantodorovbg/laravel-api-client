<?php

namespace Stoyantodorov\ApiClient;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stoyantodorov\ApiClient\Interfaces\ApiClientInterface;

class ApiClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-api-client')->hasConfigFile('api-client');

        $this->app->bind(ApiClientInterface::class, ApiClient::class);
    }
}
