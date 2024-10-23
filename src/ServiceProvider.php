<?php

namespace FilippoToso\ResourcePermissions;

use Illuminate\Support\Facades\Blade;
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

    public function packageBooted()
    {
        Blade::if('role', function (...$arguments) {
            /** @disregard P1009 */
            if ($user = auth()->user()) {
                return $user->hasRole(...$arguments);
            }
        });

        Blade::if('permission', function (...$arguments) {
            /** @disregard P1009 */
            if ($user = auth()->user()) {
                return $user->hasPermission(...$arguments);
            }
        });
    }
}
