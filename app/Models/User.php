<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use App\Traits\WithUuid;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWallets;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable implements Wallet,Customer
{
    use HasApiTokens, HasFactory, Notifiable, WithUuid, HasWallet, HasWallets, CanPay, LaratrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_token',
        'provider_refresh_token',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
        'remember_token',
        'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany('App\Models\Order','user_id');
    }
    public function company()
    {
        return $this->hasMany('App\Models\Company');
    }

    public function contact_list()
    {
        return $this->hasMany('App\Models\Contact','user_id');
    }

    public function group()
    {
        return $this->hasMany('App\Models\Group','user_id');
    }

}
