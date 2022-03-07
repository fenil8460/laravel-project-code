<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\GroupContact;
use App\Models\Group;
use App\Models\User;
use App\Traits\FindAPI;
use Illuminate\Support\Facades\Auth;


class GroupContactRepository
{
    use FindAPI;
    
    public function store($data)
    {
        return GroupContact::create($data);
    }

    public function getData()
    {
        return GroupContact::all();
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

    public function getGroupByCompany($id)
    {
        $company = Company::find($id);
        if($company != null){
            $group = Group::where('company_id',$id)->get();
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

    public function groupContact($group_id)
    {
            $group_contact = Group::find($group_id)->group_contact;
            $contacts = Contact::whereIn('id', $group_contact)->get();
            $data = [];
            foreach($contacts as $index=>$contact)
            {
                $company = Company::find($contact->company_id);
                $data[$index]=[
                    "uu_id" => $contact->uu_id,
                    "name" => $contact->name,
                    "phone_number" => $contact->phone_number,
                    "user_id" => Auth::user()->uu_id,
                    "company_id" => isset($company->uu_id) ? $company->uu_id : null,
                    "created_at" => $contact->created_at,
                    "updated_at" => $contact->updated_at,
                ];
            }
            return $data;
       
    }

}
