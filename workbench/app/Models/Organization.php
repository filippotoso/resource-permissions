<?php

namespace Workbench\App\Models;

use FilippoToso\ResourcePermissions\Models\Concerns\IsResource;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use IsResource;

    protected $table = 'organizations';

    protected $fillable = ['name'];
}
