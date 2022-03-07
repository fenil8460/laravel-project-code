<?php

namespace App\Services;

use App\Repositories\OrderItemRepository;

class OrderItemService
{
    protected $order_item_repository;
    public function __construct()
    {
        $this->order_item_repository = new OrderItemRepository;
    }

    public function store($data)
    {
        return $this->order_item_repository->store($data);
    }
}
