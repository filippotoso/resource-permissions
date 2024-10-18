<?php

namespace FilippoToso\ResourcePermissions\Models;

use FilippoToso\ResourcePermissions\Models\Concerns\RoleHasPermission;
use FilippoToso\ResourcePermissions\Models\Concerns\RoleHasUser;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use RoleHasUser;
    use RoleHasPermission;

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
