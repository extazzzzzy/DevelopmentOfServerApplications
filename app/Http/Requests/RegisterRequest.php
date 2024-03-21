<?php

namespace App\Http\Requests;

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
            'username' => 'required|string|unique:users|regex:/^(?=[A-Z])(?=.*[a-zA-Z])(?!.*\d)(?!.*\p{Cyrillic}).{7,}$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\p{Cyrillic}).{8,}$/',
            'c_password' => 'required|string|same:password',
            'birthday' => 'required|date|date_format:Y-m-d',
        ];
    }
}
