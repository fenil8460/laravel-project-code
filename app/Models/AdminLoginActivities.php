<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;

class AdminLoginActivities extends Model
{
    use HasFactory,WithUuid;

    protected $table = "admin_login_activities";

    protected $fillable = [
        'user_type',
        'admin_id',
        'user_id',
        'login_time',
        'logout_time',
        'ip_address',
    ];
}
