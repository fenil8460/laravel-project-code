<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Services\GroupService;
use App\Services\RegisterService;
use App\Services\GroupContactService;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseAPI;
use App\Events\CompanyActivity;
use App\Services\ContactService;
use Exception;

class GroupContactController extends Controller
{
    use ResponseAPI;

    public $success_status = 200;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            return $next($request);
        });
        $this->group_contact_service = new GroupContactService;
        $this->group_service = new GroupService;
        $this->company_service = new CompanyService;
        $this->register_service = new RegisterService;
        $this->contact_service = new ContactService;
    }
    public function getGroupContacts()
    {

            $groups =  $this->group_service->getData();
            if(count($groups) > 0) {
                $data = [];
                foreach($groups as $index=>$group){
                    $group_contact = $this->group_contact_service->groupContact($group->id);
                    $user = $this->register_service->getUserByAdmin($group->user_id);
                    $company = $this->company_service->findByAdmin($group->company_id);
                    $group->user_id = isset($user->uu_id) ? $user->uu_id : null;
                    $group->company_id = isset($company->uu_id) ? $company->uu_id : null;
                    $data[$index] = [
                        'group'=>$group,
                        'contact'=>$group_contact
                    ];
                }
                return $this->success($data);
            }
            else{
                return $this->error("Group Not Found",'404');
            }

    }

    public function addContactsToGroup(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found",'404');
        }

        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'contact_id'=>'required',
            'companyId' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $contact_id =  $this->contact_service->findContactByUuid($request->contact_id);
        $group_id = $this->group_service->findGroupByUuid($request->group_id);
        $data = [];
        $data = [
            'group_id' => $group_id->id,
            'contact_id'=> $contact_id->id,
         ];
         try
         {
            $success = $this->group_contact_service->store($data);
            $company_activities = $success;
            $company_activities['type'] = 'groups contacts';
            $company_activities['company_id'] = $company->id;
            event(new CompanyActivity($company_activities));
            $success['group_id'] = $group_id->uu_id;
            $success['contact_id'] = $contact_id->uu_id;
            $success['company_id'] = $company->uu_id;
            return $this->success($success);
         }
         catch(Exception $e)
         {
            return $this->error($e->getMessage(),'404');
         }
    }

    public function getCompanyGroupContact(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found",'404');
        }
        $groups = $this->group_contact_service->getCompanyGroup($company->id);
        if($groups != null && count($groups) > 0) {
            $data = [];
            foreach($groups as $index=>$group){
                $group_contact = $this->group_contact_service->groupContact($group->id);
                $data[$index] = [
                    "uu_id"=> $group->uu_id,
                    "group_name"=> $group->group_name,
                    "company_id"=> $company->uu_id,
                    "user_id"=> Auth::user()->uu_id,
                    "created_at"=> $group->created_at,
                    "updated_at"=> $group->updated_at,
                    'contact'=>$group_contact,
                ];
            }
            return $this->success($data);
        }
        else{
            return $this->error("Group Not Found",'404');
        }
    }

    public function getUserGroupContact()
    {
        $groups = $this->group_contact_service->getUserGroup();
        if(count($groups) > 0) {
            $data = [];
            foreach($groups as $index=>$group){
                $company = $this->company_service->find($group->company_id);
                $group_contact = $this->group_contact_service->groupContact($group->id);
                $data[$index] = [
                    "uu_id" => $group->uu_id,
                    "group_name" => $group->group_name,
                    "user_id" => Auth::user()->uu_id,
                    "company_id" =>  isset($company->uu_id) ? $company->uu_id : null,
                    "created_at" => $group->created_at,
                    "updated_at" => $group->updated_at,
                    "contact"=> $group_contact
                ];
                // $data[$index] = [
                //     'group'=>$group,
                //     'contact'=>$group_contact
                // ];
            }
            return $this->success($data);
        }
        else {
            return $this->error("Group Not Found",'404');
        }
    }

    public function getGroupContactsCompany()
    {

            $companies = $this->company_service->getAllCompanies();
            $data = [];
            $index = 0;
            if(count($companies) > 0 && $companies != null){
                foreach($companies as $company){
                    $groups = $this->group_contact_service->getCompanyGroup($company->id);
                    if($groups != null && count($groups) > 0) {
                        foreach($groups as $group){
                            $group_contact = $this->group_contact_service->groupContact($group->id);
                            $user = $this->register_service->getUserByAdmin($group->user_id);
                            $data[$index] = [
                                "uu_id"=>  $group->uu_id,
                                "group_name"=>  $group->group_name,
                                "user_id"=>  isset($user->uu_id) ? $user->uu_id : null,
                                "user_name"=> isset($user->name) ? $user->name : null,
                                "company_id"=>  isset($company->uu_id) ? $company->uu_id : null,
                                "company_name"=>  isset($company->name) ? $company->name : null,
                                "created_at"=>  $group->created_at,
                                "updated_at"=> $group->updated_at,
                                'contact'=>$group_contact
                            ];
                            $index++;
                        }
                    }
                }
                return $this->success($data);
            }


    }

    public function getGroupContactsByCompany($id)
    {
        $data = [];
        $index = 0;
        $company = $this->company_service->adminFindByUuid($id);
        if(!isset($company)){
            return $this->error("Company Not Found", 404);
        }
        $groups = $this->group_contact_service->getGroupByCompany($company->id);
            if($groups != null && count($groups) > 0) {
                foreach($groups as $group){
                    $group_contact = $this->group_contact_service->groupContact($group->id);
                    $user = $this->register_service->getUserByAdmin($group->user_id);
                    $data[$index] = [
                        "uu_id"=> $group->uu_id,
                        "group_name"=>  $group->group_name,
                        "user_id"=>  isset($user->uu_id) ? $user->uu_id : null,
                        "created_at"=>  $group->created_at,
                        "updated_at"=> $group->updated_at,
                        'contact'=>$group_contact
                        ];
                        $index++;
                    }
                }
                if(count($data) == 0){
                    return $this->error("Group Contact Not Found", 404);
                }else{
                    return $this->success($data);
                }

    }
}
