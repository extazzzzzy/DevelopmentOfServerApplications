<?php

namespace App\Http\Requests;

use App\DTO\RoleAndPermissionDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateRoleAndPermissionRequest extends FormRequest
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
            'role_id' => 'integer',
            'permission_id' => 'integer',
        ];
    }

    public function getRoleAndPermissionResource(): RoleAndPermissionDTO
    {
        return new RoleAndPermissionDTO([
            'role_id' => $this->input('role_id'),
            'permission_id' => $this->input('permission_id'),
        ]);
    }

    public function messages()
    {
        return [
            'role_id.integer' => 'Ссылка должна быть целым числом.',
            'permission_id.required' => 'Ссылка на роль обязательна для заполнения.',
            'permission_id.integer' => 'Ссылка должна быть целым числом.',
        ];
    }
}
