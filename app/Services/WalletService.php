<?php

namespace App\Services;

use App\Repositories\WalletRepository;

class WalletService
{
    protected $wallet_repository;

    public function __construct()
    {
        $this->wallet_repository = new WalletRepository;
    }



    public function getWalletById($id)
    {
        return $this->wallet_repository->getWalletById($id);
    }


}
