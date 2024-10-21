<?php

namespace FilippoToso\ResourcePermissions\Models;

use FilippoToso\ResourcePermissions\Models\Concerns\IsRole;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use IsRole;

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
