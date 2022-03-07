<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Mail\InviteClientMail;
use App\Services\ClientService;
use App\Services\CompanyService;
use App\Services\PermissionService;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Services\RegisterService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    use ResponseAPI;

    protected $client_service,$register_service,$permission_service,$company_service;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });

        $this->client_service = new ClientService;
        $this->company_service = new CompanyService;
        $this->register_service = new RegisterService;
        $this->permission_service = new PermissionService;
    }


    public function showClientInvites(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        $clients = $this->client_service->showClients($company->id);
        $clientss = new Collection();
        foreach($clients as $client)
        {
            $user = $this->company_service->getCompanyUser($client->user_id);
            $data = [
                'name'=> $client->name,
                'email'=> $client->email,
                'nick_name'=> $client->nick_name,
                'company' => $client->clientCompany->name,
                'company_id' => $company->uu_id,
                'status' => $client->clientStatus->name,
                'status_id' => $client->status,
                'user_id' => $user->uu_id,
                'uu_id' => $client->uu_id,
            ];
            $clientss->push($data);
        }
        return $this->success($clientss);
    }

    public function createClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'nick_name' => 'required',
            'companyId' => 'required',
        ]); 
        if($validator->fails()){
            return $this->error( $validator->errors());
        }
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        $client = $this->client_service->findClientByEmail($request->email,$company->id);
        if(count($client) > 0){
            return $this->error('Client created and Invitation probably sent, pls search for the client with this email id','404');
        }
        $user = $this->register_service->findByEmail($request->email);
        if(isset($user))
        {
            $data = [
                'name' => $request->name,
                'nick_name' => $request->nick_name,
                'uu_id' => (string)Str::uuid(),
                'email' => $request->email,
                'company_id' => $company->id,
                'user_id' => $user->id,
                'status' => '1',
            ];
        }
        else{
            $user_data = [];
            $user_data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt(rand(10, 999)),
            ];
            $user = $this->register_service->create($user_data);
            $user->is_approved = 0;
            $user->save();
            $data = [];
            $data = [
                'name' => $request->name,
                'nick_name' => $request->nick_name,
                'uu_id' => (string)Str::uuid(),
                'email' => $request->email,
                'company_id' => $company->id,
                'user_id' => $user->id,
                'status' => '1',
             ];
        }
        try
        {
            $client = $this->client_service->createClient($data);
            $client['company_id'] = $company->uu_id;
            $client['user_id'] = $user->uu_id;
            return $this->success($client);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage(),'404');
        }
    }

    public function setClientPermissions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'permission' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error( $validator->errors());
        }

        try
        {
            $user = $this->register_service->findByUUID($request->user_id);
            if(!$user)
            {
                return $this->error("User Not Found",404);
            }
            $permissions = new Collection();
            foreach($request['permission'] as $permission_id)
            {
                $permission = $this->permission_service->findPermission($permission_id);
                if(!isset($permission))
                {
                    continue;
                }
                $permissions->push($permission);

            }
            if(count($permissions) == 0)
            {
                return $this->error("Permissions Not Selected");
            }
            $user->attachPermissions($permissions);

            return $this->success($user->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function viewPermissionsById($id)
    {
        try{
            $permissions = $this->permission_service->viewClientAssignedPermissions($id);
            return $this->success($permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function editPermissionsById($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try{
            $user = $this->register_service->findByUUID($id);
            $permission = $this->permission_service->findPermission($request['permission']);
            if(!isset($permission))
            {
                return $this->error("Permission Not Found or the User don't have this permission");
            }
            $user->detachPermission($permission);
            return $this->success($user->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function viewPermissions()
    {
        try{
            $permissions = $this->permission_service->getPermissions();
            return $this->success($permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function sendInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyId' => 'required',
            'clientId' => 'required',
            'baseURL' => 'required',
        ]);
        if($validator->fails()){
            return $this->error( $validator->errors());
        }
        $company = $this->company_service->findByUuid($request->companyId,$this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found",404);
        }
        $client = $this->client_service->findClientByUuid($request->clientId);
        if(!isset($client))
        {
            return $this->error("Client Not Found",404);
        }
        $data = [
            'company' =>$company->nick_name,
            'companyId' => $company->id,
            'clientId' => $request->clientId,
            'user' =>$this->user->name,
            'link' => $request->baseURL,
        ];
        Mail::to($client->email)->send(new InviteClientMail($data));

        if (Mail::failures()) {
           return $this->error('Sorry! Please try again latter');
        }else{
            $client->status = 3;
            $client->save();
           return $this->success('Email Sent Successfully');
        }
    }

    public function acceptInvitation($client_id)
    {
        $client = $this->client_service->findClientByUuid($client_id);
        if(!isset($client))
        {
            $this->error("Client Not Found");
        }
        $client->status = 4;
        $client->save();

        $client->companies()->attach($client->company_id);
        $user = $this->register_service->find($client->user_id);
        if($user->is_approved == 1)
        {
            return redirect()->to('http://localhost:4200/login');
        }
        else{
            return redirect()->to('http://localhost:4200/register?user='.$user->email.'&client_id='.$client->uu_id);
        }

    }

    public function registerClientAsUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clientId' => 'required',
            'name' => 'required',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        $client = $this->client_service->findClientByUuid($request->clientId);
        if(!isset($client))
        {
            $this->error("Client Not Found");
        }
        $user = $this->register_service->findByEmail($client->email);

        if(!isset($user))
        {
            $this->error("User Not Found");
        }

        $data = [
            'name' => $request->name,
            'password' => bcrypt($request->password),

        ];
        $user_data = $this->register_service->updateUser($data,$user->id);
        $user_data->is_approved = 1;
        $user_data->save();
        return $this->success($user_data);
    }

    public function getUserFromClient($client_id)
    {
        $client = $this->client_service->findClientByUuid($client_id);
        if(!isset($client))
        {
            return $this->error("Client Not Found",404);
        }

        $user = $this->register_service->find($client->user_id);
        if(!isset($user))
        {
            return $this->error("User Not Found",404);
        }
        return $this->success($user);
    }

    public function declineInvitation($client_id)
    {
        $client = $this->client_service->findClientByUuid($client_id);
        if(!isset($client))
        {
            $this->error("Client Not Found");
        }
        $client->status = 6;
        $client->save();
        $this->client_service->removeClientCompany($client);
        return $this->success(["message" => "Company Invitation Declined by client","client"=> $client->name]);
    }

    public function deleteClient($id){
        $client = $this->client_service->findClientByUuid($id);
        $user = $this->register_service->find($client->user_id);
        $company =  $this->company_service->findByAdmin($client->company_id);
        $client_created_company =  $this->company_service->getCompanyByUser($client->user_id);
        if(!$company){
            return $this->error("Company Not Found",'404');
        }
        $this->client_service->deleteClientCompany($client->id); 
        $client_ids_of_same_user = $this->client_service->clientIdsOfSameUser($client->user_id);
            if(count($client_ids_of_same_user) > 0){
                $client_company = $this->client_service->findClientCompany($client_ids_of_same_user);
            }
             if(count($client_created_company) == 0 && count($client_company) == 0){
                $user->is_approved = 0;
                $user->save();
            }
        $success['delete'] = $this->client_service->deleteClient($id);
        if($success['delete']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Client Not Found",'404');
        }
    }
}
