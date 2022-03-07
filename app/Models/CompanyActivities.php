<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;

class CompanyActivities extends Model
{
    use HasFactory,WithUuid;

    protected $table = "company_activities";

    protected $fillable = [
        'type',
        'activity',
        'phone_id',
        'buy_number',
        'disocnnect',
        'reconnect',
        'ip_address',
        'company_id',
        'message',
        'group',
        'contacts',
        'group_contacts',
        'wallet',
    ];
}
