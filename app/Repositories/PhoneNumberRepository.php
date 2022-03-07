<?php

namespace App\Repositories;

use App\Models\PhoneNumber;
use Illuminate\Support\Collection;

class PhoneNumberRepository
{
    public function getData()
    {
        return PhoneNumber::all();
    }
    public function store($data)
    {
        return PhoneNumber::create($data);
    }

    public function show($id)
    {
        return PhoneNumber::find($id);
    }

    public function update($data,$id)
    {
        $company = PhoneNumber::find($id);
        return $company->update($data);
    }
    public function destroy($id)
    {
        $company = PhoneNumber::find($id);
        $company->delete();
    }

    public function getInserviceNumbers($company_id)
    {
        if(is_array($company_id) || $company_id instanceof Collection)
        {
            return PhoneNumber::whereIn('company_id',$company_id)->where('status','ACTIVE')->where('running_state','1');
        }
        else
        {
            return PhoneNumber::where('company_id',$company_id)->where('status','ACTIVE')->where('running_state','1');
        }
    }

    public function disconnectNumber($disconnectNumber,$company_id)
    {
        return PhoneNumber::where('phone_number',$disconnectNumber)->where('company_id',$company_id)->latest()->first();
    }

    public function getAllDisconnectedNumbers($phone_numbers,$company_id)
    {
        if(getType($phone_numbers) == "array")
        {
            return PhoneNumber::where('company_id',$company_id)->where('status','DISCONNECTED')->where('running_state','1')->whereIn('phone_number',$phone_numbers)->get();
        }
        else if(getType($phone_numbers) == "string")
        {
            return PhoneNumber::where('company_id',$company_id)->where('status','DISCONNECTED')->where('running_state','1')->where('phone_number',$phone_numbers)->get();
        }
    }

    public function findDisconnectedNumber($phone_numbers)
    {
            return PhoneNumber::where('status','DISCONNECTED')->where('running_state','1')->where('phone_number',$phone_numbers)->first();
    }

    public function findByCompany($id,$company_id){
        return PhoneNumber::where('id',$id)->where('company_id',$company_id)->get();
    }

    public function find($id)
    {
        return  PhoneNumber::find($id);
    }

    public function findByuuid($id)
    {
        return  PhoneNumber::where('uu_id',$id)->first();
    }

    public function findNumber($number)
    {
        return  PhoneNumber::where('phone_number',$number)->latest()->first();
    }

    public function findDisconnected($number,$company_id)
    {
        return  PhoneNumber::where('company_id',$company_id)->where('phone_number',$number)->latest()->first();
    }
}
