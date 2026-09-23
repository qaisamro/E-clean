<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'required|string',
            'email' => ['required', 'email', "unique:users,email,$userId,id"],
            'phone' => ['required', 'numeric', "unique:users,mobile,$userId,id"],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'phone.required' => 'The phone number field is required.',
            'phone.numeric' => 'Please enter a valid phone number.',
            'phone.unique' => 'This phone number is already in use.',
        ];
    }
}
