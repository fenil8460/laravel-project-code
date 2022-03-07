<?php

namespace App\Services;

use App\Library\Bandwidth\OrderPhoneClass;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public $order_phone;
    protected $order_repository;

    public function __construct()
    {
        $this->order_phone = new OrderPhoneClass;
        $this->order_repository = new OrderRepository;
    }
    public function searchAvailableNumbers($input)
    {
        try
        {
            $data['numbers'] =  $this->order_phone->getAvailableNumbers($input);
            return $data;
        }
        catch(\Exception $e)
        {
            $data['error'] = $e->getMessage();
            return $data;
        }
    }

    public function getOrders($user,$company_id)
    {
        return $this->order_repository->getOrders($user,$company_id);
    }

    public function getAllOrders($company_id)
    {
        return $this->order_repository->getAllOrders($company_id);
    }

    public function createOrder($telephone_numbers = [],$name, $siteid,$customerOrderId)
    {
        try {
            $response['order'] = $this->order_phone->buyNumber($telephone_numbers,$name,$siteid,$customerOrderId);
            return $response;
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return $response;
        }

    }

    public function getInserviceNumbers()
    {
        return $this->order_phone->getNumbers();
    }

    public function disconnectNumber($input,$name,$customer_order_id)
    {
        return $this->order_phone->disconnectNumber($input,$name,$customer_order_id);
    }

    public function getDisconnectedNumbers($disconnect_id)
    {
        return $this->order_phone->getDisconnectedNumbers($disconnect_id);
    }

    public function getAllDisconnectedNumbers()
    {
        return $this->order_phone->getAllDisconnectedNumbers();
    }

    public function store($data)
    {
        return $this->order_repository->store($data);
    }
}
