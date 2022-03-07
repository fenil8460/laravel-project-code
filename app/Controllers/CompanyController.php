<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use App\Services\CompanyService;
use App\Services\CompanyActivitiesService;
use App\Http\Controllers\Controller as Controller;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Events\CompanyActivity;
use App\Services\RegisterService;
use App\Models\Company;
use App\Models\PhoneNumber;
use App\Models\Group;
use App\Models\Contact;
use App\Models\GroupContact;
use App\Services\NotificationService;
use Bavix\Wallet\Models\Wallet;

class CompanyController extends Controller
{
    use ResponseAPI;

    protected $company_service,$notification_service;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            return $next($request);
        });
        $this->company_service = new CompanyService;
        $this->company_activities_service = new CompanyActivitiesService;
        $this->notification_service = new NotificationService;
        $this->register_service = new RegisterService;
    }


    public function index()
    {
        $companies =  $this->company_service->getData();
        $client_companies = [];
        $myCompany = [];
        foreach($companies['myCompany'] as $index=>$company){
            $user = $this->company_service->getCompanyUser($company->user_id);
            $myCompany[$index] = [
                "uu_id" => $company->uu_id,
                "user_id" => isset($user->uu_id) ? $user->uu_id : null,
                "name" => $company->name,
                "deleted_at" => $company->deleted_at,
                "created_at" => $company->created_at,
                "updated_at" => $company->updated_at,
                "nick_name" => $company->nick_name,
            ];
        }

        foreach($companies['invitedCompanies'] as $index=>$company){
            $user = $this->company_service->getCompanyUser($company->user_id);
            $client_companies[$index] = [
                "uu_id" => $company->uu_id,
                "user_id" => isset($user->uu_id) ? $user->uu_id : null,
                "name" => $company->name,
                "deleted_at" => $company->deleted_at,
                "created_at" => $company->created_at,
                "updated_at" => $company->updated_at,
                "nick_name" => $company->nick_name,
            ];
        }
        if(isset($companies)) {
            return $this->success(['myCompany' => $myCompany,'invitedCompanies' => $client_companies]);
        }
        else{
            return $this->error("Company Not Found",'404');
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $nick_name=$request->name;
        if($request->nick_name){
            $nick_name=$request->nick_name;
        }

        $data = [];
        $data = [
            'name' => $request->name,
            'nick_name' => $nick_name,
            'uu_id' => (string)Str::uuid(),
            'user_id' => Auth::user()->id,
         ];
         try
         {
            $success = $this->company_service->store($data);
            $success['user_id'] = Auth::user()->uu_id;
            $success['type']='company';
            event(new CompanyActivity($success));
            return $this->success($success);
         }
         catch(Exception $e)
         {
            return $this->error($e->getMessage(),'404');
         }
    }

    public function show($id)
    {
        $data = $this->company_service->show($id);
        $data['Message'][0]->user_id = Auth::user()->uu_id;
        if($data['status'] == true) {
            return $this->success($data);
        }
        else{
            return $this->error("Company Not Found",'404');
        }
    }

    public function update($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->name;
        $data = [];
        $nick_name=$request->name;
        if($request->nick_name){
            $nick_name=$request->nick_name;
        }
        $data = [
            'name' => $input,
            'nick_name'=>$nick_name
        ];
        $success['update'] = $this->company_service->update($data,$id);
        $success['company_name'] = $request->name;
        if($success['update']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Company Not Found",'404');
        }

    }

    public function destroy($id)
    {
        $success['delete'] =  $this->company_service->destroy($id);
        if($success['delete']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Company Not Found",'404');
        }
    }

    public function getCompany(){

            $companies = $this->company_service->getAllCompanies();
            $data = [];
            if(count($companies) > 0 && $companies != null){
                foreach($companies as $index=>$company){
                    $user = $this->company_service->getCompanyUser($company->user_id);
                    $phone = $this->company_service->getCompanyPhone($company->id);
                    $data[$index] = [
                        "uu_id"=>$company->uu_id,
                        "user_id"=>isset($user->uu_id) ? $user->uu_id : null,
                        "name"=>$company->name,
                        "nick_name"=>$company->nick_name,
                        "created_at"=>$company->created_at,
                        "updated_at"=>$company->updated_at,
                        "deleted_at"=>$company->deleted_at,
                        "user"=>$user,
                        "phone"=>$phone
                    ];
                }
                return $this->success($data);
            }
            else{
                return $this->error("Company Not Found",'404');
            }

    }

    public function getCompanyByUser($id){
        $companies = $this->company_service->getCompanyByAdmin($id);
        $data = [];
        if(count($companies) > 0 && $companies != null){
            foreach($companies as $index=>$company){
                $user =  $this->register_service->getUserByAdmin($company->user_id);
                $phone = $this->company_service->getCompanyPhone($company->id);
                $data[$index] = [
                    "uu_id"=>$company->uu_id,
                    "user_id"=>isset($user->uu_id) ? $user->uu_id : null,
                    "name"=>$company->name,
                    "nick_name"=>$company->nick_name,
                    "created_at"=>$company->created_at,
                    "updated_at"=>$company->updated_at,
                    "deleted_at"=>$company->deleted_at,
                    "phone"=>$phone
                ];
            }
            return $this->success($data);
        }
        else{
            return $this->error("Company Not Found",'404');
        }
    }

    public function storeCompanyActivities(Request $request){

        if($request->phone_id != null)
        {
            $type = PhoneNumber::class;
        }
        else if($request->group != null)
        {
            $type = Group::class;
        }
        else if($request->contacts != null)
        {
            $type = Contact::class;
        }
        else if($request->group_contacts != null)
        {
            $type = GroupContact::class;
        }
        else if($request->wallet != null)
        {
            $type = Wallet::class;
        }
        else
        {
            $type = Company::class;
        }
            $data = [
                'type' => $type,
                'uu_id' => (string)Str::uuid(),
                'company_id' => $request->company_id,
                'activity' => $request->activity,
                'phone_id' => $request->phone_id,
                'buy_number' => $request->buy_number,
                'disocnnect' => $request->disocnnect,
                'reconnect' => $request->reconnect,
                'ip_address' => request()->ip(),
                'message' => $request->message,
                'group' => $request->group,
                'contacts' => $request->contacts,
                'group_contacts' => $request->group_contacts,
                'wallet' => $request->wallet,
             ];
             try
             {
                $success = $this->company_activities_service->storeCompanyActivities($data);
                return $this->success($success);
             }
             catch(Exception $e)
             {
                return $this->error($e->getMessage(),'404');
             }

    }

    public function viewNotifications($company_id)
    {
        $company = $this->company_service->getCompanyByUuid($company_id);

        if(!isset($company))
        {
            return $this->error("Company Not Found", 404);
        }
        $notifications = $this->notification_service->viewNotifications($company->id);
        return $this->success($notifications);

    }
}
