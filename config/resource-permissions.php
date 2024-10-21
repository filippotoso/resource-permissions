<?php

// config for FilippoToso/ResourcePermission

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
