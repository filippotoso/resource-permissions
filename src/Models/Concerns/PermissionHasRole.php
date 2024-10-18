<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait PermissionHasRole
{
    /**
     * Get all of the roles for the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('resource-permission.models.role'),
            config('resource-permission.tables.permission_role'),
            'permission_id',
            'role_id',
            'id',
            'id'
        );
    }
}
