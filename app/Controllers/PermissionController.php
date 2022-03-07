<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Models\Role;
use App\Services\AdminService;
use App\Services\PermissionService;
use App\Services\RegisterService;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    use ResponseAPI;
    protected $permission_service, $admin_service, $user_service;
    public function __construct()
    {
        $this->permission_service = new PermissionService;
        $this->admin_service = new AdminService;
        $this->user_service = new RegisterService;

    }

    public function createPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions',
            'displayName' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $permission = $this->permission_service->createPermission($request->all());
            return $this->success($permission);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function getPermissions()
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

    public function findPermission($id)
    {
        try{
            $permission = $this->permission_service->findPermission($id);
            if(!isset($permission))
            {
                return $this->error("Permission Not Found",404);
            }
            return $this->success($permission);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function updatePermission(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|'.Rule::unique('permissions')->ignore($this->permission_service->findPermission($id)),
            'displayName' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try{
            $role = $this->permission_service->findPermission($id);
            if(!isset($role))
            {
                return $this->error("Permission Not Found",404);
            }
            $data = [
                'name' => $request->name,
                'display_name' =>$request->displayName,
                'description' => $request->description,
            ];
            $updated_permission = $this->permission_service->updatePermission($data,$id);
            return $this->success($updated_permission);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles',
            'displayName' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $role = $this->permission_service->createRole($request->all());
            return $this->success($role);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function getRoles()
    {
        try{
            $roles = $this->permission_service->getRoles();
            return $this->success($roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function findRole($id)
    {
        try{
            $role = $this->permission_service->findRole($id);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            return $this->success($role);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function updateRole(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|'.Rule::unique('roles','name')->ignore(Role::find($id)),
            'displayName' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try{
            $role = $this->permission_service->findRole($id);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }

            $data = [
                'name' => $request->name,
                'display_name' =>$request->displayName,
                'description' => $request->description,
            ];
            $updated_role = $this->permission_service->updateRole($data,$id);
            return $this->success($updated_role);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function deleteRole($id)
    {
        try{
            $role = $this->permission_service->findRole($id);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $roles = $this->permission_service->deleteRole($id);
            return $this->success($roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function deletePermission($id)
    {
        $success['delete'] =  $this->permission_service->deletePermission($id);
        if($success['delete']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Permission Not Found",'404');
        }
    }

    public function assignPermissionstoRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'permission' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $permissions = new Collection;
            foreach($request['permission'] as $permission_id)
            {
                $permission = $this->permission_service->findPermission($permission_id);
                if(!isset($permission) || $role->hasPermission($permission->name))
                {
                    continue;
                }
                $permissions->push($permission);

            }
            if(count($permissions) == 0)
            {
                return $this->error("Permission Not Found Or the Permission is already set");
            }
            $role->attachPermissions($permissions);

            return $this->success($role->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function assignPermissiontoRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'permission' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $permission = $this->permission_service->findPermission($request['permission']);
            if(!isset($permission))
            {
                return $this->error("Permission Not Found Or the Permission is already set");
            }
            $role->attachPermission($permission);

            return $this->success($role->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function assignAllPermissionsToRole($id)
    {
        try{
            $role = $this->permission_service->findRole($id);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $permissions = $this->permission_service->getPermissions();
            $permissions_collection = new Collection;
            foreach($permissions as $permission)
            {
                $permission = $this->permission_service->findPermission($permission->id);
                if(!isset($permission) || $role->hasPermission($permission->name))
                {
                    continue;
                }
                $permissions_collection->push($permission);

            }
            if(count($permissions_collection) == 0)
            {
                return $this->error("Permissions Not Found",404);
            }
            $role->attachPermissions($permissions_collection);
            return $this->success($role->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }


    public function removePermissionsFromRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'permission' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $permissions = new Collection;
            foreach($request['permission'] as $permission_id)
            {
                $permission = $this->permission_service->findPermission($permission_id);
                if(!isset($permission) || !$role->hasPermission($permission->name))
                {
                    continue;
                }
                $permissions->push($permission);

            }
            if(count($permissions) == 0)
            {
                return $this->error("Permission Not Found or the Role don't have these permissions");
            }
            $role->detachPermissions($permissions);

            return $this->success($role->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function removePermissionFromRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'permission' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $permission = $this->permission_service->findPermission($request['permission']);

            if(!isset($permission))
            {
                return $this->error("Permission Not Found or the Role don't have this permission");
            }
            $role->detachPermission($permission);
            return $this->success($role->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function revokeAllPermissionsFromRole($id)
    {
        try{
            $role = $this->permission_service->findRole($id);
            if(!isset($role))
            {
                return $this->error("Role Not Found",404);
            }
            $permissions = $this->permission_service->getPermissions();
            if(count($permissions) == 0)
            {
                return $this->error("Permissions Not Found",404);
            }
            $role->detachPermissions($permissions);
            return $this->success($role->permissions);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function assignRolesToAdmins(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'roles' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        try
        {
            $user = $this->admin_service->find($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $roles = new Collection;
            foreach($request['roles'] as $role_id)
            {
                $role = $this->permission_service->findRole($role_id);
                if(!isset($role) || $user->hasRole($role->name))
                {
                    continue;
                }
                $roles->push($role);

            }
            if(count($roles) == 0)
            {
                return $this->error("Roles Not Found or the User already have these roles");
            }
            $user->attachRoles($roles);

            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function assignRoleToAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'role' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        try
        {
            $user = $this->admin_service->findAdminUser($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Role Not Found Or the Role is already set");
            }
            $user->attachRole($role);

            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }
    public function assignAllRoles($id)
    {
        try
        {
            $user = $this->admin_service->findAdminUser($id);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $roles = $this->permission_service->getRoles();
            $roles_collection = new Collection;
            foreach($roles as $role)
            {
                $role = $this->permission_service->findRole($role->id);
                if(!isset($role) || $user->hasRole($role->name))
                {
                    continue;
                }
                $roles_collection->push($role);
            }
            if(count($roles_collection) == 0)
            {
                return $this->error("Roles Not Found or the User already have these roles");
            }
            $user->attachRoles($roles_collection);
            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }
    public function removeRoleFromAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'role' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $user = $this->admin_service->findAdminUser($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role) || !$user->hasRole($role->name))
            {
                return $this->error("Role Not Found or the User don't have that role");
            }
            $user->detachRole($role);
            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function removeAllRoles($id)
    {
        try
        {
            $user = $this->admin_service->findAdminUser($id);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $roles = $this->permission_service->getRoles();
            $roles_collection = new Collection;
            foreach($roles as $role)
            {
                $role = $this->permission_service->findRole($role->id);
                if(!isset($role) || !$user->hasRole($role->name))
                {
                    continue;
                }
                $roles_collection->push($role);
            }
            if(count($roles_collection) == 0)
            {
                return $this->error("Roles Not Found or the User dont have these roles");
            }
            $user->detachRoles($roles_collection);
            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function removeRolesFromAdmins(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'roles' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        try
        {
            $user = $this->admin_service->findAdminUser($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $roles = new Collection;
            foreach($request['roles'] as $role_id)
            {
                $role = $this->permission_service->findRole($role_id);
                if(!isset($role) || !$user->hasRole($role->name))
                {
                    continue;
                }
                $roles->push($role);
            }
            if(count($roles) == 0)
            {
                return $this->error("Roles Not Found or the User don't have these roles");
            }
            $user->detachRoles($roles);

            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function viewAssignedPermissions()
    {
        $roles_assigned_permissions = $this->permission_service->viewAssignedPermissions();
        return $this->success($roles_assigned_permissions);
    }

    public function assignRolesToCustomers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'roles' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        try
        {
            $user = $this->user_service->find($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $roles = new Collection;
            foreach($request['roles'] as $role_id)
            {
                $role = $this->permission_service->findRole($role_id);
                if(!isset($role) || $user->hasRole($role->name))
                {
                    continue;
                }
                $roles->push($role);

            }
            if(count($roles) == 0)
            {
                return $this->error("Roles Not Found or the User already have these roles");
            }
            $user->attachRoles($roles);

            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function assignRoleToCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'role' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        try
        {
            $user = $this->user_service->find($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role))
            {
                return $this->error("Role Not Found Or the Role is already set");
            }
            $user->attachRole($role);

            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function removeRoleFromCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'role' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        try
        {
            $user = $this->user_service->find($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $role = $this->permission_service->findRole($request['role']);
            if(!isset($role) || !$user->hasRole($role->name))
            {
                return $this->error("Role Not Found or the User don't have that role");
            }
            $user->detachRole($role);
            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function removeRolesFromCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'roles' => 'required|array|min:1',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        try
        {
            $user = $this->user_service->find($request['userId']);
            if(!isset($user))
            {
                return $this->error("Admin User Not Found",404);
            }
            $roles = new Collection;
            foreach($request['roles'] as $role_id)
            {
                $role = $this->permission_service->findRole($role_id);
                if(!isset($role) || !$user->hasRole($role->name))
                {
                    continue;
                }
                $roles->push($role);
            }
            if(count($roles) == 0)
            {
                return $this->error("Roles Not Found or the User don't have these roles");
            }
            $user->detachRoles($roles);

            return $this->success($user->roles);
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }


}
