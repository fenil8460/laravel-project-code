<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;
class SmsMessageIn extends Model
{
    use SoftDeletes,WithUuid;

    protected $dates = ['deleted_at'];
    protected $table = "sms_messages_in";
    protected $fillable = [
        'created_by_id',
        'company_id',
        'phone_number_id',
        'from_number',
        'message',
        'status',
        'received_time',
        'type',
        'direction'
    ];
}
