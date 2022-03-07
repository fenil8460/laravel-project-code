<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name' => 'create-user',
            'display_name' => 'Create User',
            'description' => 'Create User',
        ]);

        Permission::create([
            'name' => 'wallet-manage',
            'display_name' => 'Manage Wallet	',
            'description' => 'Manage Wallet',
        ]);

        Permission::create([
            'name' => 'manage-company',
            'display_name' => 'Manage Company',
            'description' => 'Manage Company',
        ]);

        Permission::create([
            'name' => 'manage-contact',
            'display_name' => 'Manage Contacts',
            'description' => 'Manage Contacts',
        ]);

        Permission::create([
            'name' => 'view-users',
            'display_name' => 'View Users',
            'description' => 'View Users',
        ]);

    }
}
