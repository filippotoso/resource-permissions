<?php

namespace FilippoToso\ResourcePermissions\Models\Concerns;

use FilippoToso\ResourcePermissions\Models\Concerns\Support\HasPermissions;
use FilippoToso\ResourcePermissions\Models\Concerns\Support\HasRoles;

/**
 * Trait for your User models to add roles and permissions.
 */
trait HasRolesAndPermissions
{
    use HasPermissions, HasRoles;
}
