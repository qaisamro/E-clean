<?php

namespace App\Http\Controllers\Web\Root;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        // Supervisors are now created with the root role (full Super Admin
        // access, no permission management). Show both legacy admins and root-
        // role supervisors so the list stays complete.
        $admins = (new UserRepository())->query()->whereHas('roles', function($role){
            $role->whereRaw('LOWER(name) IN (?, ?)', ['admin', 'root']);
        })->get();

        return view('root.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('root.admin.create');
    }

    public function toggleStatusUpdate(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'تم تحديث حالة المدير بنجاح.');
    }

    public function store(UserRequest $request)
    {
       $user = (new UserRepository())->create([
        "first_name" => $request->first_name,
        "last_name" =>  $request->last_name,
        "email" =>  $request->email,
        "gender" =>  $request->gender,
        "mobile" =>  $request->mobile,
        "password" => Hash::make($request->password),
        // Newly created admins can sign in straight away; the status switch
        // on the list page is used to disable them later.
        "is_active" => true
       ]);

       $user->assignRole($this->resolveRootRole());

       // The "root" permission may not exist on installs that never had it.
       // Creating it when missing keeps this request from failing with a 500
       // after the account has already been created.
       $rootPermission = \Spatie\Permission\Models\Permission::findOrCreate('root', 'web');
       if (!$user->hasPermissionTo($rootPermission)) {
           $user->givePermissionTo($rootPermission);
       }

       return redirect()->route('admin.index')->with('success', 'تم إنشاء المدير بنجاح.');
    }

    /**
     * The admin role is spelled "admin" on some installs and "Admin" on
     * others. Resolve it case-insensitively so both keep working.
     */
    /**
     * Supervisors take the exact same role as the Super Admin (root), so they
     * inherit full access automatically without per-permission setup.
     */
    protected function resolveRootRole(): \Spatie\Permission\Models\Role
    {
        return \Spatie\Permission\Models\Role::whereRaw('LOWER(name) = ?', ['root'])
            ->where('guard_name', 'web')
            ->first()
            ?? \Spatie\Permission\Models\Role::findOrCreate('root', 'web');
    }

    public function destroy(User $user)
    {
        // Never let a Super Admin delete their own account from this screen.
        abort_if(auth()->id() === $user->id, 403, 'لا يمكن حذف حسابك الحالي.');

        // A root account is the last line of defence for the console.
        abort_if($user->hasRole('root'), 403, 'لا يمكن حذف حساب المدير principal.');

        // Revoke any API tokens without relying on Passport relationships.
        if (\Illuminate\Support\Facades\Schema::hasTable('oauth_access_tokens')) {
            \Illuminate\Support\Facades\DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->update(['revoked' => true]);
        }

        $user->delete();

        return back()->with('success', 'تم حذف المدير بنجاح.');
    }

    public function edit(User $user)
    {
        return view('root.admin.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        (new UserRepository())->update($user, [
            "first_name" => $request->first_name,
            "last_name" =>  $request->last_name,
            "email" =>  $request->email,
            "gender" =>  $request->gender,
            "mobile" =>  $request->mobile,
            "password" => Hash::make($request->password)
        ]);

        return redirect()->route('admin.index')->with('success', 'تم تحديث المعلومات بنجاح.');
    }

    public function show(User $user)
    {
        // Supervisors with the root role inherit full access automatically,
        // so there is nothing to configure here.
        if ($user->hasRole('root')) {
            return redirect()->route('admin.index')
                ->with('success', 'هذا المشرف يمتلك صلاحيات Super Admin (root) بالكامل — لا حاجة لتعيين صلاحيات.');
        }

        $permissions = $user->getPermissionNames()->toArray();
        return view('root.admin.show', compact('user', 'permissions'));
    }

    public function setPermission(Request $request, User $user)
    {
        // Only the Super Admin may rewrite another account's permissions.
        abort_unless(auth()->user()?->hasRole('root'), 403);

        // Every <x-permission-input> also posts its dotted permission name
        // once as a hidden field, so this list is the single source of truth
        // for what the form offers (no guessing from the field names).
        $offered = (array) $request->input('permissions', []);

        foreach ($offered as $name) {
            if (! is_string($name) || $name === '') {
                continue;
            }

            // PHP rewrites dots in field names to underscores, so the radio
            // belonging to "service.index" arrives as "service_index".
            $field = str_replace('.', '_', $name);
            $answer = $request->input($field);

            if (is_string($answer) && $answer !== '') {
                // spatie throws PermissionDoesNotExist for unknown names, so
                // make sure the row exists before attaching it to the user.
                $permission = \Spatie\Permission\Models\Permission::findOrCreate($name, 'web');

                if (! $user->checkPermissionTo($permission)) {
                    $user->givePermissionTo($permission);
                }

                continue;
            }

            // "No" was picked (or the field is missing): silently skip names
            // that were never stored instead of failing the whole request.
            if ($user->checkPermissionTo($name)) {
                $user->revokePermissionTo($name);
            }
        }

        return back()->with('success', 'تم إعداد الصلاحيات بنجاح.');
    }
}
