<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
            ['name' => 'User', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
            ['name' => 'Guest', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
        ]);
    }
}
