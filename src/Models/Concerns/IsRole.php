<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Models\Concerns\Support\RoleHasPermission;
use FilippoToso\ResourcePermissions\Models\Concerns\Support\RoleHasUser;

/**
 * Trait for your User models to add roles and permissions.
 */
trait IsRole
{
    use RoleHasPermission, RoleHasUser;
}
