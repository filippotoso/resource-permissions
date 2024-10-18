<?php

namespace FilippoToso\ResourcePermissions\Finders;

use FilippoToso\ResourcePermissions\Finders\Contracts\Finder as Contract;
use FilippoToso\ResourcePermissions\Finders\Strategies\CacheFinder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @method static hasRole(Model $user, $roleIds, array $resources = [null], $or = true);
 * @method static hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true);
 */
class Finder implements Contract
{
    public static function hasRole(Model $user, $roleIds, array $resources = [null], $or = true): bool
    {
        $class = config('resource-permissions.finder');
        return $class::hasRole($user, $roleIds, $resources, $or);
    }

    public static function hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true): bool
    {
        $class = config('resource-permissions.finder');
        return $class::hasPermission($user, $permissionIds, $resources, $or);
    }

    public static function purgeUserCache(Model $user)
    {
        $prefix = config('resource-permissions.cache.prefix') ?? 'resource-permissions';

        Cache::forget(sprintf(CacheFinder::USER_ROLES_KEY, $prefix, $user->getKey()));
        Cache::forget(sprintf(CacheFinder::USER_PERMISSIONS_KEY, $prefix, $user->getKey()));
    }
}
