<?php

// config for FilippoToso/ResourcePermission

use App\Models\User;
use Carbon\Carbon;
use FilippoToso\ResourcePermissions\Finders\Strategies\DatabaseFinder;
use FilippoToso\ResourcePermissions\Finders\Strategies\FileFinder;
use FilippoToso\ResourcePermissions\Models\Permission;
use FilippoToso\ResourcePermissions\Models\Role;
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
        'user' => User::class, // Default user class
        'role' => Role::class,
        'permission' => Permission::class,
    ],

    // Use file finder in production, database finder in development
    'finder' => (env('APP_ENV') == 'production')
        ? FileFinder::class
        : DatabaseFinder::class,

    // The FileFinder will cache the results into files for a certain amount of time
    'cache' => [
        'folder' => Str::finish(storage_path('app/resource-permissions'), DIRECTORY_SEPARATOR),
        'ttl' => Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY,
    ],
];
