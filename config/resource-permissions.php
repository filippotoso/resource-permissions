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
        // @phpstan-ignore-next-line
        'user' => \App\Models\User::class, // Default user class
        'role' => \FilippoToso\ResourcePermissions\Models\Role::class,
        'permission' => \FilippoToso\ResourcePermissions\Models\Permission::class,
    ],

    'finder' => (env('APP_ENV') == 'production')
        ? \FilippoToso\ResourcePermissions\Finders\Strategies\CacheFinder::class
        : \FilippoToso\ResourcePermissions\Finders\Strategies\DatabaseFinder::class,

    'cache' => [
        'prefix' => 'resource-permissions',
        'ttl' => 60 * 60 * 24,
    ]
];
