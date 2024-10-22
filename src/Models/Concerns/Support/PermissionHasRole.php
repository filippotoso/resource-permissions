<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns\Support;

use FilippoToso\ResourcePermissions\Finders\Finder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait PermissionHasRole
{
    /**
     * Get all of the roles for the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('resource-permissions.models.role'),
            config('resource-permissions.tables.permission_role'),
            'permission_id',
            'role_id',
        );
    }

    public static function bootedPermissionHasRole()
    {
        static::created(function ($model) {
            Finder::purgePermissionsCache();
        });

        static::updated(function ($model) {
            Finder::purgePermissionsCache();
        });

        static::deleted(function ($model) {
            Finder::purgeCache();
        });
    }
}
