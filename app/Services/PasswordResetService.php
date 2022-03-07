<?php

namespace App\Services;

use App\Repositories\PasswordResetRepository;

class PasswordResetService
{
    protected $password_reset_repository;

    public function __construct()
    {
        $this->password_reset_repository = new PasswordResetRepository;
    }

    public function create($data)
    {
        return $this->password_reset_repository->create($data);
    }

    public function findByEmail($email)
    {
        return $this->password_reset_repository->findByEmail($email);
    }

    public function findByToken($token)
    {
        return $this->password_reset_repository->findByToken($token);
    }

    public function delete($email)
    {
        return $this->password_reset_repository->delete($email);
    }
    
    public function getUser()
    {
        return $this->password_reset_repository->getUser();
    }

    public function getUserByAdmin($id)
    {
        return $this->password_reset_repository->getUserByAdmin($id);
    }

    public function find($id)
    {
        return $this->password_reset_repository->find($id);
    }
    public function findByUUID($id)
    {
        return $this->password_reset_repository->findByUUID($id);
    }

    
    

}
