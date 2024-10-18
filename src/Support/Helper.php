<?php

namespace FilippoToso\ResourcePermissions\Support;

use BackedEnum;
use FilippoToso\ResourcePermissions\Data\ResourceData;
use Illuminate\Database\Eloquent\Model;

class Helper
{
    // Model, id, name
    public static function getRolesIds($roles): array
    {
        $results = [];

        $class = config('resource-permissions.models.role');

        $roles = is_iterable($roles) ? $roles : [$roles];

        $names = [];

        foreach ($roles as $role) {
            if (is_a($role, $class)) {
                $results[] = $role->getKey();
            }

            if (is_numeric($role)) {
                $results[] = (int) $role;
            }

            if (is_string($role)) {
                $names[] = $role;
            }

            if (is_a($role, BackedEnum::class)) {
                $results[] = $role->value;
            }
        }

        if (count($names) > 0) {
            $results = array_merge(
                $results,
                $class::whereIn('name', $names)->pluck('id')->toArray()
            );
        }

        return array_unique($results);
    }

    public static function getPermissionsIds($permissions): array
    {
        $results = [];

        $class = config('resource-permissions.models.permission');

        $permissions = is_iterable($permissions) ? $permissions : [$permissions];

        $names = [];

        foreach ($permissions as $permission) {
            if (is_a($permission, $class)) {
                $results[] = $permission->getKey();
            }

            if (is_numeric($permission)) {
                $results[] = (int) $permission;
            }

            if (is_string($permission)) {
                $names[] = $permission;
            }

            if (is_a($permission, BackedEnum::class)) {
                $results[] = $permission->value;
            }
        }

        if (count($names) > 0) {
            $results = array_merge(
                $results,
                $class::whereIn('name', $names)->pluck('id')->toArray()
            );
        }

        return array_unique($results);
    }

    public static function getResources($resources): array
    {
        return ResourceData::resources($resources);
    }

    public static function attachPermission(Model $user, int $permissionId, ?ResourceData $resource = null)
    {
        $user->permissions()->attach($permissionId, [
            'resource_type' => $resource?->type,
            'resource_id' => $resource?->id,
        ]);
    }

    public static function detachPermission(Model $user, int $permissionId, ?ResourceData $resource = null)
    {
        $user->permissions()
            ->wherePivot('permission_id', '=', $permissionId)
            ->wherePivot('resource_type', '=', $resource?->type)
            ->wherePivot('resource_id', '=', $resource?->id)
            ->detach();
    }

    public static function attachRole(Model $user, int $roleId, ?ResourceData $resource = null)
    {
        $user->roles()->attach($roleId, [
            'resource_type' => $resource?->type,
            'resource_id' => $resource?->id,
        ]);
    }

    public static function detachRole(Model $user, int $roleId, ?ResourceData $resource = null)
    {
        $user->roles()
            ->wherePivot('role_id', '=', $roleId)
            ->wherePivot('resource_type', '=', $resource?->type)
            ->wherePivot('resource_id', '=', $resource?->id)
            ->detach();
    }
}
