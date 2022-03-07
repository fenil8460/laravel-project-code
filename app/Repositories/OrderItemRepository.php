<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository
{
    public function store($data)
    {
        return OrderItem::create($data);
    }
}
