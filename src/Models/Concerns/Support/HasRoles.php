<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns\Support;

use FilippoToso\ResourcePermissions\Finders\Finder;
use FilippoToso\ResourcePermissions\Models\Pivots\RoleUserPivot;
use FilippoToso\ResourcePermissions\Support\Helper;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for your User models to add roles.
 */
trait HasRoles
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(config('resource-permissions.models.role'), 'user', config('resource-permissions.tables.role_user'))
            ->withPivot('resource_id', 'resource_type')
            ->using(RoleUserPivot::class);
    }

    public function scopeWhereHasRoleWithResource(Builder $query, $role, $resource, $or = true)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        $table = config('resource-permissions.tables.role_user');

        $query->whereHas('roles', function ($query) use ($rolesIds, $resources, $or, $table) {
            $query->whereIn($table . '.role_id', $rolesIds)
                ->where(function ($query) use ($resources, $or, $table) {
                    $where = ($or) ? 'orWhere' : 'where';

                    foreach ($resources as $resource) {
                        $query->{$where}($table . '.resource_type', '=', $resource->type)
                            ->{$where}($table . '.resource_id', '=', $resource->id);
                    }
                });
        });
    }

    public function hasRole($role, $resource = null, $or = true, $strict = true)
    {
        $rolesIds = Helper::getRolesIds($role);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        if (count($resources) == 0) {
            return false;
        }

        return Finder::hasRole($this, $rolesIds, $resources, $or, $strict);
    }

    public function hasRoleWithAnyResource($role, $or = true)
    {
        return $this->hasRole($role, null, $or, false);
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
