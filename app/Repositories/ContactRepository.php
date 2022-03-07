<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use App\Traits\FindAPI;

class ContactRepository
{
    use FindAPI;
    
    public function store($data)
    {
        return Contact::create($data);
    }

    public function getData()
    {
        return  Contact::all();
    }

    public function findContactByUuid($id)
    {
        return  Contact::where('uu_id',$id)->first();
    }

    public function getContactByCompany($id)
    {
        return Contact::where('company_id',$id)->get();
    }

    public function findContactByCompany($id,$company_id)
    {
        return Contact::where('id',$id)->where('company_id',$company_id)->first();
    }

    public function find($id)
    {
        $contact = Contact::where('user_id',$id)->first();
        return $contact;
    }
    
    public function getCompanyContact($company_id)
    {
        $company = Company::find($company_id);
        if($company != null){
            $contact = Company::find($company_id)->contact_list;
            return $contact;
        }else{
            return null;
        }
    }

    public function getUserContact($user_id)
    {
            $contact = User::find($user_id)->contact_list;
            return $contact;
       
    }

}
