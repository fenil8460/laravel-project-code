<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Traits\FindAPI;

class PasswordResetRepository
{
    use FindAPI;
    public function create($data)
    {
        $reset_password = PasswordReset::insert($data);
        return $reset_password;
    }

    public function findByEmail($email){
        $reset_password = PasswordReset::where('email', '=', $email)->first();
        return $reset_password;
    }

    public function findByToken($token){
        $reset_password = PasswordReset::where('token', '=', $token)->first();
        return $reset_password;
    }

    public function delete($email){
        $reset_password = PasswordReset::where('email', '=', $email)->delete();
        return $reset_password;
    }

    public function getUser()
    {
        $user = PasswordReset::all();
        return $user;
    }

    public function getUserByAdmin($id)
    {
        $user = PasswordReset::find($id);
        return $user;
    }

    public function find($id)
    {
        $user = PasswordReset::find($id);
        return $user;
    }
    public function findByUUID($id)
    {
        $user = PasswordReset::where("uu_id",$id)->first();
        return $user;
    }

    

}
