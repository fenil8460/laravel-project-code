<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\Product;
use Bavix\Wallet\Traits\HasWallet;

class SmsMessageOut extends Model implements Product
{
    use SoftDeletes,WithUuid, HasWallet;

    protected $dates = ['deleted_at'];
    protected $table="sms_messages_out";
    protected $fillable = [
        'created_by_id',
        'bandwidth_referrence_id',
        'company_id',
        'phone_number_id',
        'to_number',
        'is_group',
        'message',
        'status'
    ];


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
        return 1;
    }

    public function getMetaProduct(): ?array
    {
        return [
            'title' => 'Company ID:'. $this->company_id,
            'description' => 'Pay for Message #' . $this->id,
        ];
    }
}
