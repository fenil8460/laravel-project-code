<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;

class GroupService
{
    protected $group_repository;

    public function __construct()
    {
        $this->group_repository = new GroupRepository;
    }

    public function store($data)
    {
        return $this->group_repository->store($data);
    }

    public function getData()
    {
        return $this->group_repository->getData();
    }

    public function getCompanyGroup($id)
    {
        return $this->group_repository->getCompanyGroup($id);
    }

    public function getUserGroup()
    {
        return $this->group_repository->getUserGroup(Auth::user()->id);
    }

    public function find($id,$company_id)
    {
        return $this->group_repository->find($id,$company_id);
    }

    public function findGroupByUuid($id)
    {
        return $this->group_repository->findGroupByUuid($id);
    }


}
