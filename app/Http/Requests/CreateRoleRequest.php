<?php

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
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
            'name' => 'string|required|unique:roles|max:255|min:5',
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
}
