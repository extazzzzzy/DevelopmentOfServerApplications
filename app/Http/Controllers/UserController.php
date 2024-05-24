<?php

namespace App\Http\Controllers;

use App\DTO\UserCollectionDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserAndRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getCollectionUsers()
    {
        $users = User::all('username');
        $userCollectionDTO = new UserCollectionDTO($users, $users->count());
        return response()->json($userCollectionDTO);
    }

    public function createUser(CreateUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $userResource = $request->getUserResource();
            $user = new User([
                'username' => $userResource -> username,
                'email' => $userResource -> email,
                'password' => $userResource -> password,
                'birthday' => $userResource -> birthday,
            ]);
            $user -> save();

            DB::commit();

            return response()->json(['Пользователь успешно создан' => $user], 201);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при создании пользователя'], 500);
        }

    }

    public function getUser($user_id)
    {
        if (!(Auth::user()->roles->contains('name', 'Admin')) && $user_id != Auth::id())
        {
            return response()->json(['error' => 'У вас недостаточно прав для просмотра ролей другого пользователя!'], 404);
        }

        $user = User::find($user_id)->only(['id', 'username', 'email', 'password', 'birthday']);

        if (!$user)
        {
            return response()->json(['error' => 'Такого пользователя не существует'], 404);
        }
        return response()->json($user, 200);
    }

    public function updateUser($user_id, UpdateUserRequest $request)
    {
        DB::beginTransaction();

        try {
            if (!(Auth::user()->roles->contains('name', 'Admin')) && $user_id != Auth::id())
            {
                DB::commit();
                return response()->json(['error' => 'У вас недостаточно прав для просмотра ролей другого пользователя!'], 404);
            }

            $userResource = User::find($user_id);
            if (!$userResource)
            {
                DB::commit();
                return response()->json(['error' => 'Такого пользователя не существует'], 404);
            }
            $userResource->username = $request->getUserResource()->username;
            $userResource->email = $request->getUserResource()->email;
            $userResource->password = $request->getUserResource()->password;
            $userResource->birthday = $request->getUserResource()->birthday;

            $userResource->save();

            DB::commit();

            return response()->json([
                'message' => 'Данные пользователя успешно измененены',
                'userResource' => $userResource
            ], 200);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при изменении данных пользователя'], 500);
        }

    }

    public function deleteUserHard($user_id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($user_id);
            $userAndRoles = UserAndRole::where('user_id', $user_id)->get();
            if (!$user)
            {
                DB::commit();
                return response()->json(['error' => 'Такого пользователя не существует'], 404);
            }

            $user->forceDelete();

            foreach ($userAndRoles as $userAndRole)
            {
                $userAndRole->forceDelete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Пользователь успешно удалён(Hard)',
            ], 200);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении пользователя'], 500);
        }

    }

    public function deleteUserSoft($user_id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($user_id);
            $userAndRoles = UserAndRole::where('user_id', $user_id)->get();
            if (!$user)
            {
                DB::commit();
                return response()->json(['error' => 'Такого пользователя не существует'], 404);
            }

            $user->delete();

            foreach ($userAndRoles as $userAndRole)
            {
                $userAndRole->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Пользователь успешно удалён(Soft)',
            ], 200);
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при удалении пользователя'], 500);
        }

    }

    public function restoreSoftDeletedUser($user_id)
    {
        DB::beginTransaction();
        try {
            $user = User::withTrashed()->find($user_id);
            $userAndRoles = UserAndRole::withTrashed()->where('user_id', $user_id)->get();
            if ($user && $user->trashed())
            {
                $user->restore();

                foreach ($userAndRoles as $userAndRole)
                {
                    $userAndRole->restore();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Пользователь успешно восстановлен',
                ], 200);
            }
            else
            {
                DB::commit();

                return response()->json([
                    'message' => 'Пользователь не найден или не был удален',
                ], 404);
            }
        }

        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при восстановлении пользователя'], 500);
        }

    }
}
