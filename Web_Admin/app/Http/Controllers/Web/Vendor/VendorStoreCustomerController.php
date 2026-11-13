<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorStoreCustomerService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VendorStoreCustomerController extends Controller
{
    public function __construct(private VendorStoreCustomerService $storeCustomers)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user && \App\Support\VendorAccount::isStoreStaff($user), 403);

        $vendor = $this->storeCustomers->vendorForUser($user);
        $search = trim((string) $request->query('search', ''));

        $query = $this->storeCustomers->customersQuery($vendor);
        if ($search !== '') {
            $query->whereHas('user', function (Builder $userQuery) use ($search) {
                $userQuery->where(function (Builder $inner) use ($search) {
                    $inner->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });
        }

        $customers = $query
            ->orderByDesc('vendor_customer_stats.last_order_at')
            ->paginate(20)
            ->withQueryString();

        $summary = $this->storeCustomers->summary($vendor);

        // Never cache the customer list: a newly delivered order must be
        // visible on the next load without a hard reload or stale proxy copy.
        return response()->view('vendor.customers.index', compact('vendor', 'customers', 'summary', 'search'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Edit form for a customer of THIS store (owner or staff).
     * Only name / mobile / email are editable — never password.
     */
    public function edit(Request $request, \App\Models\Customer $customer)
    {
        $user = $request->user();
        abort_unless($user && \App\Support\VendorAccount::isStoreStaff($user), 403);
        $vendor = $this->storeCustomers->vendorForUser($user);
        abort_unless($this->storeCustomers->isCustomerOfVendor($vendor, (int) $customer->id), 404);

        return view('vendor.customers.edit', [
            'vendor' => $vendor,
            'customer' => $customer->load('user'),
        ]);
    }

    /**
     * Update contact details of a customer of THIS store.
     * Password is intentionally NOT editable here.
     */
    public function update(Request $request, \App\Models\Customer $customer)
    {
        $user = $request->user();
        abort_unless($user && \App\Support\VendorAccount::isStoreStaff($user), 403);
        $vendor = $this->storeCustomers->vendorForUser($user);
        abort_unless($this->storeCustomers->isCustomerOfVendor($vendor, (int) $customer->id), 404);

        $user = $customer->user;
        abort_unless($user, 404);

        $mobileRule = Rule::unique('users', 'mobile')->ignore($user->id);
        $emailRule = Rule::unique('users', 'email')->ignore($user->id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'mobile' => ['required', 'string', 'max:20', $mobileRule],
            'email' => ['nullable', 'email', 'max:190', $emailRule],
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
        ]);
        $user->update(['name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''))]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
            // The vendor-imposed password replaces the customer's credential:
            // drop existing API tokens so every device must sign in again.
            $user->tokens()->delete();

            return redirect()->route('vendor.customers.index')
                ->with('success', 'تم تحديث بيانات العميل وتعيين كلمة مرور جديدة بنجاح');
        }

        return redirect()->route('vendor.customers.index')
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }
}
