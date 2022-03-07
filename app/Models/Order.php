<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;


class Order extends Model
{
    use HasFactory,WithUuid,SoftDeletes;

    protected $fillable = [
        'uu_id',
        'order_id',
        'bandwidth_order_id',
        'company_id',
        'user_id',
        'order_name',
        'order_status',
        'order_type',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

}
