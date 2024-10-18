<?php

namespace FilippoToso\ResourcePermissions;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('resource-permissions')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_resource_permissions_tables');
    }
}
