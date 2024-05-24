<?php

namespace App\Http\Requests;

use App\DTO\PermissionDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
        $permissionId = $this->route('id');
        return [
            'name' => 'required|unique:roles,name,' . $permissionId,
            'description' => 'string|max:255',
        ];
    }

    public function getPermissionResource(): PermissionDTO
    {
        return new PermissionDTO([
            'name' => $this->input('name'),
            'description' => $this->input('description'),
        ]);
    }

    public function messages()
    {
        return [
            'name.required' => 'Название разрешения обязательно для заполнения.',
            'name.string' => 'Название разрешения должно быть строкой.',
            'name.unique' => 'Разрешение с таким названием уже существует.',
        ];
    }
}
