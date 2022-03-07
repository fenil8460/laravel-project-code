<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use App\Scopes\CompanyScope;
use App\Traits\FindAPI;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    use FindAPI;
    public function getData($user_id)
    {
        $company = Company::where('user_id',$user_id)->get();
        $clients = Client::where('user_id',$user_id)->get();
        $client_companies = new Collection();
        if(isset($clients))
        {
            foreach($clients as $client)
            {
                $companies = $client->companies;
                foreach($companies as $client_company){
                if(isset($companies) && count($companies)>0)
                {
                    $client_companies->push($client_company);
                }
            }
            }
        }
        return ['myCompany' => $company,'invitedCompanies' => $client_companies];
    }



    public function getAllCompanies()
    {
        $company = Company::all();
        return $company;
    }

    public function findByAdmin($id){
        $company = Company::find($id);
        return $company;
    }

    public function findCompanies($company_id,$user_id)
    {
        $company = Company::where('id',$company_id)->where('user_id',$user_id)->first();
        return $company;
    }

    public function getCompanyByUser($id){
        $company = Company::where('user_id', $id)->get();
        return $company;
    }

    public function getCompanyByAdmin($id){
        $user = User::where('uu_id',$id)->first();
        $company = Company::where('user_id', $user->id)->get();
        return $company;
    }

    public function getCompanyIdByUser($id){
        $company = Company::select('id')->where('user_id',$id)->get();
        return $company;
    }

    public function getCompanyUser($id)
    {
        $company_user = User::find($id);
        return $company_user;
    }

    public function getCompanyPhone($company_id)
    {
        $phone_numbers = Company::find($company_id)->company_phone_numbers;
        $data = [];
        foreach($phone_numbers as $index=>$phone_number){
            $user = User::find($phone_number->created_by_id);
            $company = Company::find($phone_number->company_id);
            $data[$index] = [
                "uu_id" => $phone_number->uu_id,
                "created_by_id" => isset($user->uu_id) ? $user->uu_id : null,
                "company_id" => isset($company->uu_id) ? $company->uu_id : null,
                "phone_number" => $phone_number->phone_number,
                "nick_name" => $phone_number->nick_name,
                "status" => $phone_number->status,
                "running_state" => $phone_number->running_state,
                "deleted_at" => $phone_number->deleted_at,
                "created_at" => $phone_number->created_at,
                "updated_at" => $phone_number->updated_at,
            ];
        }
        return $data;
    }

    public function getDataById($user_id)
    {
        $companies = User::find($user_id)->company;
        $data = [];
        foreach($companies as $index=>$company){
            $user = User::find($company->user_id);
            $data[$index] = [
                "uu_id" => $company->uu_id,
                "user_id" => isset($user->uu_id) ? $user->uu_id : null,
                "name" => $company->name,
                "deleted_at" => $company->deleted_at,
                "created_at" => $company->created_at,
                "updated_at" => $company->updated_at,
                "nick_name" => $company->nick_name,
            ];
        }
        return $data;
    }

    public function store($data)
    {
        return Company::create($data);
    }

    public function show($id,$user_id)
    {
        $company = Company::where('uu_id',$id)->where('user_id',$user_id);
        return $this->findResource($company);
    }

    public function find($id,$user_id)
    {
        $company = Company::where('id',$id)->where('user_id',$user_id)->first();
        return $company;
    }

    public function findByUuid($id,$user_id)
    {
        $company = Company::where('uu_id',$id)->where('user_id',$user_id)->first();
        return $company;
    }

    public function adminFindByUuid($id)
    {
        $company = Company::where('uu_id',$id)->first();
        return $company;
    }

    public function getCompanyByUuid($id)
    {
        $company = Company::where('uu_id',$id)->first();
        return $company;
    }

    public function update($data, $id, $user_id)
    {
        $company = Company::where('uu_id',$id)->where('user_id',$user_id);
        return $this->updateResource($company,$data);
    }

    public function destroy($id,$user_id)
    {
        $company = Company::where('uu_id',$id)->where('user_id',$user_id);
        return $this->destroyResource($company);
    }
}
