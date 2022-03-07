<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Laratrust\Traits\LaratrustUserTrait;

class AdminUserSeeder extends Seeder
{
    use LaratrustUserTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admin = Admin::create([
            'name' => 'SuperAdminOGT',
            'email' => 'superadmin.ogt@gmail.com',
            'password' => bcrypt('ogtsuperadmin123!@#78&*'),
        ]);

        $role = Role::where('name','super_admin')->first();
        if($role)
        {
            $admin->attachRole($role);
        }
        else{
            $super_admin = Role::create([
                'name'         => 'super_admin',
                'display_name' => 'Super Admin', // optional
                'description'  => 'Is the Super Admin', // optional
            ]);
            $admin->attachRole($super_admin);
        }

        $permissions = Permission::all();

        if($permissions)
        {
            $role = Role::where('name',"super_admin")->first();
            $role->attachPermissions($permissions);
        }
    }
}
