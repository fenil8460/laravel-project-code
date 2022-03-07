<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Services\ContactService;
use App\Services\RegisterService;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseAPI;
use App\Events\CompanyActivity;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ContactsImport;
use Exception;

class ContactController extends Controller
{
    use ResponseAPI;
    protected $contact_service;

    public $success_status = 200;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            return $next($request);
        });
        $this->contact_service = new ContactService;
        $this->company_service = new CompanyService;
        $this->register_service = new RegisterService;
    }
    public function getContactLists()
    {

            $datas =  $this->contact_service->getData();
            $data = [];
            if(count($datas) > 0) {
                foreach($datas as $index=>$item){
                    $user = $this->register_service->getUserByAdmin($item->user_id);
                    $company = $this->company_service->findCompanies($item->company_id,$item->user_id);
                    $data[$index]=[
                        "uu_id"=> $item->uu_id,
                        "name"=> $item->name,
                        "phone_number"=> $item->phone_number,
                        "user_id"=> isset($user->uu_id) ? $user->uu_id : null,
                        "user_name"=>isset($user->name) ? $user->name : null,
                        "company_id"=> isset($company->uu_id) ? $company->uu_id : null,
                        "company_name"=> isset($company->name) ? $company->name : null,
                        "created_at"=> $item->created_at,
                        "updated_at"=> $item->updated_at
                    ];
                }
                return $this->success($data);
            }
            else{
                return $this->error("Contact Not Found",'404');
            }

    }

    public function getContactByCompany($id)
    {
            $company = $this->company_service->adminFindByUuid($id);
            $contacts =  $this->contact_service->getContactByCompany($company->id);
            $data = [];
            if(count($contacts) > 0) {
                foreach($contacts as $index=>$contact){
                    $user = $this->register_service->getUserByAdmin($contact->user_id);
                    $company = $this->company_service->findByAdmin($contact->company_id);
                    $data[$index]=[
                        "uu_id" => $contact->uu_id,
                        "name" => $contact->name,
                        "phone_number" => $contact->phone_number,
                        "user_id" => isset($user->uu_id) ? $user->uu_id : null,
                        "company_id" => isset($company->uu_id) ? $company->uu_id : null,
                        "created_at" => $contact->created_at,
                        "updated_at" => $contact->updated_at,
                    ];
                }
                return $this->success($data);
            }
            else{
                return $this->error("Contact Not Found",'404');
            }

    }

    public function createContact(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found",'404');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number'=>'required',
            'companyId'=>'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = [];
        $data = [
            'name' => $request->name,
            'phone_number'=>$request->phone_number,
            'company_id'=>$company->id,
            'user_id' => $this->user->id,
         ];
         try
         {
            $success = $this->contact_service->store($data);
            $company_activities = $success;
            $company_activities['type'] = 'contacts';
            $company_activities['company_id'] = $company->uu_id;
            $company_activities['user_id'] = Auth::user()->uu_id;
            event(new CompanyActivity($company_activities));
            return $this->success($success);
         }
         catch(Exception $e)
         {
            return $this->error($e->getMessage(),'404');
         }
    }

    public function importContact(Request $request)
    {

        
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found",'404');
        }
        $validator = Validator::make($request->all(), [
            'companyId'=>'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $data = [
            'company_id'=>$company->id,
            'user_id'=>$this->user->id
        ];
         try
         {
            Excel::import(new ContactsImport($data),request()->file('file'));
            return $this->success('Contacts import successfully');
         }
         catch(Exception $e)
         {
            return $this->error($e->getMessage(),'404');
         }
    }

    public function getCompanyContact(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found",'404');
        }
        $contacts = $this->contact_service->getCompanyContact($company->id);
        if($contacts != null  && count($contacts) > 0) {
            foreach($contacts as $index=>$contact){
                $data[$index] = [
                    "uu_id" => $contact->uu_id,
                    "name" => $contact->name,
                    "phone_number" => $contact->phone_number,
                    "user_id" => Auth::user()->uu_id,
                    "company_id" => $company->uu_id,
                    "created_at" => $contact->created_at,
                    "updated_at" => $contact->updated_at,
                ];
            }
            return $this->success($data);
        }
        else{
            return $this->error("Contact Not Found",'404');
        }
    }

    public function getUserContact()
    {
        $contacts = $this->contact_service->getUserContact();
        $data = [];
        if(count($contacts) > 0) {
            foreach($contacts as $index=>$contact){
                $company = $this->company_service->find($contact->company_id);
                $data[$index] = [
                    "uu_id" => $contact->uu_id,
                    "name" => $contact->name,
                    "phone_number" => $contact->phone_number,
                    "user_id" => Auth::user()->uu_id,
                    "company_id" => isset($company->uu_id) ? $company->uu_id : null,
                    "created_at" => $contact->created_at,
                    "updated_at" => $contact->updated_at,
                ];
            }
            return $this->success($data);
        }
        else {
            return $this->error("Contact Not Found",'404');
        }
    }
}
