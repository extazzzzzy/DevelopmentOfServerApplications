<?php

namespace App\Http\Controllers;

use App\DTO\UserCollectionDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserAndRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $userResource = $request->getUserResource();
        $user = new User([
            'username' => $userResource -> username,
            'email' => $userResource -> email,
            'password' => $userResource -> password,
            'birthday' => $userResource -> birthday,
        ]);
        $user -> save();
        return response()->json(['Пользователь успешно создан' => $user], 201);
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
        if (!(Auth::user()->roles->contains('name', 'Admin')) && $user_id != Auth::id())
        {
            return response()->json(['error' => 'У вас недостаточно прав для просмотра ролей другого пользователя!'], 404);
        }

        $userResource = User::find($user_id);
        if (!$userResource)
        {
            return response()->json(['error' => 'Такого пользователя не существует'], 404);
        }
        $userResource->username = $request->getUserResource()->username;
        $userResource->email = $request->getUserResource()->email;
        $userResource->password = $request->getUserResource()->password;
        $userResource->birthday = $request->getUserResource()->birthday;

        $userResource->save();

        return response()->json([
            'message' => 'Данные пользователя успешно измененены',
            'userResource' => $userResource
        ], 200);
    }

    public function deleteUserHard($user_id)
    {
        $user = User::find($user_id);
        $userAndRoles = UserAndRole::where('user_id', $user_id)->get();
        if (!$user)
        {
            return response()->json(['error' => 'Такого пользователя не существует'], 404);
        }

        $user->forceDelete();

        foreach ($userAndRoles as $userAndRole)
        {
            $userAndRole->forceDelete();
        }

        return response()->json([
            'message' => 'Пользователь успешно удалён(Hard)',
        ], 200);
    }

    public function deleteUserSoft($user_id)
    {
        $user = User::find($user_id);
        $userAndRoles = UserAndRole::where('user_id', $user_id)->get();
        if (!$user)
        {
            return response()->json(['error' => 'Такого пользователя не существует'], 404);
        }

        $user->delete();

        foreach ($userAndRoles as $userAndRole)
        {
            $userAndRole->delete();
        }

        return response()->json([
            'message' => 'Пользователь успешно удалён(Soft)',
        ], 200);
    }

    public function restoreSoftDeletedUser($user_id)
    {
        $user = User::withTrashed()->find($user_id);
        $userAndRoles = UserAndRole::withTrashed()->where('user_id', $user_id)->get();
        if ($user && $user->trashed())
        {
            $user->restore();

            foreach ($userAndRoles as $userAndRole)
            {
                $userAndRole->restore();
            }

            return response()->json([
                'message' => 'Пользователь успешно восстановлен',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден или не был удален',
            ], 404);
        }
    }
}
