<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;


class GroupContact extends Model
{
    use HasFactory,WithUuid;

    protected $table = "group_contacts";

    protected $fillable = [
        'group_id',
        'contact_id',
    ];

    protected $hidden = [
        'id'
     ];
}
