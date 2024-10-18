<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Models\Pivots\PermissionUserPivot;
use FilippoToso\ResourcePermissions\Support\Helper;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait for your User models to add roles.
 */
trait HasRoles
{
    public function roles(): MorphToMany
    {
        return $this->morphedByMany(config('resource-permission.models.role'), 'user')
            ->using(PermissionUserPivot::class);
    }

    public function assignRole($role, $resource = null)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = Helper::getResources($resource);

        foreach ($rolesIds as $rolesId) {
            foreach ($resources as $resource) {
                Helper::attachRole($this, $rolesId, $resource);
            }
        }
    }

    public function removeRole($role, $resource = null)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = Helper::getResources($resource);

        foreach ($rolesIds as $rolesId) {
            foreach ($resources as $resource) {
                Helper::detachRole($this, $rolesId, $resource);
            }
        }
    }

    public function syncRoles($roles)
    {
        $this->roles()->sync($roles);
    }
}
