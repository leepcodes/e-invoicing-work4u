<?php

namespace App\Http\Requests\Buyer;

use Illuminate\Foundation\Http\FormRequest;

class CreateBuyerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Buyer Information
            'tin'             => ['nullable', 'string', 'max:50'],
            'registered_name' => ['required', 'string', 'max:255'],
            'trade_name'      => ['nullable', 'string', 'max:255'],
            'customer_code'   => ['required', 'string', 'max:100'],

            // Address
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'barangay'       => ['nullable', 'string', 'max:100'],
            'city'           => ['required', 'string', 'max:100'],
            'province'       => ['required', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:20'],
            'country_code'   => ['required', 'string', 'size:2'],

            // Contact
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'registered_name.required'  => 'Registered name is required.',
            'customer_code.required'    => 'Customer code is required.',

            'address_line_1.required'   => 'Address is required.',
            'city.required'             => 'City is required.',
            'province.required'         => 'Province is required.',

            'country_code.required'     => 'Country is required.',
            'country_code.size'         => 'Country code must be exactly 2 characters.',

            'email.email'               => 'Please enter a valid email address.',
        ];
    }
}
