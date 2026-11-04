<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile'     => ['required', 'string', 'min:10', 'max:15', 'unique:users,mobile'],
            'password'   => ['required', 'string', 'min:8'],
            'terms'      => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.required'      => 'Email address is required.',
            'email.email'         => 'Please enter a valid email address.',
            'email.unique'        => 'This email is already registered.',
            'mobile.required'     => 'Phone number is required.',
            'mobile.unique'       => 'This phone number is already registered.',
            'mobile.min'          => 'Phone number must be at least 10 digits.',
            'mobile.max'          => 'Phone number may not be greater than 15 digits.',
            'password.required'   => 'Password is required.',
            'password.min'        => 'Password must be at least 8 characters.',
            'terms.accepted'      => 'You must accept the Terms of Service and Privacy Policy.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
            'mobile' => preg_replace('/\s+/', '', (string) $this->mobile),
        ]);
    }
}
