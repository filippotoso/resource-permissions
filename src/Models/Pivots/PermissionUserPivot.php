<?php

namespace FilippoToso\ResourcePermissions\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PermissionUserPivot extends MorphPivot
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
        return config('resource-permissions.tables.permission_user', parent::getTable());
    }

    /**
     * Resource relation
     *
     * @return MorphTo
     */
    public function resource(): MorphTo
    {
        return $this->morphTo('resource');
    }
}
