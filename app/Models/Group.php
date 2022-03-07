<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;


class Group extends Model
{
    use HasFactory,WithUuid;

    protected $fillable = [
        'group_name',
        'user_id',
        'company_id',
    ];

    protected $hidden = [
        'id'
     ];

    public function group_contact()
    {
        return $this->hasMany('App\Models\GroupContact','group_id')->select('contact_id');
    }
}
