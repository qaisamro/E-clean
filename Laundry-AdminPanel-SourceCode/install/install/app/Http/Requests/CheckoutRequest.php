<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'payment_method' => 'required|in:stripe,cod',
            'address_id' => 'required|exists:addresses,id',
            'pick_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:pick_date',
            'pick_hour' => 'required|date_format:H:i',
            'delivery_hour' => 'required|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'address_id.required' => 'Please select a delivery address.',
            'address_id.exists' => 'Selected address does not exist.',
            'pick_date.required' => 'Please select a pick-up date.',
            'pick_date.date' => 'Invalid date format for pick-up date.',
            'delivery_date.required' => 'Please select a delivery date.',
            'delivery_date.date' => 'Invalid date format for delivery date.',
            'delivery_date.after_or_equal' => 'Delivery date must be the same day or after the pick-up date.',
            'pick_hour.required' => 'Please select a pick-up time.',
            'pick_hour.date_format' => 'Pick-up time must be in HH:MM format.',
            'delivery_hour.required' => 'Please select a delivery time.',
            'delivery_hour.date_format' => 'Delivery time must be in HH:MM format.',
        ];
    }
}
