<?php

namespace App\Http\Controllers;

use App\DTO\RoleCollectionDTO;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Models\RoleAndPermission;
use App\Models\UserAndRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function getCollectionRoles()
    {
        $roles = Role::all();
        $roleCollectionDTO = new RoleCollectionDTO($roles, $roles->count());
        return response()->json($roleCollectionDTO);
    }

    public function createRole(CreateRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $roleResource = $request->getRoleResource();
            $role = new Role([
                'name' => $roleResource -> name,
                'description' => $roleResource -> description,
            ]);
            $role -> save();

            DB::commit();

            return response()->json(['Роль успешно создана' => $role], 201);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при создании роли'], 500);
        }

    }

    public function getRole($id)
    {
        $role = Role::find($id);

        if (!$role)
        {
            return response()->json(['error' => 'Такой роли не существует'], 404);
        }
        return response()->json($role, 200);
    }

    public function updateRole($id, UpdateRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $roleResource = Role::find($id);
            if (!$roleResource)
            {
                DB::commit();
                return response()->json(['error' => 'Такой роли не существует'], 404);
            }
            $roleResource->name = $request->getRoleResource()->name;
            $roleResource->description = $request->getRoleResource()->description;

            $roleResource->save();

            DB::commit();

            return response()->json([
                'message' => 'Роль успешно изменена',
                'roleResource' => $roleResource
            ], 200);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при изменении роли'], 500);
        }

    }

    public function deleteRoleHard($id)
    {
        DB::beginTransaction();
        try {
            $role = Role::find($id);
            $roleAndPermissions = RoleAndPermission::where('role_id', $id)->get();
            $userAndRoles = UserAndRole::where('role_id', $id)->get();
            if (!$role)
            {
                DB::commit();
                return response()->json(['error' => 'Такой роли не существует'], 404);
            }

            $role->forceDelete();

            foreach ($roleAndPermissions as $roleAndPermission)
            {
                $roleAndPermission->forceDelete();
            }
            foreach ($userAndRoles as $userAndRole)
            {
                $userAndRole->forceDelete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Роль успешно удалена(Hard)',
            ], 200);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении роли'], 500);
        }

    }

    public function deleteRoleSoft($id)
    {
        DB::beginTransaction();
        try {
            $role = Role::find($id);
            $roleAndPermissions = RoleAndPermission::where('role_id', $id)->get();
            $userAndRoles = UserAndRole::where('role_id', $id)->get();
            if (!$role)
            {
                DB::commit();
                return response()->json(['error' => 'Такой роли не существует'], 404);
            }

            $role->delete();

            foreach ($roleAndPermissions as $roleAndPermission)
            {
                $roleAndPermission->delete();
            }
            foreach ($userAndRoles as $userAndRole)
            {
                $userAndRole->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Роль успешно удалена(Soft)',
            ], 200);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении роли'], 500);
        }

    }

    public function restoreSoftDeletedRole($id)
    {
        DB::beginTransaction();
        try {
            $role = Role::withTrashed()->find($id);
            $roleAndPermissions = RoleAndPermission::withTrashed()->where('role_id', $id)->get();
            $userAndRoles = UserAndRole::withTrashed()->where('role_id', $id)->get();
            if ($role && $role->trashed())
            {
                $role->restore();

                foreach ($roleAndPermissions as $roleAndPermission)
                {
                    $roleAndPermission->restore();
                }
                foreach ($userAndRoles as $userAndRole)
                {
                    $userAndRole->restore();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Роль успешно восстановлена',
                ], 200);
            }
            else
            {
                DB::commit();
                return response()->json([
                    'message' => 'Роль не найдена или не была удалена',
                ], 404);
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при восстановлении роли'], 500);
        }

    }
}
