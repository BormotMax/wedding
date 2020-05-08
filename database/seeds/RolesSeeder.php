<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['role_name' => Role::ROLE_USER],
            ['role_name' => Role::ROLE_PHOTOGRAPHER],
            ['role_name' => Role::ROLE_MODERATOR],
            ['role_name' => Role::ROLE_ADMIN],
        ]);
    }
}
