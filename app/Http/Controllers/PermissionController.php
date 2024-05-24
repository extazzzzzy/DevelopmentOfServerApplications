<?php

namespace App\Http\Controllers;

use App\DTO\PermissionCollectionDTO;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Models\RoleAndPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            $permissionResource = $request->getPermissionResource();
            $permission = new Permission([
                'name' => $permissionResource -> name,
                'description' => $permissionResource -> description,
            ]);
            $permission -> save();

            DB::commit();

            return response()->json(['Разрешение успешно создано' => $permission], 201);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при создании разрешения'], 500);
        }

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
        DB::beginTransaction();
        try {
            $permissionResource = Permission::find($id);
            if (!$permissionResource)
            {
                DB::commit();
                return response()->json(['error' => 'Такого разрешения не существует'], 404);
            }
            $permissionResource->name = $request->getPermissionResource()->name;
            $permissionResource->description = $request->getPermissionResource()->description;

            $permissionResource->save();

            DB::commit();

            return response()->json([
                'message' => 'Разрешение успешно изменено',
                'permissionResource' => $permissionResource
            ], 200);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при изменении разрешения'], 500);
        }

    }

    public function deletePermissionHard($id)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::find($id);
            $roleAndPermissions = RoleAndPermission::where('permission_id', $id)->get();

            if (!$permission)
            {
                DB::commit();
                return response()->json(['error' => 'Такого разрешения не существует'], 404);
            }
            $permission->forceDelete();

            foreach ($roleAndPermissions as $roleAndPermission)
            {
                $roleAndPermission->forceDelete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Разрешение успешно удалено(Hard)',
            ], 200);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении разрешения'], 500);
        }

    }

    public function deletePermissionSoft($id)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::find($id);
            $roleAndPermissions = RoleAndPermission::where('permission_id', $id)->get();
            if (!$permission)
            {
                DB::commit();
                return response()->json(['error' => 'Такого разрешения не существует'], 404);
            }
            $permission->delete();

            foreach ($roleAndPermissions as $roleAndPermission)
            {
                $roleAndPermission->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Разрешение успешно удалено(Soft)',
            ], 200);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении разрешения'], 500);
        }

    }

    public function restoreSoftDeletedPermission($id)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::withTrashed()->find($id);
            $roleAndPermissions = RoleAndPermission::withTrashed()->where('permission_id', $id)->get();
            if ($permission && $permission->trashed())
            {
                $permission->restore();
                foreach ($roleAndPermissions as $roleAndPermission)
                {
                    $roleAndPermission->restore();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Разрешение успешно восстановлено',
                ], 200);
            }
            else
            {
                DB::commit();
                return response()->json([
                    'message' => 'Разрешение не найдено или не было удалено',
                ], 404);
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при восстановлении разрешения'], 500);
        }

    }
}
