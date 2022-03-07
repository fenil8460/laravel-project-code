<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;

class Contact extends Model
{
    use HasFactory,WithUuid;

    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'company_id',
    ];

    protected $hidden = [
        'id'
     ];
}
