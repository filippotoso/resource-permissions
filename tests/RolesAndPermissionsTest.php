<?php

use FilippoToso\ResourcePermissions\Data\ResourceData;
use FilippoToso\ResourcePermissions\Finders\Strategies\FileFinder;
use FilippoToso\ResourcePermissions\Models\Permission;
use Illuminate\Support\Facades\DB;
use Workbench\App\Models\Project;
use Workbench\App\Models\Role;
use Workbench\App\Models\User;

it('can assign create a resource and parse it correctly', function () {

    $resource = Project::create([
        'name' => 'The Wall',
    ]);

    $data = ResourceData::resources($resource);

    expect($data[0]->type)->toBe(Project::class);
    expect($data[0]->id)->toBe($resource->id);

    ResourceData::resources([
        Project::class => $resource->id,
    ]);

    expect($data[0]->type)->toBe(Project::class);
    expect($data[0]->id)->toBe($resource->id);
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

it('can assign a role to a user and checks it (with resource)', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role1 = Role::create(['name' => 'lord-commander']);
    $role2 = Role::create(['name' => 'heir']);

    $project = Project::create([
        'name' => 'The Wall',
    ]);

    $user->assignRole($role1, $project);

    expect($user->hasRole($role1, $project))->toBeTrue();
    expect($user->hasRole($role1))->toBeFalse();
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

it('can assign a permission to a user and checks it (with resource)', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'die']);

    $project = Project::create([
        'name' => 'The Wall',
    ]);

    $user->assignPermission($permission1, $project);

    expect($user->hasPermission($permission1, $project))->toBeTrue();
    expect($user->hasPermission($permission1))->toBeFalse();
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

it('can assign a permission to a user through a role (with resource)', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role = Role::create(['name' => 'lord-commander']);

    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'lead']);

    $project = Project::create([
        'name' => 'The Wall',
    ]);

    $role->assignPermission($permission1);
    $user->assignRole($role, $project);

    expect($user->hasPermission($permission1, $project))->toBeTrue();
    expect($user->hasPermission($permission1))->toBeFalse();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a role to a user and checks it (with cache)', function () {

    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role1 = Role::create(['name' => 'lord-commander']);
    $role2 = Role::create(['name' => 'heir']);

    $user->assignRole($role1);

    expect($user->hasRole($role1))->toBeTrue();
    expect($user->hasRole($role2))->toBeFalse();
});

it('can assign a permission to a user and checks it (with cache)', function () {

    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'die']);

    $user->assignPermission($permission1);

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

    expect($user->hasPermission($permission1))->toBeTrue();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a role to a user and checks it (with resource and cache)', function () {
    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role1 = Role::create(['name' => 'lord-commander']);
    $role2 = Role::create(['name' => 'heir']);

    $project = Project::create([
        'name' => 'The Wall',
    ]);

    $user->assignRole($role1, $project);

    expect($user->hasRole($role1, $project))->toBeTrue();
    expect($user->hasRole($role1))->toBeFalse();
    expect($user->hasRole($role2))->toBeFalse();
});

it('can assign a permission to a user and checks it (with resource and cache)', function () {
    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'die']);

    $project = Project::create([
        'name' => 'The Wall',
    ]);

    $user->assignPermission($permission1, $project);

    expect($user->hasPermission($permission1, $project))->toBeTrue();
    expect($user->hasPermission($permission1))->toBeFalse();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a permission to a user through a role (with resource and cache)', function () {
    config()->set('resource-permissions.finder', FileFinder::class);

    $user = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $role = Role::create(['name' => 'lord-commander']);

    $permission1 = Permission::create(['name' => 'resuscitate']);
    $permission2 = Permission::create(['name' => 'lead']);

    $project = Project::create([
        'name' => 'The Wall',
    ]);

    $role->assignPermission($permission1);
    $user->assignRole($role, $project);

    expect($user->hasPermission($permission1, $project))->toBeTrue();
    expect($user->hasPermission($permission1))->toBeFalse();
    expect($user->hasPermission($permission2))->toBeFalse();
});

it('can assign a role to a resource and get the resource from the role', function () {
    $user1 = User::create(['name' => 'John Snow', 'email' => 'john.snow@nightswatch.local', 'password' => 'Ygritte']);
    $user2 = User::create(['name' => 'Ygritte Styr', 'email' => 'ygritte.styr@wildling.local', 'password' => 'John']);

    $role1 = Role::create(['name' => 'lord-commander']);
    $role2 = Role::create(['name' => 'heir']);

    $project1 = Project::create([
        'name' => 'The Wall',
    ]);

    $project2 = Project::create([
        'name' => 'Castle Black',
    ]);

    $user1->assignRole($role1, $project1);

    expect(Project::whereHasUserWithRole($user1)->count())->toBe(1);
    expect(Project::whereHasUserWithRole($user2)->count())->toBe(0);

    expect(Project::whereHasUserWithRole($user1, $role1)->count())->toBe(1);
    expect(Project::whereHasUserWithRole($user1, $role2)->count())->toBe(0);

    expect(Project::whereHasUserWithRole($user2, $role1)->count())->toBe(0);
    expect(Project::whereHasUserWithRole($user2, $role2)->count())->toBe(0);
});
