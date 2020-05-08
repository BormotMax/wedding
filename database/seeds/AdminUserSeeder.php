<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::where('role_name', Role::ROLE_ADMIN)->first();
        $user = User::create([
            'name' => 'Admin',
            'key' => 'adminkey',
            'role_id' => $roleAdmin->id,
        ]);
    }
}
