<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Accept both classic 0/1 and JSON/FormData true/false strings.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->exists('vendor_notifications_enabled')) {
            return;
        }

        $value = $this->input('vendor_notifications_enabled');
        if (is_string($value)) {
            $normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($normalized !== null) {
                $this->merge(['vendor_notifications_enabled' => $normalized]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = auth()->id();
        if(\request()->routeIs('admin.update')){
            $userId = $this->userId;
        }
        $rule = [
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'mobile' => ['nullable', 'numeric', "unique:users,mobile,$userId,id"],
            'email' => ['nullable', 'email', "unique:users,email,$userId,id"],
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gender' => 'nullable',
            'alternative_phone' => 'nullable',
            'driving_lience' => 'nullable',
            'date_of_birth' => 'nullable',
            'vendor_notifications_enabled' => 'sometimes|boolean',
        ];

        if (!$this->email && !$this->mobile) {
            $rule['mobile'] = 'required';
        }

        return $rule;
    }

    public function messages()
    {
        return [
            'mobile.required' => 'The email or phone number field is required',
        ];
    }
}
