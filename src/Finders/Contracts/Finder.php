<?php

namespace FilippoToso\ResourcePermissions\Finders\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Finder
{
    public static function hasRole(Model $user, $roleIds, array $resources = [null], $or = true): bool;

    public static function hasPermission(Model $user, $permissionIds, array $resources = [null], $or = true): bool;
}
