<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Services\PhoneNumberService;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseAPI;
use App\Services\ActivitiesService;
use App\Services\RegisterService;
use App\Services\AdminService;
use App\Services\ContactService;
use App\Services\GroupService;
use App\Services\SmsMessageOutService;
use Exception;

class ActivitiesController extends Controller
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
        $this->activities_service = new ActivitiesService;
        $this->register_service = new RegisterService;
        $this->admin_service = new AdminService;
        $this->company_service = new CompanyService;
        $this->phone_number_service = new PhoneNumberService;
        $this->contact_service = new ContactService;
        $this->group_service = new GroupService;
        $this->sms_service = new SmsMessageOutService;
    }

    public function getLoginActivities(){

            $login_activities = $this->activities_service->getLoginActivities();
            $data=[];
            if(count($login_activities) > 0){
                foreach($login_activities as $index=>$login_activity){
                    if($login_activity->user_id){
                        $user = $this->register_service->getUserByAdmin($login_activity->user_id);
                        $data[$index]=[
                            "uu_id"=> $login_activity->uu_id,
                            "user_type"=> $login_activity->user_type,
                            "user_id"=> isset($user->uu_id) ? $user->uu_id : null,
                            "user_name"=> isset($user->name) ? $user->name : null,
                            "admin_id"=> $login_activity->admin_id,
                            "login_time"=> $login_activity->login_time,
                            "logout_time"=> $login_activity->logout_time,
                            "ip_address"=> $login_activity->ip_address,
                            "created_at"=> $login_activity->created_at,
                            "updated_at"=> $login_activity->updated_at,
                        ];
                    }
                }
                return $this->success($data);
            }else{
                return $this->error("Login Activities Not Found",404);
            }

    }

    public function getLoginActivitiesByUser()
    {
        $login_activities = $this->activities_service->getLoginActivitiesByUser($this->user->id);
        $data=[];
        if(count($login_activities) > 0){
            foreach($login_activities as $index=>$login_activity){
                if($login_activity->user_id){
                    $user = $this->register_service->getUserByAdmin($login_activity->user_id);
                    $data[$index]=[
                        "id"=> $login_activity->id,
                        "uu_id"=> $login_activity->uu_id,
                        "user_type"=> $login_activity->user_type,
                        "user_id"=> $login_activity->user_id,
                        "user_name"=> isset($user->name) ? $user->name : null,
                        "admin_id"=> $login_activity->admin_id,
                        "login_time"=> $login_activity->login_time,
                        "logout_time"=> $login_activity->logout_time,
                        "ip_address"=> $login_activity->ip_address,
                        "created_at"=> $login_activity->created_at,
                        "updated_at"=> $login_activity->updated_at,
                    ];
                }
            }
            return $this->success($data);
        }else{
            return $this->error("Login Activities Not Found",404);
        }

    }

    public function getAdminLoginActivities(){
        $login_activities = $this->activities_service->getAdminLoginActivities();
        $data=[];
        if(count($login_activities) > 0){
            foreach($login_activities as $index=>$login_activity){
                if($login_activity->admin_id){
                    $admin = $this->admin_service->find($login_activity->admin_id);
                    $user = $this->register_service->getUserByAdmin($login_activity->user_id);
                    $data[$index]=[
                        "uu_id"=> $login_activity->uu_id,
                        "user_type"=> $login_activity->user_type,
                        "user_id"=> isset($user->uu_id) ? $user->uu_id : null,
                        "user_name"=> isset($user->name) ? $user->name : null,
                        "admin_name"=> isset($admin->name) ? $admin->name : null,
                        "admin_id"=> isset($admin->uu_id) ? $admin->uu_id : null,
                        "login_time"=> $login_activity->login_time,
                        "logout_time"=> $login_activity->logout_time,
                        "ip_address"=> $login_activity->ip_address,
                        "created_at"=> $login_activity->created_at,
                        "updated_at"=> $login_activity->updated_at,
                    ];
                }
            }
            return $this->success($data);
        }else{
            return $this->error("Login Activities Not Found",404);
        }
    }

    public function getCompanyActivities(){
        $company_activities = $this->activities_service->getCompanyActivities();
        $data=[];
        if(count($company_activities) != null){
            foreach($company_activities as $index=>$company_activity){
                    $phone_number_name = null;
                    $contacts = null;
                    $groups = null;
                    $group_contacts = null;
                    $message = null;
                    $company =  $this->company_service->adminFindByUuid($company_activity->company_id);
                    if($company != null){
                        if($company_activity->phone_id != null){
                            $phone_number_name = $this->phone_number_service->findByCompany($company_activity->phone_id,$company->id);
                            $phone_number_name = isset($phone_number_name[0]) ? $phone_number_name[0]->nick_name : null;
                        }
                        if($company_activity->contacts != null){
                            $contacts = $this->contact_service->findContactByCompany($company_activity->contacts,$company->id);
                            $contacts = isset($contacts) ? $contacts->phone_number : null;
                        }
                        if($company_activity->group != null){
                            $groups = $this->group_service->find($company_activity->group,$company->id);
                            $groups = isset($groups[0]) ? $groups[0]->group_name : null;
                        }
                        if($company_activity->group_contacts != null){
                           $group_contacts = $contacts;
                        }
                        if($company_activity->message != null){
                            $message = $this->sms_service->find($company_activity->message,$company->id);
                            $message = isset($message[0]) ? $message[0]->message : null;
                         }
                    }
                    $data[$index] = [
                        "uu_id"=> $company_activity->uu_id,
                        "type"=> $company_activity->type,
                        "company_id"=> isset($company->uu_id) ? $company->uu_id : null,
                        "company_name"=> isset($company->name) ? $company->name : null,
                        "activity"=> $company_activity->activity,
                        "phone_nick_name"=> $phone_number_name,
                        "buy_number"=> $company_activity->buy_number,
                        "disocnnect"=> $company_activity->disocnnect,
                        "reconnect"=> $company_activity->reconnect,
                        "message_text"=> $message,
                        "group_name"=> $groups,
                        "contact_number"=> $contacts,
                        "group_contacts_number"=> $group_contacts,
                        "wallet"=> $company_activity->wallet,
                        "ip_address"=> $company_activity->ip_address,
                        "created_at"=> $company_activity->created_at,
                        "updated_at"=> $company_activity->updated_at
                    ];
            }
            
            return $this->success($data);
        }
        else{
            return $this->error("Company Activities Not Found",404);
        }
    }

    public function getCompanyActivitiesByUser(Request $request){
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        $company_activities = $this->activities_service->getCompanyActivitiesByUser($company->id);
        $data=[];
        if(count($company_activities) != null){
            foreach($company_activities as $index=>$company_activity){
                    $phone_number_name = null;
                    $contacts = null;
                    $groups = null;
                    $group_contacts = null;
                    $message = null;
                    $company =  $this->company_service->findByAdmin($company_activity->company_id);
                    if($company != null){
                        if($company_activity->phone_id != null){
                            $phone_number_name = $this->phone_number_service->findByCompany($company_activity->phone_id,$company->id);
                            $phone_number_name = isset($phone_number_name[0]) ? $phone_number_name[0]->nick_name : null;
                        }
                        if($company_activity->contacts != null){
                            $contacts = $this->contact_service->findContactByCompany($company_activity->contacts,$company->id);
                            $contacts = isset($contacts[0]) ? $contacts[0]->phone_number : null;
                        }
                        if($company_activity->group != null){
                            $groups = $this->group_service->find($company_activity->group,$company->id);
                            $groups = isset($groups[0]) ? $groups[0]->group_name : null;
                        }
                        if($company_activity->group_contacts != null){
                           $group_contacts = $contacts;
                        }
                        if($company_activity->message != null){
                            $message = $this->sms_service->find($company_activity->message,$company->id);
                            $message = isset($message[0]) ? $message[0]->message : null;
                         }
                    }
                    $data[$index] = [
                        "uu_id"=> $company_activity->uu_id,
                        "type"=> $company_activity->type,
                        "company_id"=> isset($company->uu_id) ? $company->uu_id : null,
                        "company_name"=> isset($company->name) ? $company->name : null,
                        "activity"=> $company_activity->activity,
                        "phone_nick_name"=> $phone_number_name,
                        "buy_number"=> $company_activity->buy_number,
                        "disocnnect"=> $company_activity->disocnnect,
                        "reconnect"=> $company_activity->reconnect,
                        "message_text"=> $message,
                        "group_name"=> $groups,
                        "contact_number"=> $contacts,
                        "group_contacts_number"=> $group_contacts,
                        "wallet"=> $company_activity->wallet,
                        "ip_address"=> $company_activity->ip_address,
                        "created_at"=> $company_activity->created_at,
                        "updated_at"=> $company_activity->updated_at
                    ];
            }
            
            return $this->success($data);
        }
        else{
            return $this->error("Company Activities Not Found",404);
        }
    }
}
