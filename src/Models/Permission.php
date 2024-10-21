<?php

namespace FilippoToso\ResourcePermissions\Models;

use FilippoToso\ResourcePermissions\Models\Concerns\IsPermission;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use IsPermission;

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
