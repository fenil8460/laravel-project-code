<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\Product;
use Bavix\Wallet\Traits\HasWallet;


class PhoneNumber extends Model implements Product
{
    use SoftDeletes,WithUuid, HasWallet;

    protected $dates = ['deleted_at'];
    protected $table="phone_numbers";
    protected $fillable = [
        'uu_id',
        'created_by_id',
        'company_id',
        'phone_number',
        'nick_name',
        'status',
        'running_state'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function canBuy(Customer $customer, int $quantity = 1, bool $force = false): bool
    {
        /**
         * If the service can be purchased once, then
         *  return !$customer->paid($this);
         */
        return true;
    }

    public function getAmountProduct(Customer $customer)
    {
        return 2;
    }

    public function getMetaProduct(): ?array
    {
        return [
            'title' => 'Company ID:'. $this->company_id,
            'description' => 'Purchase of Product #' . $this->id,
        ];
    }
}
