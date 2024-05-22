<?php

namespace App\Http\Controllers;

use App\DTO\RoleCollectionDTO;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Models\RoleAndPermission;
use App\Models\UserAndRole;
use Illuminate\Http\Request;

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
        $roleResource = $request->getRoleResource();
        $role = new Role([
            'name' => $roleResource -> name,
            'description' => $roleResource -> description,
        ]);
        $role -> save();
        return response()->json(['Роль успешно создана' => $role], 201);
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
        $roleResource = Role::find($id);
        if (!$roleResource)
        {
            return response()->json(['error' => 'Такой роли не существует'], 404);
        }
        $roleResource->name = $request->getRoleResource()->name;
        $roleResource->description = $request->getRoleResource()->description;

        $roleResource->save();

        return response()->json([
            'message' => 'Роль успешно изменена',
            'roleResource' => $roleResource
        ], 200);
    }

    public function deleteRoleHard($id)
    {
        $role = Role::find($id);
        $roleAndPermissions = RoleAndPermission::where('role_id', $id)->get();
        $userAndRoles = UserAndRole::where('role_id', $id)->get();
        if (!$role)
        {
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

        return response()->json([
            'message' => 'Роль успешно удалена(Hard)',
        ], 200);
    }

    public function deleteRoleSoft($id)
    {
        $role = Role::find($id);
        $roleAndPermissions = RoleAndPermission::where('role_id', $id)->get();
        $userAndRoles = UserAndRole::where('role_id', $id)->get();
        if (!$role)
        {
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


        return response()->json([
            'message' => 'Роль успешно удалена(Soft)',
        ], 200);
    }

    public function restoreSoftDeletedRole($id)
    {
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

            return response()->json([
                'message' => 'Роль успешно восстановлена',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Роль не найдена или не была удалена',
            ], 404);
        }
    }
}
