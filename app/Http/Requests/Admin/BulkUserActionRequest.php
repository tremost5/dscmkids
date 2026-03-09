<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUserActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasPermission('users.manage');
    }

    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'action' => ['required', Rule::in([
                'activate',
                'deactivate',
                'promote_admin',
                'make_editor',
                'make_student',
                'delete',
            ])],
        ];
    }
}
