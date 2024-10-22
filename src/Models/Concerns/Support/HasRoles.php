<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns\Support;

use FilippoToso\ResourcePermissions\Finders\Finder;
use FilippoToso\ResourcePermissions\Models\Pivots\RoleUserPivot;
use FilippoToso\ResourcePermissions\Support\Helper;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait for your User models to add roles.
 */
trait HasRoles
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(config('resource-permissions.models.role'), 'user', config('resource-permissions.tables.role_user'))
            ->using(RoleUserPivot::class);
    }

    public function hasRole($role, $resource = null, $or = true)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        return Finder::hasRole($this, $rolesIds, $resources, $or);
    }

    public function assignRole($role, $resource = null)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        foreach ($rolesIds as $rolesId) {
            foreach ($resources as $resource) {
                Helper::attachRole($this, $rolesId, $resource);
            }
        }

        Finder::purgeUserCache($this);
    }

    public function removeRole($role, $resource = null)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        foreach ($rolesIds as $rolesId) {
            foreach ($resources as $resource) {
                Helper::detachRole($this, $rolesId, $resource);
            }
        }

        Finder::purgeUserCache($this);
    }

    public function syncRoles($roles)
    {
        $this->roles()->sync($roles);

        Finder::purgeUserCache($this);
    }
}
