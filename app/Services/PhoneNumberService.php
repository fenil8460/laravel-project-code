<?php

namespace App\Services;

use App\Repositories\PhoneNumberRepository;
use Illuminate\Support\Facades\Auth;
class PhoneNumberService
{
    protected $phone_number_repository;
    public function __construct()
    {
        $this->phone_number_repository = new PhoneNumberRepository;
    }
    public function getData()
    {
        return $this->phone_number_repository->getData();
    }
    public function store($data)
    {
        return $this->phone_number_repository->store($data);
    }
    public function show($id)
    {
        return $this->phone_number_repository->show($id);
    }

    public function findByCompany($id,$company_id)
    {
        return $this->phone_number_repository->findByCompany($id,$company_id);
    }

    public function update($data,$id)
    {
        return $this->phone_number_repository->update($data,$id);
    }
    public function destroy($id){
        return $this->phone_number_repository->destroy($id);

    }

    public function getInserviceNumbers($company_id)
    {
        return $this->phone_number_repository->getInserviceNumbers($company_id);
    }

    public function disconnectNumber($number,$company_id)
    {
        return $this->phone_number_repository->disconnectNumber($number,$company_id);
    }
    public function getDisconnectedNumbers($phone_numbers,$company_id)
    {
        return $this->phone_number_repository->getAllDisconnectedNumbers($phone_numbers,$company_id);
    }

    public function findDisconnectedNumber($phone_numbers)
    {
        return $this->phone_number_repository->findDisconnectedNumber($phone_numbers);
    }

    public function find($id)
    {
        $phone = $this->phone_number_repository->find($id);
        return $phone;

    }

    public function findByuuid($id)
    {
        $phone = $this->phone_number_repository->findByuuid($id);
        return $phone;

    }

    
    public function findNumber($number)
    {
        $phone = $this->phone_number_repository->findNumber($number);
        return $phone;

    }
    public function findDisconnected($number,$company_id)
    {
        $phone = $this->phone_number_repository->findDisconnected($number,$company_id);
        return $phone;
    }
}
