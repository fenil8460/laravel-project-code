<?php

namespace App\Services;

use App\Repositories\CompanyActivitiesRepository;
use Illuminate\Support\Facades\Auth;

class CompanyActivitiesService
{
    protected $company_activities_repository;

    public function __construct()
    {
        $this->company_activities_repository = new CompanyActivitiesRepository;
    }

    public function storeCompanyActivities($data)
    {
        return $this->company_activities_repository->store($data);
    }

    public function getCompanyActivities()
    {
        return $this->company_activities_repository->getCompanyActivities();
    }

    public function findCompanyActivtiesByUserId($id,$user_id)
    {
        return $this->company_activities_repository->findCompanyActivtiesByUserId($id,$user_id);
    }

    public function findCompanyActivtiesByAdminId($id,$admin_id)
    {
        return $this->company_activities_repository->findCompanyActivtiesByAdminId($id,$user_id);
    }


}
