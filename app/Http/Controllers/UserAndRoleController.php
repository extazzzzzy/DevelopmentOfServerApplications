<?php

namespace App\Http\Controllers;

use App\DTO\UserAndRoleCollectionDTO;
use App\DTO\UserAndRoleDTO;
use App\Http\Requests\CreateUserAndRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAndRole;
use Illuminate\Http\Request;

class UserAndRoleController extends Controller
{
    public function getCollectionUsersAndRoles()
    {
        $usersAndRoles = UserAndRole::all();
        return response()->json($usersAndRoles);
    }

    public function createUserAndRole($user_id, CreateUserAndRoleRequest $request)
    {
        $userAndRoleResource = $request->getUserAndRoleResource();

        $userAndRole = UserAndRole::where('user_id', $user_id)->where('role_id', $userAndRoleResource->role_id)->get();
        if(count($userAndRole) != 0)
        {
            return response()->json(['error' => 'У пользователя уже имеется данная роль'], 404);
        }

        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['error' => 'Указанный пользователь не найден'], 404);
        }

        $role = Role::find($userAndRoleResource->role_id);
        if (!$role) {
            return response()->json(['error' => 'Указанная роль не найдена'], 404);
        }

        $userAndRole = new UserAndRole([
            'user_id' => intval($user_id),
            'role_id' => intval($userAndRoleResource->role_id),
        ]);
        $userAndRole->save();
        return response()->json(['Роль успешно привязана к пользователю' => $userAndRole], 201);
    }

    public function getCollectionUserAndRoles($user_id)
    {
        $user = User::with('roles')->find($user_id);
        if (!$user) {
            return response()->json(['error' => 'Указанный пользователь не найден'], 404);
        }

        $userRoleCollectionDTO = new UserAndRoleCollectionDTO($user);
        return response()->json($userRoleCollectionDTO);
    }

    public function deleteUserAndRoleHard($user_id, $role_id)
    {
        $userAndRole = UserAndRole::where('user_id', $user_id)->where('role_id', $role_id)->first();
        if (!$userAndRole)
        {
            return response()->json(['error' => 'Запись с указанными user_id и role_id не найдена'], 404);
        }
        $userAndRole->forceDelete();
        return response()->json(['message' => 'Роль у пользователя успешно удалена(Hard)'], 200);
    }

    public function deleteUserAndRoleSoft($user_id, $role_id)
    {
        $userAndRole = UserAndRole::where('user_id', $user_id)->where('role_id', $role_id)->first();
        if (!$userAndRole)
        {
            return response()->json(['error' => 'Запись с указанными user_id и role_id не найдена'], 404);
        }
        $userAndRole->delete();
        return response()->json(['message' => 'Роль у пользователя успешно удалена(Soft)'], 200);
    }

    public function restoreSoftDeletedUserAndRole($user_id, $role_id)
    {
        $userAndRole = UserAndRole::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id)->first();

        if ($userAndRole && $userAndRole->trashed())
        {
            $userAndRole->restore();
            return response()->json([
                'message' => 'Роль пользователя успешно восстановлена',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Роль пользователя не найдена или не была удалена',
            ], 404);
        }
    }
}
