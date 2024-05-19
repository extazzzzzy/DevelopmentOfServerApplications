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
        $loginResource = $request->getLoginResource();
        if (Auth::attempt(['username' => $loginResource->username, 'password' => $loginResource->password]))
        {
            $token = Auth::user()->createToken('token')->plainTextToken;
            return response()->json(['token' => $token], 200);
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
        $user = Auth::user();
        return response()->json(new UserResource($user), 200);
    }

    public function out1()
    {
        Auth::user()->currentAccessToken() -> delete();
        return response()->json(['message' => 'Вы успешно вышли из системы'], 200);
    }
}
