<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;

class OrderItem extends Model
{
    use HasFactory,SoftDeletes,WithUuid;

    protected $table="order_items";
    protected $fillable = [
        'uu_id',
        'order_id',
        'phone_number',
        'order_status',
        'city',
        'lata',
        'rate_center',
        'state',
        'tier',
        'vendor_id',
        'vendor_name',
    ];
}
