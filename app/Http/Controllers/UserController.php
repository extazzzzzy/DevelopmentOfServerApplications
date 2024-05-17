<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $loginResource = $request->getLoginResource();
        // Ваш код для авторизации пользователя
    }

    public function register(RegisterRequest $request)
    {
        $registerResource = $request->getRegisterResource();
        // Ваш код для регистрации пользователя
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return new UserResource($user);
    }
}
