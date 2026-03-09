<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasPermission('users.manage');
    }

    public function rules(): array
    {
        /** @var User $target */
        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:120'],
            'class_group' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($target?->id)],
            'role' => ['required', Rule::in(array_keys(config('admin_permissions.roles', [])))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
