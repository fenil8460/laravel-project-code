<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Traits\ResponseAPI;
use App\Http\Controllers\Controller as Controller;
use App\Models\Admin;
use App\Models\Role;
use App\Services\AdminService;
use App\Services\CompanyService;
use App\Services\PermissionService;
use App\Services\RegisterService;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;


class AdminController extends Controller
{
    use ResponseAPI;

    public $success_status = 200;
    protected $register_service,$admin_service,$permission_service;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
        $this->company_service = new CompanyService;
        $this->register_service = new RegisterService;
        $this->wallet_service = new WalletService();
        $this->admin_service = new AdminService;
        $this->permission_service = new PermissionService;
    }

    public function viewAdminUsers()
    {

            try{
                $users = $this->admin_service->getAdminUsers();
                if($users->count() == 0)
                {
                    return $this->error('No Admin Users Found',404);
                }

                return $this->success($users);
            }
            catch(Exception $e)
            {
                return $this->error($e->getMessage());
            }


    }

    public function createAdminUser(Request $request)
    {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:admins|email',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
                'role' => 'required',
            ]);
            if($validator->fails()){
                return $this->error($validator->errors());
            }
            $input = $request->all();
            $input['uu_id'] = (string)Str::uuid();
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Requested Role not found",404);
            }
            $admin_user = $this->admin_service->store($input);
            if($admin_user)
            {
                $admin_user->attachRole($role);
            }
            else{
                return $this->error("Admin Creation Failed",404);
            }
            $data = [
                'name' => $admin_user->name,
                'email' => $admin_user->email,
                'token' =>  $admin_user->createToken('admin OGT')->accessToken,
                'role' => $role->name,
                'message' => "Admin User Created and Role Assigned",
            ];
            return $this->success($data);


    }


    public function allUsers(Request $request)
    {

            $data = [];
            foreach($this->register_service->getUser() as $index=>$users){
                $data[$index] = [
                    "uu_id"=>$users->uu_id,
                    "name"=>$users->name,
                    "email"=>$users->email,
                    "active"=>$users->active,
                    "company"=>$this->company_service->getDataById($users->id),
                    "created_at"=>$users->created_at
                ];
            }
            return $this->sendResponse($data,$this->success_status);


    }

    public function banUser($id)
    {
        $user = $this->register_service->findByUUID($id);
        if(!isset($user))
        {
            return $this->error("User Not Found", 404);
        }
        $user->active = 0;
        $user->save();
        $data = [
            'user' =>$user,
            'status' => 'User Banned',
        ];
        return $this->success($data);

    }

    public function activateUser($id)
    {
        $user = $this->register_service->findByUUID($id);
        if(!isset($user))
        {
            return $this->error("User Not Found", 404);
        }
        $user->active = 1;
        $user->save();
        $data = [
            'user' =>$user,
            'status' => 'User Activated',
        ];
        return $this->success($data);
    }

    public function getUserByAdmin($id)
    {
            $user = $this->register_service->findByUUID($id);
            $data = [
                "uu_id" => $user->uu_id,
                "name" => $user->name,
                "email" => $user->email,
                "email_verified_at" => $user->email_verified_at,
                "password" => $user->password,
                "remember_token" => $user->remember_token,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "provider" => $user->provider,
                "provider_id" => $user->provider_id,
                "provider_token" => $user->provider_token,
                "provider_refresh_token" => $user->provider_refresh_token,
                "active" => $user->active,
                "is_approved" => $user->is_approved,
            ];
            return $this->success($data,$this->success_status);
    }

    public function findAdminUser($id)
    {

            try{
                $user = $this->admin_service->findAdminUser($id);
                if(!isset($user))
                {
                    return $this->error("Admin User Not Found",404);
                }
                $roles = $user->roles;
                return $this->success($user);
            }
            catch(Exception $e)
            {
                return $this->error($e->getMessage());
            }

    }

    public function updateAdminUser(Request $request,$id)
    {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if($validator->fails()){
                return $this->error($validator->errors());
            }

            $input = $request->all();
            if(isset($request['role']))
            {
                $role = $this->permission_service->findRole($request['role']);

            }
            $admin_user = $this->admin_service->updateAdminUser($input,$id);
            if($admin_user && isset($role))
            {
                $admin_user->attachRole($role);
            }
            $data = [
                'name' => $admin_user->name,
                'email' => $admin_user->email,
                'token' =>  $admin_user->createToken('admin OGT')->accessToken,
                'role' => $admin_user->getRoles(),
                'message' => "Admin User Updated",
            ];
            return $this->success($data);

    }

    public function deleteAdminUser($id)
    {
        $success['delete'] =  $this->admin_service->deleteAdminUser($id);
        if($success['delete']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Admin User Not Found",'404');
        }
    }

    public function viewAdminPermissions()
    {
        $permissions = $this->user->allPermissions();
        if(!isset($permissions))
        {
            return $this->error("No Permissions Found",'404');

        }
        $permission_names = new Collection;
        foreach($permissions as $permission)
        {
            $permission_names->push($permission->name);
        }

        return $this->success($permission_names);

    }

    public function depositAmountToWallet(Request $request)
    {

            $validator = Validator::make($request->all(), [
                'company_id'=> 'required',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $amount = $request->amount;
            $company_id = $request->company_id;
            try {
                $company = $this->company_service->getCompanyByUuid($company_id);
                if ($company != null) {
                        $company->deposit($amount);
                    $data = [
                        "amountDeposited" => $amount,
                        "walletBalance" => $company->balance
                    ];
                    return $this->success($data);
                } else {
                    return $this->error("Company Not Found", '404');
                }
            } catch (Exception $e) {
                return $this->error($e->getMessage(), 500);
            }

    }

    public function withdrawAmountFromWallet(Request $request)
    {

            $validator = Validator::make($request->all(), [
                'company_id'=> 'required',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $amount = $request->amount;
            $company_id = $request->company_id;
            try {
                $company = $this->company_service->getCompanyByUuid($company_id);
                if ($company != null) {
                    $company->withdraw($amount);
                    $data = [
                        "amountWithdrawn" => $amount,
                        "walletBalance" => $company->balance
                    ];
                    return $this->success($data);
                } else {
                    return $this->error("User Not Found", '404');
                }
            } catch (Exception $e) {
                return $this->error($e->getMessage(), 500);
            }

    }




}
