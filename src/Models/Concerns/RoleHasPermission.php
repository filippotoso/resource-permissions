<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RoleHasPermission
{
    /**
     * Get all of the permissions for the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('resource-permission.models.role'),
            config('resource-permission.tables.permission_role'),
            'role_id',
            'permission_id',
            'id',
            'id'
        );
    }
}
