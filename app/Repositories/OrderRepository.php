<?php

namespace App\Repositories;
use App\Models\Order;
class OrderRepository
{

    public function store($data)
    {
        return Order::create($data);
    }

    public function getOrders($user,$company_id)
    {
        return Order::where('user_id',$user->id)->where('company_id',$company_id)->get();
    }

    public function getAllOrders($company_id)
    {
        return Order::where('company_id',$company_id)->get();
    }
}
