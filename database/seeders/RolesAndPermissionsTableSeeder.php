<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();
        $guestRole = Role::where('name', 'Guest')->first();

        $allPermissions = Permission::all();

        $adminRole->permissions()->sync($allPermissions->pluck('id')->toArray());

        $userPermissions = Permission::whereIn('name', ['get-list-user', 'get-user', 'update-user', 'get-user_and_role'])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id')->toArray());

        $guestPermissions = Permission::where('name', 'get-list-user')->get();
        $guestRole->permissions()->sync($guestPermissions->pluck('id')->toArray());
    }
}
