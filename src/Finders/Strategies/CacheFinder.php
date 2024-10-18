<?php

namespace FilippoToso\ResourcePermissions\Finders\Strategies;

use Carbon\Carbon;
use FilippoToso\ResourcePermissions\Finders\Contracts\Finder;
use FilippoToso\ResourcePermissions\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheFinder implements Finder
{
    public const USER_ROLES_KEY = '%s-user-roles-%s';

    public const USER_PERMISSIONS_KEY = '%s-user-permissions-%s';

    public static function hasRole(Model $user, $roleIds, array $resources = [null], $or = true): bool
    {
        $prefix = config('resource-permissions.cache.prefix') ?? 'resource-permissions';
        $ttl = config('resource-permissions.cache.ttl') ?? Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;

        $userRoles = Cache::remember(sprintf(self::USER_ROLES_KEY, $prefix, $user->getKey()), $ttl, function () use ($user) {
            $roles = $user->roles()->get();

            $cache = [];

            foreach ($roles as $role) {
                $cache[$role->getKey()][$role->pivot->resource_type ?? ''][$role->pivot->resource_id ?? ''] = true;
            }

            return $cache;
        });

        $result = true;

        foreach ($roleIds as $roleId) {
            foreach ($resources as $resource) {

                $current = isset($userRoles[$roleId][$resource?->type ?? ''][$resource?->id ?? '']);

                $result = $result && $current;

                // Stops at the first success if it's an $or matching
                if ($or && $current) {
                    return true;
                }
            }
        }

        return $result;
    }

    public static function hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true): bool
    {
        $prefix = config('resource-permissions.cache.prefix') ?? 'resource-permissions';
        $ttl = config('resource-permissions.cache.ttl') ?? Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;

        $userPermissions = Cache::remember(sprintf(self::USER_PERMISSIONS_KEY, $prefix, $user->getKey()), $ttl, function () use ($user) {

            $cache = [];

            $permissions = $user->permissions()->get();

            foreach ($permissions as $permission) {
                $cache[$permission->getKey()][$permission->pivot->resource_type][$permission->pivot->resource_id] = true;
            }

            $table = config('resource-permissions.tables.role_user');

            $permissions = Permission::withWhereHas('roles.users', function ($query) use ($user, $table) {
                $query->where($table.'.user_type', '=', $user->getMorphClass())
                    ->where($table.'.user_id', '=', $user->getKey());
            })->get();

            foreach ($permissions as $permission) {
                foreach ($permission->roles as $role) {
                    foreach ($role->users as $user) {
                        $cache[$permission->getKey()][$user->pivot->resource_type ?? ''][$user->pivot->resource_id ?? ''] = true;
                    }
                }
            }

            return $cache;
        });

        $result = true;

        foreach ($permissionIds as $permissionId) {
            foreach ($resources as $resource) {

                $current = isset($userPermissions[$permissionId][$resource?->type ?? ''][$resource?->id ?? '']);

                $result = $result && $current;

                // Stops at the first success if it's an $or matching
                if ($or && $current) {
                    return true;
                }
            }
        }

        return $result;
    }
}
