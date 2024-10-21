<?php

use FilippoToso\ResourcePermissions\Finders\Finder;
use FilippoToso\ResourcePermissions\Finders\Strategies\CacheFinder;
use FilippoToso\ResourcePermissions\Finders\Strategies\FileFinder;
use FilippoToso\ResourcePermissions\Models\Permission;
use Workbench\App\Models\Role;
use Workbench\App\Models\User;

it('can assign and remove a role to a user', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role = Role::create(['name' => 'lord-commander']);

    $user->assignRole($role);

    expect($user->roles()->count())->toBe(1);

    $user->removeRole($role);

    expect($user->roles()->count())->toBe(0);
});

it('can assign and remove a permission to a user', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $permission = Permission::create(['name' => 'resuscitate']);

    $user->assignPermission($permission);

    expect($user->permissions()->count())->toBe(1);

    $user->removePermission($permission);

    expect($user->permissions()->count())->toBe(0);
});

it('can assign and remove a permission to a role', function () {
    $role = Role::create(['name' => 'lord-commander']);
    $permission = Permission::create(['name' => 'resuscitate']);

    $role->assignPermission($permission);

    expect($role->permissions()->count())->toBe(1);

    $role->removePermission($permission);

    expect($role->permissions()->count())->toBe(0);
});

it('can assign a role to a user and checks it', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role1 = Role::create(['name' => 'lord-commander']);
    $role2 = Role::create(['name' => 'heir']);

    $user->assignRole($role1);

    expect($user->hasRole($role1))->toBeTrue();
    expect($user->hasRole($role2))->toBeFalse();
});

it('can assign a permission to a user and checks it', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'die']);

    $user->assignPermission($permission1);

    expect($user->hasPermission($permission1))->toBeTrue();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a permission to a user through a role', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role = Role::create(['name' => 'lord-commander']);

    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'lead']);

    $role->assignPermission($permission1);
    $user->assignRole($role);

    expect($user->hasPermission($permission1))->toBeTrue();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a role to a user and checks it (with cache)', function () {

    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role1 = Role::create(['name' => 'lord-commander']);
    $role2 = Role::create(['name' => 'heir']);

    $user->assignRole($role1);

    Finder::purgeUserCache($user);

    expect($user->hasRole($role1))->toBeTrue();
    expect($user->hasRole($role2))->toBeFalse();
});

it('can assign a permission to a user and checks it (with cache)', function () {

    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'die']);

    $user->assignPermission($permission1);

    Finder::purgeUserCache($user);

    expect($user->hasPermission($permission1))->toBeTrue();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a permission to a user through a role (with cache)', function () {
    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role = Role::create(['name' => 'lord-commander']);

    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'lead']);

    $role->assignPermission($permission1);
    $user->assignRole($role);

    Finder::purgeUserCache($user);

    expect($user->hasPermission($permission1))->toBeTrue();
    expect($user->hasPermission($permission2))->toBeFalse();
});
