<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\WithUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use WithUuid;
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

      /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'nick_name',
        'company_id',
        'user_id',
        'status',
    ];

    protected $hidden = [
        'id'
     ];

    public function clientStatus()
    {
        return $this->belongsTo('App\Models\ClientStatus','status','id');
    }

    public function clientCompany()
    {
        return $this->belongsTo('App\Models\Company','company_id','id');
    }
    public function companies()
    {
        return $this->belongsToMany('App\Models\Company')->withTimestamps();
    }


}
