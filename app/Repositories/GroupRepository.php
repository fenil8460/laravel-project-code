<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Group;
use App\Models\User;
use App\Traits\FindAPI;

class GroupRepository
{
    use FindAPI;
    
    public function store($data)
    {
        return Group::create($data);
    }

    public function getData()
    {
        return  Group::all();
    }

    public function findGroupByUuid($id)
    {
        return  Group::where('uu_id',$id)->first();
    }

    public function find($id,$company_id)
    {
        return Group::where('id',$id)->where('company_id',$company_id)->get();
    }
    
    public function getCompanyGroup($company_id)
    {
        $company = Company::find($company_id);
        if($company != null){
            $group = Company::find($company_id)->group;
            return $group;
        }else{
            return null;
        }
    }

    public function getUserGroup($user_id)
    {
            $group = User::find($user_id)->group;
            return $group;
       
    }

}
