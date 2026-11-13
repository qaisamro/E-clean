<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfilePhotoRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\DriverRepository;
use App\Repositories\UserRepository;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private OtpService $otp)
    {
    }

    public function update(UserRequest $request)
    {
        $user = (new UserRepository())->updateByRequest($request, auth()->user());

        return $this->json('Profile is updated successfully', [
            'user' => (new UserResource($user)),
        ]);
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request)
    {
        $user = (new UserRepository())->updateProfilePhotoByRequest($request, auth()->user());

        return $this->json('Profile photo is updated successfully', [
            'user' => (new UserResource($user)),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = (new DriverRepository())->changePasswordByRequest($request, auth()->user());
        if ($user) {
            return $this->json('Password change successfully', [
                'user' => (new UserResource($user)),
            ]);
        }

        return $this->json('Incurrect password', [], Response::HTTP_BAD_REQUEST);
    }

    public function requestPhoneChange(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'mobile' => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'mobile')->ignore($user->id),
            ],
        ]);

        $result = $this->otp->issue($user, (string) $validated['mobile'], 'phone_change', $request);

        if (($result['status'] ?? null) === 'disabled') {
            return $this->json('Phone change via OTP is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (in_array($result['status'], ['rate_limited', 'cooldown'], true)) {
            return $this->json($result['message'], ['retry_after' => $result['retry_after'] ?? 60], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $data = [];
        if (($result['show_in_app'] ?? false) && $this->otp->showInApp((string) $validated['mobile'])) {
            $data['otp'] = $result['code'];
        }

        return $this->json('Verification code sent to the new phone number.', $data);
    }

    public function confirmPhoneChange(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'mobile' => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'mobile')->ignore($user->id),
            ],
            'otp' => ['required', 'string', 'max:12'],
        ]);

        $result = $this->otp->verifyAndConsume(
            (string) $validated['mobile'],
            (string) $validated['otp'],
            'phone_change',
            $request,
            $user->id
        );

        if ($result['status'] === 'rate_limited') {
            return $this->json($result['message'], ['retry_after' => $result['retry_after']], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($result['status'] === 'disabled') {
            return $this->json('Phone change via OTP is disabled for this operation.', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($result['status'] !== 'verified') {
            return $this->json('Invalid or expired verification code.', [], Response::HTTP_BAD_REQUEST);
        }

        $user->update([
            'mobile' => $validated['mobile'],
            'mobile_verified_at' => now(),
        ]);

        return $this->json('Phone number updated successfully.', [
            'user' => new UserResource($user->fresh()),
        ]);
    }
}
