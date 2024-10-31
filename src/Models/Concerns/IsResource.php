<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Support\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * Trait for your Resouce models to get users models
 */
trait IsResource
{
    public function scopeWhereHasUserWithRole(Builder $query, Model $user, $role = null)
    {
        $rolesIds = is_null($role) ? null : Helper::getRolesIds($role);

        $query->whereExists(function ($query) use ($user, $rolesIds) {
            /** @var Model $this */
            $query->select('resource_id')
                ->from(config('resource-permissions.tables.role_user'))
                ->whereColumn(config('resource-permissions.tables.role_user') . '.resource_id', '=', $this->getTable() . '.' . $this->getKeyName())
                ->where(config('resource-permissions.tables.role_user') . '.resource_type', '=', $this->getMorphClass())
                ->where(config('resource-permissions.tables.role_user') . '.user_id', '=', $user->getKey())
                ->where(config('resource-permissions.tables.role_user') . '.user_type', '=', $user->getMorphClass())
                ->when(! is_null($rolesIds), function ($query) use ($rolesIds) {
                    $query->whereIn(config('resource-permissions.tables.role_user') . '.role_id', $rolesIds);
                });
        });
    }

    public function scopeWhereHasUsersWithRole(Builder $query, $role = null)
    {
        $rolesIds = is_null($role) ? null : Helper::getRolesIds($role);

        $query->whereExists(function ($query) use ($rolesIds) {

            /** @var Model $this */
            $query->select('resource_id')
                ->from(config('resource-permissions.tables.role_user'))
                ->whereColumn(config('resource-permissions.tables.role_user') . '.resource_id', '=', $this->getTable() . '.' . $this->getKeyName())
                ->where(config('resource-permissions.tables.role_user') . '.resource_type', '=', $this->getMorphClass())
                ->when(! is_null($rolesIds), function ($query) use ($rolesIds) {
                    $query->whereIn(config('resource-permissions.tables.role_user') . '.role_id', $rolesIds);
                });
        });
    }
}
