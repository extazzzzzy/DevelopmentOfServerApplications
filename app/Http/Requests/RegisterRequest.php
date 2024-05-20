<?php

namespace App\Http\Requests;

use App\DTO\RegisterDTO;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
            'c_password' => 'required|string|same:password',
            'birthday' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function getRegisterResource(): RegisterDTO
    {
        return new RegisterDTO([
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'c_password' => $this->input('c_password'),
            'birthday' => $this->input('birthday'),
        ]);
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
