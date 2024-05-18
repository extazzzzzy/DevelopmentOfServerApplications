<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login1(LoginRequest $request)
    {
/*        Auth::logout();
        return response()->json(['error' => 'очищено']);*/
        if (Auth::user()) {
            return response()->json(['error' => 'Один из пользователей уже авторизован на этом устройстве']);
        }
        $loginResource = $request->getLoginResource();
        if (Auth::attempt(['username' => $loginResource->username, 'password' => $loginResource->password]))
        {
            $token = Auth::user()->createToken('token')->plainTextToken;
            $cookie = cookie('user_session', $token, 60, null, null, false, true);
            return response()->json(['token' => $token], 200)->withCookie($cookie);
        }
        return response()->json(['error' => 'Неверный логин или пароль']);
    }

    public function register1(RegisterRequest $request)
    {
        if (Auth::user()) {
            return response()->json(['error' => 'Для регистрации нового пользователя необходимо выйти из аккаунта']);
        }
        $registerResource = $request->getRegisterResource();
        $user = new User([
            'username' => $registerResource->username,
            'email' => $registerResource->email,
            'password' => $registerResource->password,
            'birthday' => $registerResource->birthday,
        ]);
        $user->save();
        return response()->json(['Пользователь успешно зарегистрирован' => $user], 201);
    }

    public function me1()
    {
        if (!Auth::user()) {
            return response()->json(['error' => 'Необходимо авторизоваться']);
        }
        $user = Auth::user();
        return response()->json(new UserResource($user), 200);
    }

    public function out1(Request $request)
    {
        $user = Auth::user();
        if (!$user)
        {
            return response()->json(['error' => 'Вы не авторизованы, выйти невозможно']);
        }

        $token = $request->cookie('user_session');


        if ($token)
        {
            $user->tokens()->where('id', $token)->delete();
        }
        else
        {
            return response()->json(['error' => 'Токен не найден'], 400);
        }

        Auth::logout();

        $cookie = \Cookie::forget('user_session');

        return response()->json(['message' => 'Вы успешно вышли из системы'], 200)->cookie($cookie);
    }
}
