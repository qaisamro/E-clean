<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'address_name' => [$this->routeIsAddressUpdate() ? 'required_without:is_default' : 'required', 'string', 'max:256'],
            'is_default' => ['sometimes', 'boolean'],
            'road_no' => ['nullable'],
            'house_no' => ['nullable'],
            'house_name' => ['nullable'],
            'area' => [$this->routeIsAddressUpdate() ? 'required_without:is_default' : 'required'],
            'flat_no' => ['nullable'],
            'sub_district_id' => ['nullable'],
            'district_id' => ['nullable'],
            'address_line' => ['nullable'],
            'address_line2' => ['nullable'],
            'delivery_note' => ['nullable'],
            'post_code' => ['nullable'],
            'phone' => ['nullable'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
        ];
    }

    private function routeIsAddressUpdate(): bool
    {
        return $this->route('address') !== null;
    }
}
