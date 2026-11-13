<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ReSendOtpRequest;
use App\Http\Requests\RegistrationRequest;
use App\Repositories\CustomerRepository;
use App\Repositories\DeviceKeyRepository;
use App\Models\CustomerJoinRequest;
use App\Exceptions\JoinRequestStoreUnavailable;
use App\Services\IntegrationSettingsService;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private UserRepository $userRepo;
    private OtpService $otp;
    private IntegrationSettingsService $settings;

    public function __construct(UserRepository $userRepo, OtpService $otp, IntegrationSettingsService $settings)
    {
        $this->userRepo = $userRepo;
        $this->otp = $otp;
        $this->settings = $settings;
    }

    /**
     * Registration requirements, read live from the Super Admin settings.
     *
     * - OTP requirement uses the existing OTP group (otp.enabled +
     *   otp.enabled_registration) — no duplicate settings are created.
     * - Approval requirement uses customer_registration.enabled +
     *   customer_registration.approval_required.
     * The two settings are completely independent.
     */
    private function registrationRequirements(): array
    {
        return [
            'otp_required' => $this->otp->enabledFor('registration'),
            'approval_required' => (string) $this->settings->get('customer_registration.enabled', '0') === '1'
                && (string) $this->settings->get('customer_registration.approval_required', '0') === '1',
        ];
    }

    /**
     * What the client must do next: verify_otp | await_approval | ready.
     */
    private function nextStep(bool $otpRequired, bool $otpVerified, bool $approvalRequired): string
    {
        if ($otpRequired && !$otpVerified) {
            return 'verify_otp';
        }

        if ($approvalRequired) {
            return 'await_approval';
        }

        return 'ready';
    }

    /**
     * Is the admin approval feature switched on?
     */
    private function approvalFeatureEnabled(): bool
    {
        return (string) $this->settings->get('customer_registration.enabled', '0') === '1'
            && (string) $this->settings->get('customer_registration.approval_required', '0') === '1';
    }

    public function register(RegistrationRequest $request)
    {
        $contact = (string) ($request->mobile ?? $request->email);

        $requirements = $this->registrationRequirements();
        $otpRequired = (bool) $requirements['otp_required'];
        $requireApproval = (bool) $requirements['approval_required'];

        try {
            // The whole registration is one transaction: if the approval record
            // cannot be written, nothing is left behind (no half-created,
            // unapproved account).
            return DB::transaction(function () use ($request, $contact, $otpRequired, $requireApproval) {
                $user = $this->userRepo->registerUser($request);

                // Record the policy this account was created under. Existing
                // accounts keep the column default (not required), so enabling
                // OTP later can never lock out current customers.
                $user->update(['phone_verification_required' => $otpRequired]);

                if ($requireApproval) {
                    // New customer accounts require admin approval before they can use the app.
                    $user->update(['is_active' => false]);
                }

                (new CustomerRepository())->storeByUser($user);
                $user->assignRole('customer');
                // NOTE: mobile_verified_at is NOT set here. It is only set after a
                // successful OTP verification, and only when OTP is enabled — an
                // unverified phone is never silently marked as verified.

                if ($request->device_key) {
                    (new DeviceKeyRepository())->storeByRequest($user->customer, $request);
                }

                // Build the join request BEFORE any OTP is sent, so a store
                // failure never sends a code for an account that does not exist.
                $trackingToken = null;
                if ($requireApproval) {
                    $joinRequest = CustomerJoinRequest::createOrFail([
                        'user_id' => $user->id,
                        'status' => CustomerJoinRequest::STATUS_PENDING,
                        'request_metadata' => [
                            'source' => 'mobile_app',
                            'device_key' => $request->input('device_key'),
                            'ip' => $request->ip(),
                            'otp_required' => $otpRequired,
                        ],
                    ]);
                    // Generate a secure tracking token (returned only once).
                    $trackingToken = $joinRequest->generateTrackingToken(30);
                }

                // Only request a code when the admin requires OTP at registration.
                $result = ['status' => 'not_required'];
                if ($otpRequired) {
                    $result = $this->otp->issue($user, $contact, 'registration', $request);
                }

                $nextStep = $this->nextStep($otpRequired, (bool) $user->mobile_verified_at, $requireApproval);

                $data = [
                    'user' => new UserResource($user),
                    'otp_required' => $otpRequired,
                    'approval_required' => $requireApproval,
                    'approval_status' => $requireApproval ? CustomerJoinRequest::STATUS_PENDING : 'approved',
                    'next_step' => $nextStep,
                ];

                if ($trackingToken) {
                    $data['tracking_token'] = $trackingToken;
                }

                // A full access token is only issued once every required step is done.
                if ($nextStep === 'ready') {
                    $data['access'] = $this->userRepo->getAccessToken($user);
                }

                if ($this->mayExposeOtp($result, $contact)) {
                    $data['otp'] = $result['code'];
                }

                $message = $requireApproval
                    ? $this->settings->get('customer_registration.pending_message', 'تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة. يرجى انتظار موافقة الإدارة لتفعيل حسابك.')
                    : 'Registration successfully complete';

                return $this->json($message, $data, $this->statusForOtpIssue($result));
            });
        } catch (JoinRequestStoreUnavailable $e) {
            // FAIL CLOSED: an approval-protected registration must never fall
            // back to "normal" registration. The transaction already rolled
            // back, so no unapproved account was created.
            report($e);

            return $this->json(
                'تعذر إنشاء طلب الانضمام حاليًا. يرجى المحاولة بعد قليل أو التواصل مع الدعم.',
                ['next_step' => 'retry_later'],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }

    public function mobileVerify(OTPRequest $request)
    {
        $contact = (string) $request->contact;
        $result = $this->otp->verifyAndConsume($contact, (string) $request->otp, 'registration', $request, auth()->id());

        if ($result['status'] === 'rate_limited') {
            return $this->json($result['message'], ['retry_after' => $result['retry_after']], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($result['status'] === 'disabled') {
            return $this->json('OTP verification is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($result['status'] !== 'verified') {
            return $this->json('Invalid OTP or contact!', [], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepo->findByContact($contact);
        if (!$user) {
            return $this->json('Invalid OTP or contact!', [], Response::HTTP_BAD_REQUEST);
        }

        $user->update(['mobile_verified_at' => now()]);

        // Tell the client what is still outstanding, and hand over a full
        // access token only when nothing else is required.
        try {
            $joinRequest = $this->approvalFeatureEnabled()
                ? CustomerJoinRequest::latestForUserOrFail($user->id)
                : CustomerJoinRequest::safeLatestForUser($user->id);
        } catch (JoinRequestStoreUnavailable $e) {
            // FAIL CLOSED: the phone is verified, but approval cannot be checked,
            // so no token is issued. The client retries or logs in later.
            report($e);

            return $this->json(
                'تم التحقق من رقم الهاتف، لكن خدمة طلبات الانضمام غير متاحة حاليًا. يرجى المحاولة بعد قليل.',
                [
                    'otp_verified' => true,
                    'next_step' => 'retry_later',
                ],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        if ($joinRequest && $joinRequest->isPending()) {
            $nextStep = 'await_approval';
        } elseif ($joinRequest && $joinRequest->isRejected()) {
            $nextStep = 'rejected';
        } else {
            $nextStep = 'ready';
        }

        $data = [
            'user' => new UserResource($user),
            'otp_verified' => true,
            'approval_required' => (bool) $joinRequest,
            'approval_status' => $joinRequest?->status ?? 'approved',
            'next_step' => $nextStep,
        ];

        if ($nextStep === 'ready' && $user->customer) {
            $data['access'] = $this->userRepo->getAccessToken($user);
        }

        return $this->json('Mobile verification complete', $data);
    }

    public function login(LoginRequest $request)
    {
        $contact = (string) ($request->contact);

        // Check the account state before authenticating so the client always
        // receives an actionable response instead of a generic failure.
        $user = $this->userRepo->findByContact($contact);
        if ($user && $user->hasRole('customer')) {
            try {
                $joinRequest = $this->approvalFeatureEnabled()
                    ? CustomerJoinRequest::latestForUserOrFail($user->id)
                    : CustomerJoinRequest::safeLatestForUser($user->id);
            } catch (JoinRequestStoreUnavailable $e) {
                // FAIL CLOSED: approval cannot be verified, so refuse the login
                // instead of treating the account as approved.
                report($e);

                return $this->json(
                    'خدمة طلبات الانضمام غير متاحة حاليًا، يرجى المحاولة بعد قليل.',
                    ['next_step' => 'retry_later'],
                    Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            // 1) Phone verification gate. Only applies to accounts that were
            //    registered while OTP was required, so existing customers are
            //    never blocked by enabling OTP later.
            if ($this->otp->enabledFor('registration') && $user->phone_verification_required && !$user->mobile_verified_at) {
                return $this->json(
                    'يرجى التحقق من رقم هاتفك عبر رمز التحقق قبل تسجيل الدخول.',
                    [
                        'otp_required' => true,
                        'phone_verified' => false,
                        'approval_required' => (bool) $joinRequest,
                        'approval_status' => $joinRequest?->status ?? 'approved',
                        'next_step' => 'verify_otp',
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }

            // 2) Join-request approval gate.
            if ($joinRequest && $joinRequest->isPending()) {
                return $this->json(
                    'الحساب قيد المراجعة من قبل الإدارة.',
                    [
                        'approval_status' => CustomerJoinRequest::STATUS_PENDING,
                        'approval_required' => true,
                        'next_step' => 'await_approval',
                        'message' => $this->settings->get('customer_registration.pending_message', 'الحساب قيد المراجعة من قبل الإدارة.'),
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            if ($joinRequest && $joinRequest->isRejected()) {
                return $this->json(
                    'تم رفض طلب انضمامك.',
                    [
                        'approval_status' => CustomerJoinRequest::STATUS_REJECTED,
                        'approval_required' => true,
                        'next_step' => 'rejected',
                        'message' => $this->settings->get('customer_registration.rejected_message', 'تم رفض طلب انضمامك.'),
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            // If account is inactive for other reasons, fall through to normal
            // authentication which will return invalid credentials.
        }

        $authUser = $this->authenticate($request);
        if ($authUser) {
            if ($authUser->customer) {
                if ($request->device_key) {
                    (new DeviceKeyRepository())->storeByRequest($authUser->customer, $request);
                }

                return $this->json('Log In Successfull', [
                    'user' => new UserResource($authUser),
                    'access' => $this->userRepo->getAccessToken($authUser),
                    'approval_status' => 'approved',
                    'approval_required' => false,
                    'next_step' => 'ready',
                ]);
            }
        }

        return $this->json('Credential is invalid!', [], Response::HTTP_BAD_REQUEST);
    }

    public function requestLoginOtp(Request $request)
    {
        $validated = $request->validate([
            'contact' => ['required', 'string', 'max:191'],
            'device_key' => ['nullable', 'string', 'max:191'],
        ]);

        $user = $this->userRepo->findByContact($validated['contact']);

        // Do not reveal whether the account exists.
        $data = ['message' => 'If the account exists, a verification code has been sent.'];
        if ($user) {
            $result = $this->otp->issue($user, (string) $validated['contact'], 'login', $request);

            if (($result['status'] ?? null) === 'disabled') {
                return $this->json('OTP login is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($this->mayExposeOtp($result, (string) $validated['contact'])) {
                $data['otp'] = $result['code'];
            }
        }

        return $this->json('Verification code request received', $data);
    }

    public function verifyLoginOtp(Request $request)
    {
        $validated = $request->validate([
            'contact' => ['required', 'string', 'max:191'],
            'otp' => ['required', 'string', 'max:12'],
            'device_key' => ['nullable', 'string', 'max:191'],
        ]);

        $result = $this->otp->verifyAndConsume((string) $validated['contact'], (string) $validated['otp'], 'login', $request);

        if ($result['status'] === 'rate_limited') {
            return $this->json($result['message'], ['retry_after' => $result['retry_after']], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($result['status'] === 'disabled') {
            return $this->json('OTP login is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($result['status'] !== 'verified') {
            return $this->json('Invalid or expired verification code.', [], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepo->findByContact((string) $validated['contact']);
        if (!$user || !$user->customer) {
            return $this->json('Invalid or expired verification code.', [], Response::HTTP_BAD_REQUEST);
        }

        if (!empty($validated['device_key'])) {
            (new DeviceKeyRepository())->storeByRequest($user->customer, $request);
        }

        // A successful OTP on the user's own mobile number is genuine phone
        // verification, so complete it instead of leaving the account stuck
        // between "OTP required" and "not verified".
        if ((string) $validated['contact'] === (string) $user->mobile && !$user->mobile_verified_at) {
            $user->update(['mobile_verified_at' => now()]);
        }

        return $this->json('Log In Successfull', [
            'user' => new UserResource($user),
            'access' => $this->userRepo->getAccessToken($user),
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        if (\request()->device_key) {
            (new DeviceKeyRepository())->destroy(\request()->device_key);
        }

        if ($user) {
            $user->token()->revoke();

            return $this->json('Logged out successfully!');
        }

        return $this->json('No Logged in user found', [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Store or refresh the authenticated customer's FCM device key.
     *
     * Used by the app to recover from accounts that were created before
     * device keys were saved at registration, and to keep the key fresh.
     * The key is not a secret — it identifies this device to Firebase — and
     * is replaced whenever the app installs or logs in again.
     */
    public function storeDeviceKey(Request $request)
    {
        $validated = $request->validate([
            'device_key' => ['required', 'string', 'max:191'],
            'device_type' => ['nullable', 'string', 'max:20'],
        ]);

        $user = auth()->user();
        if (!$user || !$user->customer) {
            return $this->json('Unauthenticated', [], Response::HTTP_UNAUTHORIZED);
        }

        (new DeviceKeyRepository())->storeByRequest($user->customer, $request);

        return $this->json('Device key stored', ['success' => true]);
    }

    private function authenticate(LoginRequest $request)
    {
        $user = $this->userRepo->findActiveBycontact($request->contact);

        if (!is_null($user) && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return false;
    }

    public function resendOTP(ReSendOtpRequest $request)
    {
        $contact = (string) $request->contact;
        $purpose = (string) ($request->input('purpose', 'registration'));
        if (!in_array($purpose, OtpService::PURPOSES, true)) {
            $purpose = 'registration';
        }

        $user = $this->userRepo->findByContact($contact);
        if (!$user) {
            return $this->json('Sorry, your contact is not found!');
        }

        $result = $this->otp->issue($user, $contact, $purpose, $request, true);

        if ($result['status'] === 'disabled') {
            return $this->json('OTP is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (in_array($result['status'], ['rate_limited', 'cooldown'], true)) {
            return $this->json($result['message'], ['retry_after' => $result['retry_after'] ?? 60], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $data = [];
        if ($this->mayExposeOtp($result, $contact)) {
            $data['otp'] = $result['code'];
        }

        return $this->json('Verification code is resent success to your contact', $data);
    }

    private function mayExposeOtp(array $result, string $contact): bool
    {
        // In-app OTP is only possible for explicitly listed test contacts and
        // only in local/staging/testing environments.
        return ($result['show_in_app'] ?? false) === true
            && $this->otp->showInApp($contact)
            && !empty($result['code']);
    }

    private function statusForOtpIssue(array $result): int
    {
        return in_array($result['status'], ['rate_limited', 'cooldown'], true)
            ? Response::HTTP_TOO_MANY_REQUESTS
            : Response::HTTP_OK;
    }

    /**
     * Secure join-request status check.
     *
     * Requires the one-time tracking token issued at registration (or via the
     * recovery flow). Never reveals whether an account exists; invalid,
     * expired or revoked tokens all return the same generic 404 response.
     */
    public function joinRequestStatusByToken(Request $request)
    {
        $validated = $request->validate([
            'tracking_token' => ['required', 'string', 'min:16', 'max:128'],
        ]);

        try {
            $joinRequest = CustomerJoinRequest::findByTrackingToken((string) $validated['tracking_token']);
        } catch (JoinRequestStoreUnavailable $e) {
            // A status is never guessed. No account information is revealed.
            report($e);

            return $this->json('رمز المتابعة غير صالح أو منتهي الصلاحية.', [], Response::HTTP_NOT_FOUND);
        }

        if (!$joinRequest) {
            return $this->json('رمز المتابعة غير صالح أو منتهي الصلاحية.', [], Response::HTTP_NOT_FOUND);
        }

        $message = $joinRequest->isPending()
            ? $this->settings->get('customer_registration.pending_message', 'الحساب قيد المراجعة من قبل الإدارة.')
            : ($joinRequest->isApproved()
                ? $this->settings->get('customer_registration.approved_message', 'تم قبول طلبك، يمكنك الآن استخدام التطبيق.')
                : $this->settings->get('customer_registration.rejected_message', 'تم رفض طلب انضمامك.'));

        // NOTE: No user data, contact details or rejection reasons are
        // exposed. The tracking token proves ownership of this account.
        return $this->json('حالة الطلب.', [
            'approval_status' => $joinRequest->status,
            'message' => $message,
        ]);
    }

    /**
     * Recovery step 1: request a fresh tracking token via OTP.
     *
     * The recovery OTP uses the "generic" purpose, which follows only the
     * global OTP switch — it is deliberately independent of
     * otp.enabled_registration, so a customer can still recover a lost
     * tracking token even when registration OTP is switched off.
     *
     * Sends an OTP to the registered contact (proof of phone ownership) and
     * always responds generically so account existence is never revealed.
     */
    public function joinRequestRecovery(Request $request)
    {
        $validated = $request->validate([
            'contact' => ['required', 'string', 'max:191'],
        ]);

        $contact = (string) $validated['contact'];

        if (!$this->otp->enabled()) {
            // Nothing was sent. The message stays generic and reveals nothing
            // about the account; it only explains the service state.
            return $this->json(
                'خدمة إرسال رموز التحقق معطلة حاليًا، يرجى التواصل مع الدعم.',
                [],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = $this->userRepo->findByContact($contact);

        if ($user && $user->hasRole('customer')) {
            try {
                // Feature-aware: when approval is enabled, a missing store means
                // recovery cannot be judged, so no OTP is sent.
                $joinRequest = $this->approvalFeatureEnabled()
                    ? CustomerJoinRequest::latestForUserOrFail($user->id)
                    : CustomerJoinRequest::safeLatestForUser($user->id);
            } catch (JoinRequestStoreUnavailable $e) {
                report($e);

                return $this->json(
                    'خدمة طلبات الانضمام غير متاحة حاليًا، يرجى المحاولة بعد قليل.',
                    ['next_step' => 'retry_later'],
                    Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            if ($joinRequest) {
                $this->otp->issue($user, $contact, 'generic', $request);
            }
        }

        // Generic response: never reveal whether an account exists.
        return $this->json('إذا كان هناك طلب انضمام لهذا الاتصال، فقد تم إرسال رمز التأكيد.');
    }

    /**
     * Recovery step 2: verify the OTP and issue a new tracking token.
     *
     * The previous token (if any) is automatically invalidated by issuing a
     * fresh one; only the newest hash is stored.
     */
    public function joinRequestRecoveryVerify(Request $request)
    {
        $validated = $request->validate([
            'contact' => ['required', 'string', 'max:191'],
            'otp' => ['required', 'string', 'max:12'],
        ]);

        $contact = (string) $validated['contact'];
        $verification = $this->otp->verify(
            $contact,
            (string) $validated['otp'],
            'generic',
            $request,
            null,
            false
        );

        // Generic failure response: never reveal whether an account exists.
        if (($verification['status'] ?? null) !== 'verified') {
            return $this->json('رمز غير صالح أو منتهي الصلاحية.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->userRepo->findByContact($contact);
        if (!$user || !$user->hasRole('customer')) {
            return $this->json('رمز غير صالح أو منتهي الصلاحية.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $joinRequest = $this->approvalFeatureEnabled()
                ? CustomerJoinRequest::latestForUserOrFail($user->id)
                : CustomerJoinRequest::safeLatestForUser($user->id);
        } catch (JoinRequestStoreUnavailable $e) {
            // The OTP is already consumed; report a retryable failure instead of
            // claiming there is no join request.
            report($e);

            return $this->json(
                'خدمة طلبات الانضمام غير متاحة حاليًا، يرجى المحاولة بعد قليل.',
                ['next_step' => 'retry_later'],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        if (!$joinRequest) {
            return $this->json('لا يوجد طلب انضمام مسجل لهذا الحساب.', [], Response::HTTP_NOT_FOUND);
        }

        // Issuing a new token invalidates the previous one (hash overwrite).
        $trackingToken = $joinRequest->generateTrackingToken(30);

        return $this->json('تم إنشاء رمز متابعة جديد.', [
            'tracking_token' => $trackingToken,
            'approval_status' => $joinRequest->status,
        ]);
    }

    /**
     * Return the authenticated customer's own join-request status.
     * Exposes only the app-safe status and a generic Arabic message; no
     * rejection reason or personal data is returned here.
     */
    public function joinRequestStatusMe(Request $request)
    {
        $user = $request->user();
        // The approval middleware already guaranteed readability; this is a
        // defensive second check so a status is never guessed.
        try {
            $joinRequest = CustomerJoinRequest::latestForUserOrFail($user->id);
        } catch (JoinRequestStoreUnavailable $e) {
            report($e);

            return $this->json('خدمة طلبات الانضمام غير متاحة حاليًا، يرجى المحاولة بعد قليل.', [], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (!$joinRequest) {
            return $this->json('الحساب مفعل.', [
                'approval_status' => 'approved',
                'approval_required' => false,
                'next_step' => 'ready',
                'message' => $this->settings->get('customer_registration.approved_message', 'تم قبول طلبك، يمكنك الآن استخدام التطبيق.'),
            ]);
        }

        $message = $joinRequest->isPending()
            ? $this->settings->get('customer_registration.pending_message', 'الحساب قيد المراجعة من قبل الإدارة.')
            : ($joinRequest->isApproved()
                ? $this->settings->get('customer_registration.approved_message', 'تم قبول طلبك، يمكنك الآن استخدام التطبيق.')
                : $this->settings->get('customer_registration.rejected_message', 'تم رفض طلب انضمامك.'));

        $nextStep = $joinRequest->isPending()
            ? 'await_approval'
            : ($joinRequest->isRejected() ? 'rejected' : 'ready');

        return $this->json('حالة الطلب.', [
            'approval_status' => $joinRequest->status,
            'approval_required' => true,
            'next_step' => $nextStep,
            'message' => $message,
        ]);
    }
}
