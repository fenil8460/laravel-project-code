<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use Illuminate\Support\Collection;

class AdminService
{
    protected $admin_repository;

    public function __construct()
    {
        $this->admin_repository = new AdminRepository;
    }

    public function store($data)
    {
        return $this->admin_repository->store($data);
    }

    public function find($id)
    {
        return $this->admin_repository->find($id);
    }

    public function getAdminUsers()
    {
        $users =  $this->admin_repository->getAdminUsers();
        $users_and_roles = new Collection();
        foreach ($users as $user)
        {
            $roles = $user->roles;
            $users_and_roles->push(["user" => $user,"role" => $roles]);
        }
        return $users_and_roles;
    }

    public function findAdminUser($id)
    {
        $user = $this->admin_repository->findAdminUser($id);
        return $user;
    }

    public function updateAdminUser($data,$id)
    {
        return $this->admin_repository->updateAdminUser($data,$id);
    }

    public function deleteAdminUser($id)
    {
        return $this->admin_repository->deleteAdminUser($id);
    }

}
