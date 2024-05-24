<?php

namespace App\Http\Controllers;

use App\DTO\RoleAndPermissionCollectionDTO;
use App\Http\Requests\CreateRoleAndPermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleAndPermission;
use Illuminate\Http\Request;

class RoleAndPermissionController extends Controller
{
    public function getCollectionRolesAndPermissions()
    {
        $rolesAndPermissions = RoleAndPermission::all();
        return response()->json($rolesAndPermissions);
    }

    public function createRoleAndPermission($role_id, CreateRoleAndPermissionRequest $request)
    {
        $roleAndPermissionResource = $request->getRoleAndPermissionResource();

        $roleAndPermission = RoleAndPermission::where('role_id', $role_id)->where('permission_id', $roleAndPermissionResource->permission_id)->get();
        if(count($roleAndPermission) != 0)
        {
            return response()->json(['error' => 'У роли уже имеется данное разрешение'], 404);
        }

        $role = Role::find($role_id);
        if (!$role) {
            return response()->json(['error' => 'Указанная роль не найдена'], 404);
        }

        $permission = Permission::find($roleAndPermissionResource->permission_id);
        if (!$permission) {
            return response()->json(['error' => 'Указанное разрешение не найдена'], 404);
        }

        $roleAndPermission = new RoleAndPermission([
            'role_id' => intval($role_id),
            'permission_id' => intval($roleAndPermissionResource->permission_id),
        ]);
        $roleAndPermission->save();
        return response()->json(['Разрешение успешно выдано указанной роли' => $roleAndPermission], 201);
    }

    public function getCollectionRoleAndPermissions($role_id)
    {
        $role = Role::with('permissions')->find($role_id);
        if (!$role) {
            return response()->json(['error' => 'Указанная роль не найдена'], 404);
        }

        $rolePermissionsCollectionDTO = new RoleAndPermissionCollectionDTO($role);
        return response()->json($rolePermissionsCollectionDTO);
    }

    public function deleteRoleAndPermissionHard($role_id, $permission_id)
    {
        $roleAndPermission = RoleAndPermission::where('role_id', $role_id)->where('permission_id', $permission_id)->first();
        if (!$roleAndPermission)
        {
            return response()->json(['error' => 'Запись с указанными role_id и permission_id не найдена'], 404);
        }
        $roleAndPermission->forceDelete();
        return response()->json(['message' => 'Разрешение у роли успешно удалено(Hard)'], 200);
    }

    public function deleteRoleAndPermissionSoft($role_id, $permission_id)
    {
        $roleAndPermission = RoleAndPermission::where('role_id', $role_id)->where('permission_id', $permission_id)->first();
        if (!$roleAndPermission)
        {
            return response()->json(['error' => 'Запись с указанными role_id и permission_id не найдена'], 404);
        }
        $roleAndPermission->delete();
        return response()->json(['message' => 'Разрешение у роли успешно удалено(Soft)'], 200);
    }

    public function restoreSoftDeletedRoleAndPermission($role_id, $permission_id)
    {
        $roleAndPermission = RoleAndPermission::withTrashed()->where('role_id', $role_id)->where('permission_id', $permission_id)->first();

        if ($roleAndPermission && $roleAndPermission->trashed())
        {
            $roleAndPermission->restore();
            return response()->json([
                'message' => 'Разрешение роли успешно восстановлено',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Разрешение у роли не найдено или не было удалено',
            ], 404);
        }
    }
}
