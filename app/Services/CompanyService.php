<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Auth;

class CompanyService
{
    protected $company_repository;

    public function __construct()
    {
        $this->company_repository = new CompanyRepository;
    }

    public function getData()
    {
        return $this->company_repository->getData(Auth::user()->id);
    }

    public function getAllCompanies()
    {
        return $this->company_repository->getAllCompanies();
    }

    public function findCompanies($company_id,$user_id)
    {
        return $this->company_repository->findCompanies($company_id,$user_id);
    }

    public function getCompanyByUser($id)
    {
        return $this->company_repository->getCompanyByUser($id);
    }

    public function getCompanyByAdmin($id)
    {
        return $this->company_repository->getCompanyByAdmin($id);
    }

    public function getCompanyIdByUser($id)
    {
        return $this->company_repository->getCompanyIdByUser($id);
    }

    public function getCompanyUser($id)
    {
        return $this->company_repository->getCompanyUser($id);
    }

    public function getCompanyPhone($id)
    {
        return $this->company_repository->getCompanyPhone($id);
    }

    public function getDataById($id)
    {
        return $this->company_repository->getDataById($id);
    }

    public function store($data)
    {
        return $this->company_repository->store($data);
    }

    public function show($id)
    {
        return $this->company_repository->show($id,Auth::user()->id);
    }

    public function find($id)
    {
        return $this->company_repository->find($id,Auth::user()->id);
    }

    public function findByUser($id,$user_id)
    {
        return $this->company_repository->find($id,$user_id);
    }

    public function findByAdmin($id)
    {
        return $this->company_repository->findByAdmin($id);
    }

    public function findByUuid($company_id,$user_id)
    {
        return $this->company_repository->findByUuid($company_id,$user_id);
    }

    public function adminFindByUuid($company_id)
    {
        return $this->company_repository->adminFindByUuid($company_id);
    }

    public function getCompanyByUuid($company_id)
    {
        return $this->company_repository->getCompanyByUuid($company_id);
    }

    public function update($data, $id)
    {
        return $this->company_repository->update($data, $id,Auth::user()->id);
    }

    public function destroy($id)
    {
        return $this->company_repository->destroy($id,Auth::user()->id);
    }

    public function destroyById($company_id,$user_id)
    {
        return $this->company_repository->destroy($company_id,$user_id);
    }

}
