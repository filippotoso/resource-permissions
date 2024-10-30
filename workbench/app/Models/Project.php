<?php

namespace Workbench\App\Models;

use FilippoToso\ResourcePermissions\Models\Concerns\IsResource;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use IsResource;

    protected $table = 'projects';

    protected $fillable = ['name'];
}
