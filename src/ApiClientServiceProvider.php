<?php

namespace Stoyantodorov\ApiClient;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stoyantodorov\ApiClient\Commands\ApiClientCommand;

class ApiClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-api-client')
            ->hasConfigFile()
            ->hasCommand(ApiClientCommand::class);
    }
}
