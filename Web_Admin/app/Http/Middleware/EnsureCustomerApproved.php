<?php

namespace App\Http\Middleware;

use App\Exceptions\JoinRequestStoreUnavailable;
use App\Models\CustomerJoinRequest;
use App\Services\IntegrationSettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerApproved
{
    public function __construct(private IntegrationSettingsService $settings)
    {
    }

    /**
     * Is the admin approval feature switched on?
     */
    private function approvalFeatureEnabled(): bool
    {
        return (string) $this->settings->get('customer_registration.enabled', '0') === '1'
            && (string) $this->settings->get('customer_registration.approval_required', '0') === '1';
    }

    /**
     * Block access to protected customer API routes when the account's
     * join-request is pending or rejected, or when phone verification is still
     * outstanding. Revokes any existing token so the client cannot reuse a
     * previously issued bearer token.
     *
     * Only generic, configurable messages are returned. The internal rejection
     * reason is NEVER exposed to the client (it stays visible to admins only).
     *
     * Failure policy:
     * - Approval feature ON  -> FAIL CLOSED. If the join-request store cannot be
     *   read, approval status is unverifiable, so access is denied (503).
     * - Approval feature OFF -> the previous safe behaviour (fail open), so
     *   existing customers keep working before the migration runs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('customer')) {
            // 1) Phone verification gate. Applies only to accounts registered
            //    while OTP was required — enabling OTP later never locks out
            //    existing customers.
            if (app(\App\Services\OtpService::class)->enabledFor('registration')
                && $user->phone_verification_required && !$user->mobile_verified_at) {
                if ($user->token()) {
                    $user->token()->revoke();
                }

                return response()->json([
                    'message' => 'يرجى التحقق من رقم هاتفك عبر رمز التحقق قبل استخدام التطبيق.',
                    'otp_required' => true,
                    'next_step' => 'verify_otp',
                ], Response::HTTP_FORBIDDEN);
            }

            // 2) Approval gate.
            try {
                $joinRequest = $this->approvalFeatureEnabled()
                    ? CustomerJoinRequest::latestForUserOrFail($user->id)
                    : CustomerJoinRequest::safeLatestForUser($user->id);
            } catch (JoinRequestStoreUnavailable $e) {
                return response()->json([
                    'message' => 'خدمة طلبات الانضمام غير متاحة حاليًا، يرجى المحاولة بعد قليل.',
                    'next_step' => 'retry_later',
                ], Response::HTTP_SERVICE_UNAVAILABLE);
            }

            if ($joinRequest && $joinRequest->isPending()) {
                // Revoke any existing token so it cannot be reused.
                if ($user->token()) {
                    $user->token()->revoke();
                }

                return response()->json([
                    'message' => $this->settings->get('customer_registration.pending_message', 'الحساب قيد المراجعة من قبل الإدارة.'),
                    'approval_status' => CustomerJoinRequest::STATUS_PENDING,
                    'next_step' => 'await_approval',
                ], Response::HTTP_FORBIDDEN);
            }

            if ($joinRequest && $joinRequest->isRejected()) {
                if ($user->token()) {
                    $user->token()->revoke();
                }

                return response()->json([
                    'message' => $this->settings->get('customer_registration.rejected_message', 'تم رفض طلب انضمامك.'),
                    'approval_status' => CustomerJoinRequest::STATUS_REJECTED,
                    'next_step' => 'rejected',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
