<?php

namespace FilippoToso\ResourcePermissions\Finders;

use FilippoToso\ResourcePermissions\Finders\Strategies\FileFinder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static hasRole(Model $user, $roleIds, array $resources = [null], $or = true, $strict = true);
 * @method static hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true, $strict = true);
 */
class Finder implements Contracts\Finder
{
    public static function hasRole(Model $user, $roleIds, array $resources = [null], $or = true, $strict = true): bool
    {
        $class = config('resource-permissions.finder');

        return $class::hasRole($user, $roleIds, $resources, $or, $strict);
    }

    public static function hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true, $strict = true): bool
    {
        $class = config('resource-permissions.finder');

        return $class::hasPermission($user, $permissionIds, $resources, $or, $strict);
    }

    public static function purgeCache()
    {
        static::purgeRolesCache();
        static::purgeUsersCache();
    }

    public static function rolesIdsByName(array $names = [])
    {
        $class = config('resource-permissions.finder');

        return $class::rolesIdsByName($names);
    }

    public static function permissionsIdsByName(array $names = [])
    {
        $class = config('resource-permissions.finder');

        return $class::permissionsIdsByName($names);
    }

    public static function purgeRolesCache()
    {
        FileFinder::purgeRolesCache();
    }

    public static function purgePermissionsCache()
    {
        FileFinder::purgePermissionsCache();
    }

    public static function purgeUsersCache()
    {
        FileFinder::purgeUsersCache();
    }

    public static function purgeUserCache(Model $user)
    {
        FileFinder::purgeUserCache($user);
    }
}
