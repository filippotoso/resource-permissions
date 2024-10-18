<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Models\Pivots\RoleUserPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait RoleHasUser
{
    /**
     * Get all of the user for the role.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            config('resource-permission.models.role'),
            'user',
            config('resource-permission.tables.role_user')
        )->using(RoleUserPivot::class);
    }
}
