<?php

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
        $roleId = $this->route('id');
        return [
            'name' => 'required|unique:roles,name,' . $roleId,
            'description' => 'string|max:255',
        ];
    }

    public function getRoleResource(): RoleDTO
    {
        return new RoleDTO([
            'name' => $this->input('name'),
            'description' => $this->input('description'),
        ]);
    }

    public function messages()
    {
        return [
            'name.required' => 'Название роли обязательно для заполнения.',
            'name.string' => 'Название роли должно быть строкой.',
            'name.unique' => 'Роль с названием уже существует именем уже существует.',
        ];
    }
}
