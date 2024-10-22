<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns\Support;

use FilippoToso\ResourcePermissions\Finders\Finder;
use FilippoToso\ResourcePermissions\Models\Pivots\PermissionUserPivot;
use FilippoToso\ResourcePermissions\Support\Helper;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait for your User models to add permissions.
 */
trait HasPermissions
{
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(config('resource-permissions.models.permission'), 'user', config('resource-permissions.tables.permission_user'))
            ->withPivot('resource_id', 'resource_type')
            ->using(PermissionUserPivot::class);
    }

    public function hasPermission($permission, $resource = null, $or = true)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        return Finder::hasPermission($this, $permissionsIds, $resources, $or);
    }

    public function assignPermission($permission, $resource = null)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        foreach ($permissionsIds as $permissionId) {
            foreach ($resources as $resource) {
                Helper::attachPermission($this, $permissionId, $resource);
            }
        }

        Finder::purgeUserCache($this);
    }

    public function removePermission($permission, $resource = null)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $resources = is_null($resource) ? [$resource] : Helper::getResources($resource);

        foreach ($permissionsIds as $permissionId) {
            foreach ($resources as $resource) {
                Helper::detachPermission($this, $permissionId, $resource);
            }
        }

        Finder::purgeUserCache($this);
    }

    public function syncPermissions($permissions)
    {
        $this->permissions()->sync($permissions);

        Finder::purgeUserCache($this);
    }
}
