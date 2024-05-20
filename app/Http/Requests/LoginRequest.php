<?php

namespace App\Http\Requests;

use App\DTO\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
        ];
    }

    public function getLoginResource(): LoginDTO
    {
        return new LoginDTO([
            'username' => $this->input('username'),
            'password' => $this->input('password'),
        ]);;
    }

    public function messages()
    {
        return [
            'username.required' => 'Имя пользователя обязательно для заполнения.',
            'username.string' => 'Имя пользователя должно быть строкой.',
            'username.regex' => 'Имя пользователя должно начинаться с заглавной буквы и содержать не менее 7 символов.',
            'username.unique' => 'Пользователь с таким именем уже существует.',

            'email.required' => 'Email обязателен для заполнения.',
            'email.string' => 'Email должен быть строкой.',
            'email.email' => 'Email должен быть в формате: "user@mail.ru".',
            'email.unique' => 'Пользователь с таким Email уже существует.',

            'password.required' => 'Пароль обязателен для заполнения.',
            'password.string' => 'Пароль должен быть строкой.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.regex' => 'Пароль должен содержать хотя бы одну строчную букву, одну заглавную букву, одну цифру и один специальный символ.',

            'c_password.required' => 'Подтверждение пароля обязательно должно быть заполнено.',
            'c_password.string' => 'Подтверждение пароля должно быть строкой.',
            'c_password.same' => 'Пароль и подтверждение пароля должны совпадать.',

            'birthday.required' => 'Дата рождения обязательня для заполнения.',
            'birthday.date' => 'Дата рождения должна быть действительной датой.',
            'birthday.date_format' => 'Дата рождения должна быть в формате ГГГГ-ММ-ДД.',
        ];
    }
}
