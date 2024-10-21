<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Models\Concerns\Support\PermissionHasRole;
use FilippoToso\ResourcePermissions\Models\Concerns\Support\PermissionHasUser;

/**
 * Trait for your User models to add roles and permissions.
 */
trait IsPermission
{
    use PermissionHasRole, PermissionHasUser;
}
