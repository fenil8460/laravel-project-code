<?php

namespace App\Services;

use App\Repositories\RegisterRepository;
use Illuminate\Support\Facades\Auth;

class RegisterService
{
    protected $register_repository;

    public function __construct()
    {
        $this->register_repository = new RegisterRepository;
    }

    public function create($data)
    {
        return $this->register_repository->create($data);
    }

    public function getUser()
    {
        return $this->register_repository->getUser();
    }

    public function getUserByAdmin($id)
    {
        return $this->register_repository->getUserByAdmin($id);
    }

    public function find($id)
    {
        return $this->register_repository->find($id);
    }

    public function update($data)
    {
        return $this->register_repository->updateUser($data,Auth::user()->id);
    }

    public function findByUUID($id)
    {
        return $this->register_repository->findByUUID($id);
    }

    public function findByEmail($email)
    {
        return $this->register_repository->findByEmail($email);
    }

    public function updateUser($data,$id)
    {
        return $this->register_repository->updateUser($data,$id);
    }


}
