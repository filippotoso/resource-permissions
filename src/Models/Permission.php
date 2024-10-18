<?php

namespace FilippoToso\ResourcePermissions\Models;

use FilippoToso\ResourcePermissions\Models\Concerns\PermissionHasRole;
use FilippoToso\ResourcePermissions\Models\Concerns\PermissionHasUser;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use PermissionHasRole;
    use PermissionHasUser;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'description' => null,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
