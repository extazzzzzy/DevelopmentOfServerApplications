<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\LoginResource;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
        ];
    }

    public function getLoginResource(): LoginResource
    {
        return new LoginResource($this->validated());
    }
}
