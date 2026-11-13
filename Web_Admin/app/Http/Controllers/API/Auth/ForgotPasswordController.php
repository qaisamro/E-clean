<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ForgotPasswordOtpVerifyRequest;

class ForgotPasswordController extends Controller
{
    private OtpService $otp;
    private UserRepository $userRepo;

    public function __construct(OtpService $otp, UserRepository $userRepo)
    {
        $this->otp = $otp;
        $this->userRepo = $userRepo;
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        // Policy is checked before lookup: known and unknown contacts get the
        // same response. Disabling OTP never grants a reset token by contact.
        if (!$this->otp->enabledFor('password_reset')) {
            return $this->json('استعادة كلمة السر بالرمز معطلة حاليًا. تواصل مع الدعم لاستعادة حسابك.', [
                'otp_required' => false,
                'next_step' => 'contact_support',
            ]);
        }
        $contact = (string) $request->contact;
        $user = $this->userRepo->findByContact($contact);

        $data = ['otp_required' => true, 'next_step' => 'verify_otp'];
        if ($user) {
            $result = $this->otp->issue($user, $contact, 'password_reset', $request);

            if (($result['status'] ?? null) === 'disabled') {
                return $this->json('Password reset via OTP is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (in_array($result['status'], ['rate_limited', 'cooldown'], true)) {
                return $this->json($result['message'], ['retry_after' => $result['retry_after'] ?? 60], Response::HTTP_TOO_MANY_REQUESTS);
            }

            if (($result['show_in_app'] ?? false) && $this->otp->showInApp($contact)) {
                $data['otp'] = $result['code'];
            }
        }

        // The response is intentionally identical for existing and unknown
        // contacts to prevent account enumeration.
        return $this->json('If the account exists, a password reset code has been sent.', $data);
    }

    public function verifyOtp(ForgotPasswordOtpVerifyRequest $request)
    {
        $contact = (string) $request->contact;
        $user = $this->userRepo->findByContact($contact);

        if (!$user) {
            return $this->json('Invalid OTP', [], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->otp->verify($contact, (string) $request->otp, 'password_reset', $request, $user->id);

        if ($result['status'] === 'rate_limited') {
            return $this->json($result['message'], ['retry_after' => $result['retry_after']], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($result['status'] === 'disabled') {
            return $this->json('Password reset via OTP is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($result['status'] !== 'verified') {
            return $this->json('Invalid OTP', [], Response::HTTP_BAD_REQUEST);
        }

        return $this->json('Otp matched successfully!', [
            'token' => $result['record']->token,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $verifyCode = $this->otp->latestValidToken((string) $request->token);

        if (!$verifyCode) {
            return $this->json('Invalid or expired token', [], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepo->findByContact($verifyCode->contact);

        if (!$user) {
            return $this->json('Invalid or expired token', [], Response::HTTP_BAD_REQUEST);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $verifyCode->delete();

        return $this->json('Password reset successfully!');
    }
}
