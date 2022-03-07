<?php

namespace App\Models;

// use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWallets;
use Illuminate\Notifications\Notifiable;
use Rinvex\Subscriptions\Traits\HasSubscriptions;
class Company extends Model implements Wallet,Customer
{
    use SoftDeletes,WithUuid,HasWallet, HasWallets, CanPay;
    use HasSubscriptions,Notifiable;

    protected $table = "companies";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'name',
        'nick_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
       'id'
    ];

    // protected static function booted()
    // {
    //     static::addGlobalScope(new CompanyScope);
    // }

    public function orders()
    {
        return $this->hasMany('App\Models\Order','company_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function phone_numbers()
    {
        return $this->hasMany('App\Models\PhoneNumber','company_id');
    }

    public function contact_list()
    {
        return $this->hasMany('App\Models\Contact','company_id');
    }

    public function group()
    {
        return $this->hasMany('App\Models\Group','company_id');
    }

    public function company_phone_numbers()
    {
        return $this->hasMany('App\Models\PhoneNumber','company_id')->where('status','ACTIVE')->where('running_state',1);
    }

    public function company_clients()
    {
        return $this->hasMany('App\Models\Client','company_id','id');
    }

    public function clients()
    {
        return $this->belongsToMany('App\Models\Client')->withTimestamps();
    }
}
