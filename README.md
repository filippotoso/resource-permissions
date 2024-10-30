# An highly opinionated polymorphic role / permission package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/filippotoso/resource-permissions.svg?style=flat-square)](https://packagist.org/packages/filippotoso/resource-permissions)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/filippotoso/resource-permissions/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/filippotoso/resource-permissions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/filippotoso/resource-permissions/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/filippotoso/resource-permissions/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require filippo-toso/resource-permissions
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="resource-permissions-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="resource-permissions-config"
```

This is the contents of the published config file:

```php
use Carbon\Carbon;
use Illuminate\Support\Str;

return [
    /**
     * The name of the tables used by the package
     */
    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'permission_role' => 'permission_role',
        'role_user' => 'role_user',
        'permission_user' => 'permission_user',
    ],

    'models' => [
        // @phpstan-ignore-next-line
        'user' => \App\Models\User::class, // Default user class
        'role' => \FilippoToso\ResourcePermissions\Models\Role::class,
        'permission' => \FilippoToso\ResourcePermissions\Models\Permission::class,
    ],

    // Use file finder in production, database finder in development
    'finder' => (env('APP_ENV') == 'production')
        ? \FilippoToso\ResourcePermissions\Finders\Strategies\FileFinder::class
        : \FilippoToso\ResourcePermissions\Finders\Strategies\DatabaseFinder::class,

    // The FileFinder will cache the results into files for a certain amount of time
    'cache' => [
        'folder' => Str::finish(storage_path('app/resource-permissions'), DIRECTORY_SEPARATOR),
        'ttl' => Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY,
    ],
];
```

## Usage

Add the `HasRolesAndPermissions` trait to your User model:

```php

use FilippoToso\ResourcePermissions\Models\Concerns\HasRolesAndPermissions;

// ...

class User
{
    use HasRolesAndPermissions;

    // ...
}
```
Now you can create a user, a permission, and a role. Then assign the permission to the role and assign the role to the user.


```php
$user = User::create([
    'name' => 'John Snow', 
    'email' => 'john.snow@nightswatch.local', 
    'password' => Hash::make('Ygritte'),
]);

$role = Role::create(['name' => 'lord-commander']);

$permission = Permission::create(['name' => 'lead']);

$role->assignPermission($permission);
$user->assignRole($role);

if ($user->hasPermission($permission)) {
    dump('I shall take no wife, hold no lands, father no children...');
} 
```

You can also assign permissions directly to the user:

```php
$user = User::create([
    'name' => 'John Snow', 
    'email' => 'john.snow@nightswatch.local', 
    'password' => Hash::make('Ygritte'),
]);

$permission = Permission::create(['name' => 'lead']);

$user->assignPermission($permission);

if ($user->hasPermission($permission)) {
    dump('I shall wear no crowns and win no glory...');
} 
```

Both `hasRole()` and `hasPermission()` methods accept as first parameter:

- a role/permission Model instance
- the numeric key of the role/permission Model instance
- a string with the role/permission name
- a BackednEnum with the role/permission name
- an array with any of the above

When you assign a role or a permission to a user, you can pass a second `$resource` parameter.
This is the uniqueness of this package.
You can assign a role or a permission to a user relative to a specific Eloquent model.
For instance a user can be assigned the `manager` role of an Organization and the `owner` role of a project.

This second parameter can be:

- a Model instance
- a ResourceData object (an internal representation of a resource)
- an array with a single element where the key is the Model class and the value is the numeric key of the model
- an array with any of the above

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Filippo Toso](https://github.com/filippotoso)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
