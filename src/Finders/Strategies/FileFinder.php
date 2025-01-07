<?php

namespace FilippoToso\ResourcePermissions\Finders\Strategies;

use Carbon\Carbon;
use FilippoToso\ResourcePermissions\Finders\Contracts\Finder;
use FilippoToso\ResourcePermissions\Support\File;
use Illuminate\Database\Eloquent\Model;

class FileFinder implements Finder
{
    /**
     * Contains roles with their permissions mapped using names and ids as keys
     */
    protected const ROLES_PATH = 'roles.php';

    /**
     * Contains permissions contents
     */
    protected const PERMISSIONS_PATH = 'permissions.php';

    /**
     * The folder used to store user's roles and permissions
     */
    protected const USERS_DIRECTORY = 'users';

    /**
     * Contains user's roles and user's direct permissions mapped using names and ids as keys
     */
    protected const USERS_PATH = self::USERS_DIRECTORY . '/%s/%s.php';

    protected static function rolesCache()
    {
        $ttl = config('resource-permissions.file.ttl') ?? Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;

        $path = config('resource-permissions.cache.folder') . static::ROLES_PATH;

        return File::remember($path, $ttl, function () {
            $class = config('resource-permissions.models.role');
            $roles = $class::with('permissions:id')->get();

            $cache = [
                'roles' => [],
                'names' => [],
            ];

            foreach ($roles as $role) {
                $cache['roles'][$role->getKey()] = $role->permissions->pluck('id')->toArray();
                $cache['names'][$role->name] = $role->getKey();
            }

            return $cache;
        });
    }

    protected static function permissionsCache()
    {
        $ttl = config('resource-permissions.file.ttl') ?? Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;

        $path = config('resource-permissions.cache.folder') . static::PERMISSIONS_PATH;

        return File::remember($path, $ttl, function () {
            $class = config('resource-permissions.models.permission');
            $permissions = $class::with('roles')->get();

            $cache = [
                'permissions' => [],
                'names' => [],
            ];

            foreach ($permissions as $permission) {
                $cache['permissions'][$permission->getKey()] = $permission->roles->pluck('id')->toArray();
                $cache['names'][$permission->name] = $permission->getKey();
            }

            return $cache;
        });
    }

    protected static function userCache(Model $user)
    {
        $ttl = config('resource-permissions.file.ttl') ?? Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;

        $path = static::userCachePath($user);

        return File::remember($path, $ttl, function () use ($user) {
            $roles = $user->roles()->get();

            $cache = [
                'roles' => [],
                'permissions' => [],
            ];

            foreach ($roles as $role) {
                $cache['roles'][$role->getKey()][$role->pivot->resource_type ?? ''][$role->pivot->resource_id ?? ''] = true;
            }

            $permissions = $user->permissions()->get();

            foreach ($permissions as $permission) {
                $cache['permissions'][$permission->getKey()][$permission->pivot->resource_type][$permission->pivot->resource_id] = true;
            }

            return $cache;
        });
    }

    public static function hasRole(Model $user, $roleIds, array $resources = [null], $or = true, $strict = true): bool
    {
        $userCache = static::userCache($user);
        $rolesCache = static::rolesCache();

        $result = true;

        foreach ($roleIds as $roleId) {
            foreach ($resources as $resource) {

                // If the roles exists and has been assigned to the user
                $current = ($strict)
                    ? isset($rolesCache['roles'][$roleId]) && isset($userCache['roles'][$roleId][$resource?->type ?? ''][$resource?->id ?? ''])
                    : isset($rolesCache['roles'][$roleId]) && isset($userCache['roles'][$roleId]);

                $result = $result && $current;

                // Stops at the first success if it's an $or matching
                if ($or && $current) {
                    return true;
                }
            }
        }

        return $result;
    }

    public static function hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true, $strict = true): bool
    {
        $userCache = static::userCache($user);

        $rolesCache = static::rolesCache();

        $permissionsCache = static::permissionsCache();

        $mapped = $userCache['permissions'];

        foreach ($userCache['roles'] as $roleId => $roleResources) {
            foreach ($roleResources as $roleResourceType => $roleResourceIds) {
                foreach ($roleResourceIds as $roleResourceId => $value) {
                    $rolePermissionsIds = $rolesCache['roles'][$roleId] ?? [];
                    foreach ($rolePermissionsIds as $rolePermissionsId) {
                        $mapped[$rolePermissionsId][$roleResourceType][$roleResourceId] = true;
                    }
                }
            }
        }

        $result = true;

        foreach ($permissionIds as $permissionId) {
            foreach ($resources as $resource) {

                // If the permission exists and has been assigned to the user
                $current = ($strict)
                    ? isset($permissionsCache['permissions'][$permissionId]) && isset($mapped[$permissionId][$resource?->type ?? ''][$resource?->id ?? ''])
                    : isset($permissionsCache['permissions'][$permissionId]) && isset($mapped[$permissionId]);

                $result = $result && $current;

                // Stops at the first success if it's an $or matching
                if ($or && $current) {
                    return true;
                }
            }
        }

        return $result;
    }

    public static function rolesIdsByName(array $names = [])
    {
        $roles = static::rolesCache();

        return array_intersect_key($roles['names'], array_combine($names, $names));
    }

    public static function permissionsIdsByName(array $names = [])
    {
        $permissions = static::permissionsCache();

        return array_intersect_key($permissions['names'], array_combine($names, $names));
    }

    public static function purgeUserCache(Model $user)
    {
        $subFolder = substr(sha1($user->getKey()), 0, 2);

        File::delete(config('resource-permissions.cache.folder') . sprintf(self::USERS_PATH, $subFolder, $user->getKey()));
    }

    public static function purgeUsersCache()
    {
        File::deleteDirectory(config('resource-permissions.cache.folder') . self::USERS_DIRECTORY);
    }

    public static function purgeRolesCache()
    {
        File::delete(config('resource-permissions.cache.folder') . self::ROLES_PATH);
    }

    public static function purgePermissionsCache()
    {
        File::delete(config('resource-permissions.cache.folder') . self::PERMISSIONS_PATH);
    }

    protected static function userCachePath(Model $user)
    {
        $subFolder = substr(sha1($user->getKey()), 0, 2);

        return config('resource-permissions.cache.folder') . sprintf(self::USERS_PATH, $subFolder, $user->getKey());
    }
}
