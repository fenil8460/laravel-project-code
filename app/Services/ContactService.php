<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    protected $contact_repository;

    public function __construct()
    {
        $this->contact_repository = new ContactRepository;
    }

    public function store($data)
    {
        return $this->contact_repository->store($data);
    }

    public function getData()
    {
        return $this->contact_repository->getData();
    }

    public function getContactByCompany($id)
    {
        return $this->contact_repository->getContactByCompany($id);
    }

    public function findContactByCompany($id,$company_id)
    {
        return $this->contact_repository->findContactByCompany($id,$company_id);
    }
    

    public function getCompanyContact($id)
    {
        return $this->contact_repository->getCompanyContact($id);
    }

    public function getUserContact()
    {
        return $this->contact_repository->getUserContact(Auth::user()->id);
    }

    public function findContactByUuid($id)
    {
        return $this->contact_repository->findContactByUuid($id);
    }

}
