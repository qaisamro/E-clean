<?php

namespace App\Http\Controllers\Web\Root;

use App\Http\Controllers\Controller;
use App\Models\CustomerJoinRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuditLogger;
use App\Services\IntegrationSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerJoinRequestController extends Controller
{
    public function __construct(
        private IntegrationSettingsService $settings,
        private AuditLogger $audit
    ) {
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $query = CustomerJoinRequest::with(['user', 'approver', 'rejecter'])
            ->latest('id');

        // Filters
        $statusFilter = $request->input('status');
        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $statusFilter);
        }

        $search = $request->input('search');
        if ($search) {
            $query->whereHas('user', function ($user) use ($search) {
                $user->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(15)->appends($request->only(['status', 'search']));

        $stats = [
            'total' => CustomerJoinRequest::count(),
            'pending' => CustomerJoinRequest::pending()->count(),
            'approved' => CustomerJoinRequest::approved()->count(),
            'rejected' => CustomerJoinRequest::rejected()->count(),
        ];

        $approvalEnabled = (string) $this->settings->get('customer_registration.enabled', '0') === '1';
        $approvalRequired = (string) $this->settings->get('customer_registration.approval_required', '0') === '1';

        return view('root.customer-join-requests.index', compact(
            'requests',
            'stats',
            'statusFilter',
            'search',
            'approvalEnabled',
            'approvalRequired'
        ));
    }

    public function show(CustomerJoinRequest $joinRequest)
    {
        $this->authorizeSuperAdmin();

        $joinRequest->load(['user', 'approver', 'rejecter']);
        $user = $joinRequest->user;

        return view('root.customer-join-requests.show', compact('joinRequest', 'user'));
    }

    public function approve(Request $request, CustomerJoinRequest $joinRequest)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'approver_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!$joinRequest->isPending()) {
            return back()->with('error', 'هذا الطلب تم معالجته سابقًا.');
        }

        $approverId = $request->input('approver_id', auth()->id());

        DB::transaction(function () use ($joinRequest, $approverId) {
            $joinRequest->update([
                'status' => CustomerJoinRequest::STATUS_APPROVED,
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            // Activate the user account using the project's existing mechanism
            $user = $joinRequest->user;
            $user->update(['is_active' => true]);

            // Ensure customer record exists
            if (!$user->customer) {
                (new \App\Repositories\CustomerRepository())->storeByUser($user);
            }

            // Ensure customer role
            if (!$user->hasRole('customer')) {
                $user->assignRole('customer');
            }
        });

        $this->audit->log(
            'customer.join_request.approved',
            'customer_join_requests',
            'request:' . $joinRequest->id,
            'pending',
            'approved',
            [
                'user_id' => $joinRequest->user_id,
                'approver_id' => $approverId,
                'note' => $request->input('note'),
            ]
        );

        \Illuminate\Support\Facades\Cache::forget('customer_join_requests.pending_count');

        return back()->with('success', 'تم الموافقة على طلب الانضمام وتفعيل الحساب.');
    }

    public function reject(Request $request, CustomerJoinRequest $joinRequest)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'rejecter_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'rejection_reason' => ['required', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!$joinRequest->isPending()) {
            return back()->with('error', 'هذا الطلب تم معالجته سابقًا.');
        }

        $rejecterId = $request->input('rejecter_id', auth()->id());

        DB::transaction(function () use ($joinRequest, $rejecterId, $request) {
            $joinRequest->update([
                'status' => CustomerJoinRequest::STATUS_REJECTED,
                'rejected_by' => $rejecterId,
                'rejected_at' => now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            // Revoke all tokens/sessions for the rejected user
            $user = $joinRequest->user;
            if ($user && $user->token()) {
                $user->token()->revoke();
            }
        });

        $this->audit->log(
            'customer.join_request.rejected',
            'customer_join_requests',
            'request:' . $joinRequest->id,
            'pending',
            'rejected',
            [
                'user_id' => $joinRequest->user_id,
                'rejecter_id' => $rejecterId,
                'reason' => $request->input('rejection_reason'),
                'note' => $request->input('note'),
            ]
        );

        \Illuminate\Support\Facades\Cache::forget('customer_join_requests.pending_count');

        return back()->with('success', 'تم رفض طلب الانضمام وإبطال الجلسات المرتبطة.');
    }

    public function review(Request $request, CustomerJoinRequest $joinRequest)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!$joinRequest->isPending()) {
            return back()->with('error', 'هذا الطلب تم معالجته سابقًا.');
        }

        if ($request->input('action') === 'approve') {
            return $this->approve($request->merge(['approver_id' => auth()->id()]), $joinRequest);
        }

        return $this->reject($request->merge([
            'rejecter_id' => auth()->id(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]), $joinRequest);
    }

    public function reviewAgain(Request $request, CustomerJoinRequest $joinRequest)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ], [], ['note' => 'سبب إعادة النظر']);

        if (!$joinRequest->isRejected()) {
            return back()->with('error', 'يمكن فقط إعادة النظر في طلب مرفوض.');
        }

        DB::transaction(function () use ($joinRequest, $request) {
            $joinRequest->update([
                'status' => CustomerJoinRequest::STATUS_PENDING,
                'rejection_reason' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);
        });

        \Illuminate\Support\Facades\Cache::forget('customer_join_requests.pending_count');

        $this->audit->log(
            'customer.join_request.review_again',
            'customer_join_requests',
            'request:' . $joinRequest->id,
            'rejected',
            'pending',
            [
                'user_id' => $joinRequest->user_id,
                'note' => $request->input('note'),
            ]
        );

        return back()->with('success', 'تم إرجاع الطلب إلى انتظار الموافقة.');
    }

    public function revokeTrackingToken(Request $request, CustomerJoinRequest $joinRequest)
    {
        $this->authorizeSuperAdmin();

        $joinRequest->revokeTrackingToken();

        $this->audit->log(
            'customer.join_request.tracking_token_revoked',
            'customer_join_requests',
            'request:' . $joinRequest->id,
            'active',
            'revoked',
            [
                'user_id' => $joinRequest->user_id,
                'ip' => $request->ip(),
            ]
        );

        return back()->with('success', 'تم إبطال رمز المتابعة. العميل سيحتاج إلى طلب رمز جديد.');
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasAnyRole(['root', 'admin']), 403);
    }
}
