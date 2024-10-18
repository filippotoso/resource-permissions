<?php

// config for FilippoToso/ResourcePermission

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
        'user' => \App\Models\User::class, // Default user class
        'role' => \FilippoToso\ResourcePermissions\Models\Role::class,
        'permission' => \FilippoToso\ResourcePermissions\Models\Permission::class,
    ],

    'finder' => (env('APP_ENV') == 'production')
        ? \FilippoToso\ResourcePermissions\Checkers\Strategies\CacheChecker::class
        : \FilippoToso\ResourcePermissions\Checkers\Strategies\DatabaseChecker::class,
];
