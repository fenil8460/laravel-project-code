<?php

namespace App\Services;

use App\Repositories\GroupContactRepository;
use Illuminate\Support\Facades\Auth;

class GroupContactService
{
    protected $group_contact_repository;

    public function __construct()
    {
        $this->group_contact_repository = new GroupContactRepository;
    }

    public function store($data)
    {
        return $this->group_contact_repository->store($data);
    }

    public function getData()
    {
        return $this->group_contact_repository->getData();
    }

    public function getCompanyGroup($id)
    {
        return $this->group_contact_repository->getCompanyGroup($id);
    }
    
    public function getGroupByCompany($id)
    {
        return $this->group_contact_repository->getGroupByCompany($id);
    }
    
    public function getUserGroup()
    {
        return $this->group_contact_repository->getUserGroup(Auth::user()->id);
    }

    public function groupContact($group_id)
    {
        return $this->group_contact_repository->groupContact($group_id);
    }

}
