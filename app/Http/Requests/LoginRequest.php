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
            'username' => 'required|string|regex:/^(?=[A-Z])(?=.*[a-zA-Z])(?!.*\d)(?!.*\p{Cyrillic}).{7,}$/',
            'password' => 'required|string|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\p{Cyrillic}).{8,}$/',
        ];
    }

    public function getLoginResource(): LoginResource
    {
        return new LoginResource($this->validated());
    }
}
