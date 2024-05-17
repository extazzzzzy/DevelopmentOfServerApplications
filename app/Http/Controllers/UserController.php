<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function login1(LoginRequest $request)
    {
        $loginResource = $request->getLoginResource();
        // Ваш код для авторизации пользователя
    }

    public function register1(RegisterRequest $request)
    {
        $registerResource = $request->getRegisterResource();

        $user = new User([
            'username' => $registerResource->username,
            'email' => $registerResource->email,
            'password' => bcrypt($registerResource->password),
            'birthday' => $registerResource->birthday,
        ]);
        $user->save();
        return response()->json(['Пользователь успешно зарегистрирован' => $user], 201);
    }

    public function me1(Request $request)
    {
        $user = $request->user();
        return new UserResource($user);
    }
}
