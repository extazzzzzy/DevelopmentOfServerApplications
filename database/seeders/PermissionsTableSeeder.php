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
        // Lab 3
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

        // Lab 4
        DB::table('permissions')->insert([
            ['name' => 'get-story-role', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
            ['name' => 'get-story-permission', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
            ['name' => 'get-story-user', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
            ['name' => 'get-story-collection', 'cipher' => Str::uuid(), 'created_at' => now(), 'updated_at' => now(), 'created_by' => 0],
        ]);

        DB::table('permissions')->insert($permissions);
    }
}
