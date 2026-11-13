<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Enums\UserStatus;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Setting;
use App\Models\User;
use App\Support\PermissionRegistry;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Show all users except super admins (current user + system accounts)
     */
    public function index()
    {
        return view('user.index', [
            'users' => User::whereNot('role', Roles::SUPER_ADMIN->value)
                ->paginate(config('app.default_pagination')),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('user.create', [
            'groups' => PermissionRegistry::groups(),
        ]);
    }

    /**
     * Handle store request
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            if (is_null($validated['password'] ?? null)) {
                $defaultPassword = Setting::firstWhere('key', 'default-password');
                if (! $defaultPassword?->value) {
                    toast('تعذر إنشاء مستخدم جديد لأن كلمة المرور الافتراضية غير محددة في الإعدادات', 'error');
                    return to_route('users.create');
                }
                $validated['password'] = $defaultPassword->value;
            }
            $validated['permissions'] = $validated['permissions'] ?? [];
            User::create($validated);
        } catch (\Throwable $throwable) {
            toast('حدث خطأ أثناء إنشاء المستخدم', 'error');
            return to_route('users.index');
        }
        toast('تم إنشاء المستخدم بنجاح', 'success');
        return to_route('users.index');
    }

    /**
     * Show specified user
     */
    public function show(User $user)
    {
        abort_if($user->role === Roles::SUPER_ADMIN, 403);
        return view('user.show', [
            'user' => $user,
            'groups' => PermissionRegistry::groups(),
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        abort_if($user->role === Roles::SUPER_ADMIN, 403);
        return view('user.edit', [
            'user' => $user,
            'groups' => PermissionRegistry::groups(),
        ]);
    }

    /**
     * Handle update request
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $data = $request->validated();
            $data['permissions'] = $data['permissions'] ?? [];
            $user->fill($data);
            if ($user->isDirty()) {
                $user->save();
            }
        } catch (\Throwable $throwable) {
            toast('حدث خطأ أثناء تحديث بيانات المستخدم', 'error');
            return to_route('users.index');
        }
        toast('تم تحديث بيانات المستخدم بنجاح', 'success');
        return to_route('users.index');
    }

    /**
     * Handle delete request
     */
    public function destroy(User $user)
    {
        $this->checkActions($user);
        try {
            $user->delete();
        } catch (\Throwable $throwable) {
            toast('حدث خطأ أثناء حذف المستخدم', 'error');
            return to_route('users.index');
        }
        toast('تم حذف المستخدم بنجاح', 'success');
        return to_route('users.index');
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $this->checkActions($user);
        try {
            $defaultPassword = Setting::firstWhere('key', 'default-password');
            if (! $defaultPassword?->value) {
                toast('تعذر استعادة كلمة المرور لأن كلمة المرور الافتراضية غير محددة في الإعدادات', 'error');
                return to_route('users.index');
            }
            $user->update(['password' => $defaultPassword->value]);
        } catch (\Throwable $throwable) {
            toast('حدث خطأ أثناء استعادة كلمة مرور المستخدم', 'error');
            return to_route('users.index');
        }
        toast('تم استعادة كلمة مرور المستخدم بنجاح', 'success');
        return to_route('users.index');
    }

    /**
     * Toggle user suspension
     */
    public function toggleSuspension(User $user)
    {
        $this->checkActions($user);
        try {
            $status = $user->status === UserStatus::ACTIVE ? UserStatus::SUSPENDED : UserStatus::ACTIVE;
            $user->update(['status' => $status]);
        } catch (\Throwable $throwable) {
            toast('حدث خطأ أثناء تغيير حالة المستخدم', 'error');
            return to_route('users.index');
        }
        toast('تم تغيير حالة المستخدم بنجاح', 'success');
        return to_route('users.index');
    }

    /**
     * Never allow the session super admin (or an administrator) to manage a super admin.
     */
    private function checkActions(User $user): void
    {
        abort_if($user->role === Roles::SUPER_ADMIN, 401);
    }
}
