<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

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
    }

    public function removePermission($permission)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $this->permissions()->detach($permissionsIds);
    }
}
