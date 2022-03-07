<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
{
    public $guarded = [];
}
