<?php

namespace FilippoToso\ResourcePermissions\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionUserPivot extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('resource-permission.tables.permission_user', parent::getTable());
    }

    /**
     * Resource relation
     */
    public function resource(): MorphTo
    {
        return $this->morphTo('resource');
    }
}
