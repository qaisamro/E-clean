<?php

namespace App\Http\Requests\User;

use App\Enums\Roles;
use App\Support\PermissionRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        // Super admin may edit any non-super-admin; a super admin can never edit another super admin.
        if (! auth()->user()?->isSuperAdmin()) {
            return false;
        }
        return $user && $user->role !== Roles::SUPER_ADMIN;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'phone' => ['required', 'numeric', 'regex:/^01[0125]{1}[0-9]{8}$/', 'unique:users,phone,' . $this->route('user')?->id],
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
            'role' => 'الدور',
            'permissions' => 'الصلاحيات',
        ];
    }
}
