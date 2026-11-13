<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $isUpdate = request()->isMethod('put') || request()->isMethod('patch') ||
            request()->routeIs('product.update');
        $imgRule = $isUpdate ? 'nullable' : 'required';

        return [
            'name' => ['nullable', 'string', 'max:256'],
            'name_bn' => ['required', 'string', 'max:256'],
            'slug' => ['nullable', 'string', 'max:256'],
            'price' => ['required', 'numeric'],
            'description' => ['nullable'],
            'discount_price' => ['nullable', 'numeric'],
            'image' => [$imgRule, 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'service_id' => ['required', 'exists:services,id'],
            'variant_id' => ['nullable', 'exists:variants,id'],
        ];
    }

    public function messages()
    {
        return [
            'service_id.required' => 'The service field is required.'
        ];
    }
}
