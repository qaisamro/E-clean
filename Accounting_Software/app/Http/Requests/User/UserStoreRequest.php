<?php

namespace App\Http\Requests\User;

use App\Enums\Roles;
use App\Support\PermissionRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:254'],
            'phone' => ['required', 'numeric', 'regex:/^01[0125]{1}[0-9]{8}$/', 'unique:users,phone'],
            'password' => ['nullable', Password::default(), 'confirmed'],
            'role' => ['required', Rule::in([Roles::ADMIN->value])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(PermissionRegistry::keys())],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'phone' => 'الهاتف',
            'password' => 'كلمة المرور',
            'role' => 'الدور',
            'permissions' => 'الصلاحيات',
        ];
    }
}
