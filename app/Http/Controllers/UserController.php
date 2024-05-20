<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $this->clearExpiredTokens();
        $loginResource = $request->getLoginResource();
        if (Auth::attempt(['username' => $loginResource->username, 'password' => $loginResource->password]))
        {
            if (Auth::user() -> tokens() -> count() < env('MAX_ACTIVE_TOKENS'))
            {
                $token = Auth::user()->createToken('token')->plainTextToken;
                return response()->json(['token' => $token], 200);
            }
            else
            {
                return response()->json(['message' => 'Авторизованно максимальное количество пользователей']);
            }

        }
        return response()->json(['error' => 'Неверный логин или пароль']);
    }

    public function register(RegisterRequest $request)
    {
        $this->clearExpiredTokens();
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

    public function me()
    {
        $this->clearExpiredTokens();
        $user = Auth::user();
        return response()->json(new UserDTO($user));
    }

    public function out()
    {
        $this->clearExpiredTokens();
        Auth::user()->currentAccessToken() -> delete();
        return response()->json(['message' => 'Вы успешно вышли из системы'], 200);
    }

    public function out_all()
    {
        $this->clearExpiredTokens();
        Auth::user() -> tokens() -> delete();
        return response()->json(['message' => 'Всё токены пользователя уничтожены'], 200);
    }

    public function tokens()
    {
        $this->clearExpiredTokens();
        return response()->json(['tokens' => Auth::user() -> tokens -> pluck('token')]);
    }

    public function clearExpiredTokens(): void
    {
        $expirationTime = now()->subMinutes(env('EXPIRATION_TOKEN'));
        \DB::table('personal_access_tokens')
            ->where('created_at', '<=', $expirationTime)
            ->delete();
    }
}
