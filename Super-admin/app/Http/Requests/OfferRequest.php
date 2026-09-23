<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
        $imgRule = request()->isMethod('put') ? 'nullable' : 'nullable';

        return [
            'title' => ['required', 'string', 'max:256'],
            'description' => ['nullable'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'image' => [$imgRule, 'image', 'mimes:jpg,jpeg,png,svg'],
        ];
    }
}