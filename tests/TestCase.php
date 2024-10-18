<?php

namespace FilippoToso\ResourcePermissions\Tests;

use FilippoToso\ResourcePermissions\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'FilippoToso\\ResourcePermissions\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_resource_permissions_tables.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../workbench/database/migrations/create_users_table.php';
        $migration->up();
    }
}
