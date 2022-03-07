<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Role;
use App\Traits\FindAPI;
use Illuminate\Support\Facades\Crypt;

class AdminRepository
{
    use FindAPI;
    public function store($data)
    {
        return Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Crypt::encryptString($data['password']),
            'uu_id' => $data['uu_id'],
        ]);
    }

    public function find($id)
    {
        $admin = Admin::where('id',$id)->first();
        return $admin;
    }

    public function getAdminUsers()
    {
        $users = Admin::get();
        return $users;
    }

    public function findAdminUser($id)
    {
        $user = Admin::where('uu_id',$id)->first();
        return $user;
    }

    public function updateAdminUser($data,$id)
    {
        $user = Admin::where('uu_id',$id)->first();
        $user->update($data);
        return $user;
    }

    public function deleteAdminUser($id)
    {
        $admin_user = Admin::where('uu_id',$id);
        return $this->destroyResource($admin_user);
    }



}
