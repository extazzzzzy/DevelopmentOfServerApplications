<?php

namespace App\Http\Requests;

use App\DTO\UserAndRoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserAndRoleRequest extends FormRequest
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
            'user_id' => 'integer|required',
            'role_id' => 'integer|required',
        ];
    }

    public function getUserAndRoleResource(): UserAndRoleDTO
    {
        return new UserAndRoleDTO([
            'user_id' => $this->input('user_id'),
            'role_id' => $this->input('role_id'),
        ]);
    }

    public function messages()
    {
        return [
            'user_id.required' => 'Ссылка на пользователя обязательна для заполнения.',
            'user_id.integer' => 'Ссылка должна быть целым числом.',
            'role_id.required' => 'Ссылка на роль обязательна для заполнения.',
            'role_id.integer' => 'Ссылка должна быть целым числом.',
        ];
    }
}
