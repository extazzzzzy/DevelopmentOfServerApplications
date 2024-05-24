<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersAndRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_and_roles')->insert([
            ['user_id' => '1', 'role_id' => '1', 'created_by' => 0,],
            ['user_id' => '2', 'role_id' => '2', 'created_by' => 0,],
        ]);
    }
}
