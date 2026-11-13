<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use App\Enum\PaymentGateway;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
class OrderRequest extends FormRequest


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
            'products' => ['required', 'array'],
            'products.*.id' => 'required|exists:' . (new Product())->getTable() . ',id',
            'products.*.quantity' => 'required|integer|min:1',
            'additional_service_id' => 'nullable|array',
            'pick_date' => ['required', 'date'],
            'pick_hour' => ['required'],
            'delivery_date' => ['required', 'date'],
            'delivery_hour' => ['required'],
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')
                    ->where('customer_id', auth()->user()?->customer?->id ?? 0),
            ],
            'coupon_id' => ['nullable','exists:' . (new Coupon())->getTable() . ',id'],
            'payment_type' => ['required', new Enum(PaymentGateway::class)],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
        ];
    }

    public function messages()
    {
        return [
            'address_id.required' => 'The address fuild is required.',
        ];
    }
}
