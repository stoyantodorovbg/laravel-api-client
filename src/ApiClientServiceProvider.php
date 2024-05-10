<?php

namespace Stoyantodorov\ApiClient;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stoyantodorov\ApiClient\Factories\TokenFromConfigFactory;
use Stoyantodorov\ApiClient\Factories\TokenFromDataFactory;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenFromConfigFactoryInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenFromDataFactoryInterface;

class ApiClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-api-client')->hasConfigFile('api-client');

        $this->app->bind(HttpClientInterface::class, HttpClient::class);
        $this->app->bind(TokenFromConfigFactoryInterface::class, TokenFromConfigFactory::class);
        $this->app->bind(TokenFromDataFactoryInterface::class, TokenFromDataFactory::class);
    }
}
