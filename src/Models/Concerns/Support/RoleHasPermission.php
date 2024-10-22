<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns\Support;

use FilippoToso\ResourcePermissions\Finders\Finder;
use FilippoToso\ResourcePermissions\Support\Helper;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RoleHasPermission
{
    /**
     * Get all of the permissions for the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('resource-permissions.models.permission'),
            config('resource-permissions.tables.permission_role'),
            'role_id',
            'permission_id',
        );
    }

    public function assignPermission($permission)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $this->permissions()->syncWithoutDetaching($permissionsIds);

        Finder::purgeRolesCache();
    }

    public function removePermission($permission)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $this->permissions()->detach($permissionsIds);

        Finder::purgeRolesCache();
    }

    public static function bootedHasRoles()
    {
        static::created(function ($model) {
            Finder::purgeRolesCache();
        });
        static::updated(function ($model) {
            Finder::purgeRolesCache();
        });

        static::deleted(function ($model) {
            Finder::purgeRolesCache();
        });
    }
}
