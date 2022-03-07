<?php

namespace Database\Seeders;

use App\Models\ClientStatus;
use Illuminate\Database\Seeder;

class ClientStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ClientStatus::create([
            'name' => 'Client and User Created',
        ]);

        ClientStatus::create([
            'name' => 'User Permissions Set',
        ]);

        ClientStatus::create([
            'name' => 'Invitation Sent',
        ]);

        ClientStatus::create([
            'name' => 'Invitation Accepted',
        ]);

        ClientStatus::create([
            'name' => 'User Registration Completed',
        ]);

        ClientStatus::create([
            'name' => 'Invitation Rejected',
        ]);

    }
}
