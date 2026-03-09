<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasPermission('users.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'class_group' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(array_keys(config('admin_permissions.roles', [])))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
