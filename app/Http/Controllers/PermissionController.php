<?php

namespace App\Http\Controllers;

use App\DTO\PermissionCollectionDTO;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function getCollectionPermissions()
    {
        $permissions = Permission::all();
        $permissionCollectionDTO = new PermissionCollectionDTO($permissions, $permissions->count());
        return response()->json($permissionCollectionDTO);
    }

    public function createPermission(CreatePermissionRequest $request)
    {
        $permissionResource = $request->getPermissionResource();
        $permission = new Permission([
            'name' => $permissionResource -> name,
            'description' => $permissionResource -> description,
        ]);
        $permission -> save();
        return response()->json(['Разрешение успешно создано' => $permission], 201);
    }

    public function getPermission($id)
    {
        $permission = Permission::find($id);

        if (!$permission)
        {
            return response()->json(['error' => 'Такого разрешения не существует'], 404);
        }
        return response()->json($permission, 200);
    }

    public function updatePermission($id, UpdatePermissionRequest $request)
    {
        $permissionResource = Permission::find($id);
        if (!$permissionResource)
        {
            return response()->json(['error' => 'Такого разрешения не существует'], 404);
        }
        $permissionResource->name = $request->getPermissionResource()->name;
        $permissionResource->description = $request->getPermissionResource()->description;

        $permissionResource->save();

        return response()->json([
            'message' => 'Разрешение успешно изменено',
            'permissionResource' => $permissionResource
        ], 200);
    }

    public function deletePermissionHard($id)
    {
        $permission = Permission::find($id);
        if (!$permission)
        {
            return response()->json(['error' => 'Такого разрешения не существует'], 404);
        }
        $permission->forceDelete();

        return response()->json([
            'message' => 'Разрешение успешно удалено(Hard)',
        ], 200);
    }

    public function deletePermissionSoft($id)
    {
        $permission = Permission::find($id);
        if (!$permission)
        {
            return response()->json(['error' => 'Такого разрешения не существует'], 404);
        }
        $permission->delete();

        return response()->json([
            'message' => 'Разрешение успешно удалено(Soft)',
        ], 200);
    }

    public function restoreSoftDeletedPermission($id)
    {
        $permission = Permission::withTrashed()->find($id);

        if ($permission && $permission->trashed())
        {
            $permission->restore();
            return response()->json([
                'message' => 'Разрешение успешно восстановлено',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Разрешение не найдено или не было удалено',
            ], 404);
        }
    }
}
