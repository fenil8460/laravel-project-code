<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\FindAPI;
use Illuminate\Support\Collection;

class PermissionRepository
{

    use FindAPI;

    public function createRole($data)
    {
        if(!isset($data['description']))
        {
            $data['description'] = null;
        }
        $role = Role::create([
            'name' => $data['name'],
            'display_name' => $data['displayName'],
            'description' => $data['description'],
        ]);
        return $role;
    }

    public function findRole($id)
    {
        return Role::where('id',$id)->first();
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function updateRole($data,$id)
    {
        $role = Role::where('id',$id)->first();
        if(!isset($data['description']))
        {
            $data['description'] = null;
        }
        $role->update($data);
         return $role;
    }

    public function deleteRole($id)
    {
        $role = Role::where('id',$id)->first();
        return $this->destroyResource($role);
    }

    public function createPermission($data)
    {
        if(!isset($data['description']))
        {
            $data['description'] = null;
        }
        $permission = Permission::create([
            'name' => $data['name'],
            'display_name' => $data['displayName'],
            'description' => $data['description'],
        ]);
        return $permission;
    }

    public function getPermissions()
    {
        return Permission::all();
    }

    public function findPermission($permission)
    {
        return Permission::where('id',$permission)->first();
    }


    public function updatePermission($data,$id)
    {
        $permission = Permission::where('id',$id)->first();
        if(!isset($data['description']))
        {
            $data['description'] = null;
        }
         $permission->update($data);
         return $permission;
    }

    public function deletePermission($id)
    {
        $permission = Permission::where('id',$id)->first();
        return $this->destroyResource($permission);
    }

    public function viewAssignedPermissions()
    {
        $roles = Role::all();
        $permissions =Permission::all();
        $assigned_and_not_permissions = new Collection;
        foreach($roles as $role)
        {
            $not_assigned_permissions =new Collection;
            foreach($permissions as $permission)
            {
                if(!$role->hasPermission($permission->name))
                {
                    $not_assigned_permissions->push($permission);
                }
            }
            $assigned_and_not_permissions->push([
                'role' => $role,
                'permissions' => $role->permissions,
                "noPermissions" => $not_assigned_permissions,
            ]);
        }
        return $assigned_and_not_permissions;

    }

    public function viewClientAssignedPermissions($user_id)
    {
        $user = User::where('uu_id',$user_id)->first();
        $permissions =Permission::all();
        $assigned_and_not_permissions = new Collection;
        $not_assigned_permissions =new Collection;
        foreach($permissions as $permission)
        {
            if(!$user->hasPermission($permission->name))
            {
                $not_assigned_permissions->push($permission);
            }
        }
        $assigned_and_not_permissions->push([
            'user' => $user,
            'permissions' => $user->permissions,
            "noPermissions" => $not_assigned_permissions,
        ]);
        return $assigned_and_not_permissions;
    }

}
