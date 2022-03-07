<?php

namespace App\Repositories;

use App\Models\CompanyActivities;
use App\Traits\FindAPI;

class CompanyActivitiesRepository
{
    use FindAPI;
    
    public function store($data)
    {
        return CompanyActivities::create($data);
    }

    public function getCompanyActivities()
    {
        return  CompanyActivities::all();
    }

    public function findCompanyActivtiesByUserId($id,$user_id)
    {
        return CompanyActivities::where('uu_id',$id)->where('user_id',$user_id);
        return $this->findResource($company);
    }

    public function findCompanyActivtiesByAdminId($id,$admin_id)
    {
        return CompanyActivities::where('uu_id',$id)->where('admin_id',$admin_id);
        return $this->findResource($company);
    }

}
