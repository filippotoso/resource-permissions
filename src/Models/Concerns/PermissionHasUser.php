<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Models\Pivots\PermissionUserPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait PermissionHasUser
{
    /**
     * Get all of the user for the permission.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            config('resource-permission.models.user'),
            'user',
            config('resource-permission.tables.permission_user')
        )->using(PermissionUserPivot::class);
    }
}
