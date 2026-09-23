<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
        return [
            'question' => 'required|string|min:3|max:200',
            'answer' => 'required|string|min:10|max:500',
            'category_id' => 'required|exists:faq_categories,id',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question.required' => 'The question field is required.',
            'question.min' => 'The question must be at least 3 characters.',
            'question.max' => 'The question may not be greater than 200 characters.',
            'answer.required' => 'The answer field is required.',
            'answer.min' => 'The answer must be at least 10 characters.',
            'answer.max' => 'The answer may not be greater than 500 characters.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
