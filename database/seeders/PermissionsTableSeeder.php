<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = ['user', 'role', 'permission', 'user_and_role', 'role_and_permission'];
        $actions = ['get-list', 'get', 'create', 'update', 'delete', 'restore'];
        $permissions = [];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => $action . '-' . $entity,
                    'cipher' => Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 0,
                ];
            }
        }

        DB::table('permissions')->insert($permissions);
    }
}
