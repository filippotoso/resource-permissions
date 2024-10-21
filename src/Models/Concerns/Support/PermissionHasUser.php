<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns\Support;

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
            config('resource-permissions.models.user'),
            'user',
            config('resource-permissions.tables.permission_user')
        )->using(PermissionUserPivot::class);
    }
}
