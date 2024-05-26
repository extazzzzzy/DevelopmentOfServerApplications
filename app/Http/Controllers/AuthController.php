<?php

namespace App\Http\Controllers;
use App\DTO\ProfileUserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\TwoFactorCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $loginResource = $request->getLoginResource();
        $user = User::where('username', $loginResource->username)->first();

        $lastCode = $user->twoFactorCodes()->orderBy('created_at', 'desc')->first();
        if ($lastCode && $lastCode->created_at->diffInSeconds(now()) < 30) {
            return response()->json(['message' => 'Вы запрашиваете код слишком часто. Попробуйте позже.'], 429);
        }

        if ($user && Auth::attempt(['username' => $loginResource->username, 'password' => $loginResource->password])) {
            $this->send2FACode($user);
            return response()->json(['message' => 'Код двухфакторной авторизации отправлен на ваш email.']);
        }

        return response()->json(['error' => 'Неверный логин или пароль'], 401);
    }

    public function confirm2FACode(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'code' => 'required|numeric',
        ]);

        $user = User::where('username', $request->username)->firstOrFail();
        $code = $user->twoFactorCodes()->where('code', $request->code)->first();

        if ($code && !$code->isExpired())
        {
            $code->delete();

            if ($user->tokens()->count() < env('MAX_ACTIVE_TOKENS', 3))
            {
                $token = $user->createToken('token')->plainTextToken;
                return response()->json(['token' => $token], 200);
            }
            else
            {
                return response()->json(['message' => 'Авторизовано максимальное количество пользователей'], 429);
            }
        }

        return response()->json(['error' => 'Неверный или истекший код.'], 422);
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);

        $user = User::where('username', $request->username)->firstOrFail();

        $lastCode = $user->twoFactorCodes()->orderBy('created_at', 'desc')->first();
        if ($lastCode && $lastCode->created_at->diffInSeconds(now()) < 30) {
            return response()->json(['message' => 'Вы запрашиваете код слишком часто. Попробуйте позже.'], 429);
        }

        return $this->send2FACode($user);
    }

    private function send2FACode($user)
    {
        $user->twoFactorCodes()->delete();

        $code = TwoFactorCode::generateCode();
        $expiresAt = TwoFactorCode::generateExpiration();

        $user->twoFactorCodes()->create([
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.mail.ru';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ugrasu_auth@mail.ru';
            $mail->Password   = 'VjQsL98Uu72AYhD0VjJg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('ugrasu_auth@mail.ru');
            $mail->addAddress($user->email);

            $mail->isHTML(false);
            $mail->Subject = 'Authentication Code';
            $mail->Body = $code;

            $mail->send();
        } catch (Exception $e) {
            return response()->json(['error' => 'Произошла ошибка при отправке сообщения!'], 500);
        }
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
    {        $this->clearExpiredTokens();
        $user = Auth::user();
        return response()->json(new ProfileUserDTO($user));
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

    public function getTokens()
    {
        $this->clearExpiredTokens();
        return response()->json(['tokens' => Auth::user() -> tokens() -> pluck('token')]);
    }

    public function clearExpiredTokens(): void
    {
        $expirationTime = now()->subMinutes(env('EXPIRATION_TOKEN'));
        \DB::table('personal_access_tokens')
            ->where('created_at', '<=', $expirationTime)
            ->delete();
    }
}
