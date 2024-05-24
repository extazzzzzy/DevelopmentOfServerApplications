<?php

namespace App\Http\Requests;

use App\DTO\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
            'birthday' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function getUserResource(): UserDTO
    {
        return new UserDTO([
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
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

            'birthday.required' => 'Дата рождения обязательня для заполнения.',
            'birthday.date' => 'Дата рождения должна быть действительной датой.',
            'birthday.date_format' => 'Дата рождения должна быть в формате ГГГГ-ММ-ДД.',
        ];
    }
}
