<?php

namespace FilippoToso\ResourcePermissions\Finders\Strategies;

use FilippoToso\ResourcePermissions\Finders\Contracts\Finder;
use Illuminate\Database\Eloquent\Model;

class DatabaseFinder implements Finder
{
    public static function hasRole(Model $user, $roleIds, array $resources = [null], $or = true): bool
    {
        $query = $user->roles();

        $method = ($or) ? 'orWhere' : 'where';

        $table = config('resource-permissions.tables.role_user');

        $query->where(function ($query) use ($roleIds, $resources, $table, $method) {
            foreach ($roleIds as $roleId) {
                foreach ($resources as $resource) {
                    $query->{$method}(function ($query) use ($roleId, $resource, $table) {
                        $query->where($table.'.role_id', $roleId)
                            ->where($table.'.resource_type', $resource?->type)
                            ->where($table.'.resource_id', $resource?->id);
                    });
                }
            }
        });

        return $query->exists();
    }

    public static function hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true): bool
    {
        $query = $user->permissions();

        $method = ($or) ? 'orWhere' : 'where';

        $permissionUserTable = config('resource-permissions.tables.permission_user');

        $query->where(function ($query) use ($permissionIds, $resources, $permissionUserTable, $method) {
            foreach ($permissionIds as $permissionId) {
                foreach ($resources as $resource) {
                    $query->{$method}(function ($query) use ($permissionId, $resource, $permissionUserTable) {
                        $query->where($permissionUserTable.'.permission_id', $permissionId)
                            ->where($permissionUserTable.'.resource_type', $resource?->type)
                            ->where($permissionUserTable.'.resource_id', $resource?->id);
                    });
                }
            }
        });

        if ($query->exists()) {
            return true;
        }

        $permissionRoleTable = config('resource-permissions.tables.permission_role');
        $roleUserTable = config('resource-permissions.tables.role_user');

        $query = $user->roles();

        $query->where(function ($query) use ($permissionIds, $resources, $roleUserTable, $permissionRoleTable, $method) {
            foreach ($resources as $resource) {
                $query->{$method}(function ($query) use ($resource, $roleUserTable, $permissionRoleTable, $permissionIds) {
                    $query->where($roleUserTable.'.resource_type', $resource?->type)
                        ->where($roleUserTable.'.resource_id', $resource?->id)
                        ->whereHas('permissions', function ($query) use ($permissionIds, $permissionRoleTable) {
                            foreach ($permissionIds as $permissionId) {
                                $query->where($permissionRoleTable.'.permission_id', $permissionId);
                            }
                        });
                });
            }
        });

        return $query->exists();
    }

    public static function rolesIdsByName(array $names = [])
    {
        $class = config('resource-permissions.models.role');

        return $class::whereIn('name', $names)->pluck('id')->toArray();
    }

    public static function permissionsIdsByName(array $names = [])
    {
        $class = config('resource-permissions.models.permission');

        return $class::whereIn('name', $names)->pluck('id')->toArray();
    }
}
