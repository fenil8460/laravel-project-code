<?php

namespace App\Repositories;

use App\Models\User;
use App\Traits\FindAPI;

class RegisterRepository
{
    use FindAPI;
    public function create($data)
    {
        $user = User::create($data);
        return $user;
    }

    public function getUser()
    {
        $user = User::all();
        return $user;
    }

    public function getUserByAdmin($id)
    {
        $user = User::find($id);
        return $user;
    }

    public function find($id)
    {
        $user = User::find($id);
        return $user;
    }
    public function findByUUID($id)
    {
        $user = User::where("uu_id",$id)->first();
        return $user;
    }

    public function findByEmail($email){
        $user = User::where('email', '=', $email)->first();
        return $user;
    }

    public function updateUser($data, $id){
        $user = User::find($id);
        $user->update($data);
        return $user;
    }

}
