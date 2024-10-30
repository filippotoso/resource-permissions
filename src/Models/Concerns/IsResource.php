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
            $query->select(DB::raw(1))
                ->from($this->getTable())
                ->join(config('resource-permissions.tables.role_user'), function (JoinClause $join) use ($user, $rolesIds) {
                    $join->on(config('resource-permissions.tables.role_user') . '.resource_id', '=', $this->getTable() . '.' . $this->getKeyName())
                        ->where(config('resource-permissions.tables.role_user') . '.resource_type', '=', $this->getMorphClass())
                        ->where(config('resource-permissions.tables.role_user') . '.user_id', '=', $user->getKey())
                        ->where(config('resource-permissions.tables.role_user') . '.user_type', '=', $user->getMorphClass())
                        ->when(! is_null($rolesIds), function (JoinClause $join) use ($rolesIds) {
                            $join->whereIn(config('resource-permissions.tables.role_user') . '.role_id', $rolesIds);
                        });
                });
        });
    }

    public function scopeWhereHasUsersWithRole(Builder $query, $role = null)
    {
        $rolesIds = is_null($role) ? null : Helper::getRolesIds($role);

        $query->whereExists(function ($query) use ($rolesIds) {
            /** @var Model $this */
            $query->select(DB::raw(1))
                ->from($this->getTable())
                ->join(config('resource-permissions.tables.role_user'), function (JoinClause $join) use ($rolesIds) {
                    $join->on(config('resource-permissions.tables.role_user') . '.resource_id', '=', $this->getTable() . '.' . $this->getKeyName())
                        ->where(config('resource-permissions.tables.role_user') . '.resource_type', '=', $this->getMorphClass())
                        ->when(! is_null($rolesIds), function (JoinClause $join) use ($rolesIds) {
                            $join->whereIn(config('resource-permissions.tables.role_user') . '.role_id', $rolesIds);
                        });
                });
        });
    }
}
