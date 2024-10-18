<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

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
        return $this->morphedByMany(config('resource-permission.models.permission'), 'user')
            ->using(PermissionUserPivot::class);
    }

    public function assignPermission($permission, $resource = null)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $resources = Helper::getResources($resource);

        foreach ($permissionsIds as $permissionId) {
            foreach ($resources as $resource) {
                Helper::attachPermission($this, $permissionId, $resource);
            }
        }
    }

    public function removePermission($permission, $resource = null)
    {
        $permissionsIds = Helper::getPermissionsIds($permission);
        $resources = Helper::getResources($resource);

        foreach ($permissionsIds as $permissionId) {
            foreach ($resources as $resource) {
                Helper::detachPermission($this, $permissionId, $resource);
            }
        }
    }

    public function syncPermissions($permissions)
    {
        $this->permissions()->sync($permissions);
    }
}
