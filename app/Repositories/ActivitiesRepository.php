<?php

namespace App\Repositories;

use App\Models\AdminLoginActivities;
use App\Models\LoginActivities;
use App\Models\CompanyActivities;
use App\Traits\FindAPI;

class ActivitiesRepository
{
    use FindAPI;

    public function getLoginActivities()
    {
        return  LoginActivities::all();
    }

    public function getLoginActivitiesByUser($id)
    {
        return  LoginActivities::where('user_id',$id)->get();
    }

    public function getAdminLoginActivities()
    {
        return  AdminLoginActivities::all();
    }

    public function getCompanyActivities(){
        return  CompanyActivities::all();
    }

    public function getCompanyActivitiesByUser($id){
        return  CompanyActivities::where('company_id',$id)->get();
    }
}
