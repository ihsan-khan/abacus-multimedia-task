<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'payment_method_id' => 'required|string',
        ];
    }
}
