<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;

class LoginActivities extends Model
{
    use HasFactory,WithUuid;

    protected $table = "login_activities";

    protected $fillable = [
        'user_type',
        'user_id',
        'admin_id',
        'login_time',
        'logout_time',
        'ip_address',
    ];
}
