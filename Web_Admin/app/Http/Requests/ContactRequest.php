<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|string|min:2|max:255',
            'email'        => 'required|email:rfc,dns|max:255',
            'phone_number' => 'nullable|string|min:6|max:20',
            'subject'      => 'required|string|min:3|max:255',
            'message'      => 'required|string|min:10|max:2000',
            'terms'        => 'required|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'         => 'The name field is required.',
            'name.string'           => 'The name must be a string.',
            'name.min'              => 'The name must be at least 2 characters.',
            'name.max'              => 'The name may not be greater than 255 characters.',
            'email.required'        => 'The email field is required.',
            'email.email'           => 'Please enter a valid email address.',
            'email.max'             => 'The email may not be greater than 255 characters.',
            'phone_number.string'   => 'The phone number must be a string.',
            'phone_number.min'      => 'The phone number must be at least 6 characters.',
            'phone_number.max'      => 'The phone number may not be greater than 20 characters.',
            'subject.required'      => 'The subject field is required.',
            'subject.string'        => 'The subject must be a string.',
            'subject.min'           => 'The subject must be at least 3 characters.',
            'subject.max'           => 'The subject may not be greater than 255 characters.',
            'message.required'      => 'The message field is required.',
            'message.string'        => 'The message must be a string.',
            'message.min'           => 'The message must be at least 10 characters.',
            'message.max'           => 'The message may not be greater than 2000 characters.',
            'terms.required'        => 'You must agree to the terms and conditions.',
            'terms.accepted'        => 'You must agree to the terms and conditions.',
        ];
    }
}
