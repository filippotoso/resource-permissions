<?php

use Workbench\App\Models\Role;
use Workbench\App\Models\User;

it('can assign a role', function () {
    $user = User::create(['name' => 'John Snow', 'email' => 'john@snow.local', 'password' => 'password']);
    $role = Role::create(['name' => 'admin']);

    $user->assignRole($role);

    expect($user->roles()->count())->toBe(1);
});
