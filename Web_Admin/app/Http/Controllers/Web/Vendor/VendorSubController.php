<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSub;
use App\Support\VendorSubPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

/**
 * Child ("sub") vendor accounts management.
 *
 * Only the store OWNER (vendor_admin) may create/manage them; a sub account
 * can never manage other sub accounts. The sub shares the same store scope
 * (VendorAccount::vendorFor), but every capability is granted per-user by
 * the owner, permission by permission — see VendorSubPermissions for the
 * exact catalog.
 */
class VendorSubController extends Controller
{
    /** The parent store this owner manages, or 403. */
    private function parentVendor(): Vendor
    {
        abort_unless(auth()->user()?->hasRole('vendor_admin'), 403, 'هذه الصفحة لمالك المتجر فقط.');
        $vendor = auth()->user()?->vendor;
        abort_unless($vendor, 403);
        return $vendor;
    }

    /** The sub must belong to the signed-in owner's store. */
    private function authorizeSub(VendorSub $sub): void
    {
        $vendor = $this->parentVendor();
        abort_unless((int) $sub->vendor_id === (int) $vendor->id, 404);
    }

    public function index(Request $request)
    {
        $vendor = $this->parentVendor();
        $subs = VendorSub::with('user')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        return view('vendor.subs.index', compact('vendor', 'subs'));
    }

    public function create(Request $request)
    {
        $vendor = $this->parentVendor();
        return view('vendor.subs.create', compact('vendor'));
    }

    public function store(Request $request)
    {
        $vendor = $this->parentVendor();
        $validated = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:190|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $sub = DB::transaction(function () use ($validated, $vendor) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);
            $user->assignRole('vendor_sub');
            return VendorSub::create([
                'vendor_id' => $vendor->id,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('vendor.subs.show', $sub->id)
            ->with('success', 'تم إنشاء الحساب الفرعي — الآن حدّد الصلاحيات التي يراها.');
    }

    public function edit(Request $request, VendorSub $sub)
    {
        $this->authorizeSub($sub);
        $sub->load('user');
        return view('vendor.subs.edit', compact('sub'));
    }

    public function update(Request $request, VendorSub $sub)
    {
        $this->authorizeSub($sub);
        $validated = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'email' => ['nullable', 'email', 'max:190', Rule::unique('users', 'email')->ignore($sub->user_id)],
            'mobile' => ['required', 'string', 'max:20', Rule::unique('users', 'mobile')->ignore($sub->user_id)],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($validated, $sub) {
            $user = $sub->user;
            $data = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'mobile' => $validated['mobile'],
            ];
            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
                $user->tokens()->delete();
            }
            $user->update($data);
        });

        return redirect()->route('vendor.subs.index')
            ->with('success', 'تم تحديث الحساب الفرعي بنجاح');
    }

    public function toggleStatus(Request $request, VendorSub $sub)
    {
        $this->authorizeSub($sub);
        $user = $sub->user;
        $user->update(['is_active' => !$user->is_active]);
        if (!$user->is_active) {
            // Sign out the child account from every device immediately.
            $user->tokens()->delete();
        }
        return back()->with('success', $user->is_active ? 'تم تفعيل الحساب الفرعي' : 'تم تعطيل الحساب الفرعي');
    }

    public function destroy(Request $request, VendorSub $sub)
    {
        $this->authorizeSub($sub);
        DB::transaction(function () use ($sub) {
            $user = $sub->user;
            $sub->delete();
            $user?->tokens()->delete();
            $user?->delete();
        });

        return redirect()->route('vendor.subs.index')
            ->with('success', 'تم حذف الحساب الفرعي');
    }

    public function show(Request $request, VendorSub $sub)
    {
        $this->authorizeSub($sub);
        $sub->load('user');
        $permissions = $sub->user->getPermissionNames()->toArray();
        $groups = VendorSubPermissions::groups();

        return view('vendor.subs.show', compact('sub', 'permissions', 'groups'));
    }

    /**
     * Persists the owner's chosen capabilities for this child account.
     * Only names present in the catalog are accepted — nothing else may
     * ever be attached, no matter what the form posts.
     */
    public function setPermission(Request $request, VendorSub $sub)
    {
        $this->authorizeSub($sub);
        $user = $sub->user;

        $offered = (array) $request->input('permissions', []);
        $allowed = array_flip(VendorSubPermissions::all());

        foreach ($offered as $name) {
            if (!is_string($name) || $name === '' || !isset($allowed[$name])) {
                continue;
            }

            // PHP rewrites dots in field names to underscores, so the radio
            // belonging to "pos.index" arrives as "pos_index".
            $field = str_replace('.', '_', $name);
            $answer = $request->input($field);

            $permission = Permission::findOrCreate($name, 'web');

            if (is_string($answer) && $answer !== '') {
                if (!$user->checkPermissionTo($permission)) {
                    $user->givePermissionTo($permission);
                }
                continue;
            }

            if ($user->checkPermissionTo($name)) {
                $user->revokePermissionTo($permission);
            }
        }

        return back()->with('success', 'تم حفظ الصلاحيات بنجاح');
    }
}
