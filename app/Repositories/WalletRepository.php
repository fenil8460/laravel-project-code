<?php

namespace App\Repositories;

use Bavix\Wallet\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletRepository
{
    public function getUserWallets()
    {
        $wallets = Wallet::where('holder_id',Auth::user()->id)->where('holder_type',"App\Models\User")->get();
        return $wallets;
    }



    public function getWalletById($id)
    {
        $wallet = Wallet::where('id',$id)->where('holder_id',Auth::user()->id)->first();
        return $wallet;
    }


}
