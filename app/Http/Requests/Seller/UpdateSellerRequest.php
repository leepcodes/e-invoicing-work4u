<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'registered_name' => ['nullable', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'branch_code' => ['nullable', 'string', 'max:50'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tin' => 'TIN',
            'registered_name' => 'registered name',
            'trade_name' => 'trade name',
            'branch_code' => 'branch code',
            'address_line_1' => 'address',
            'address_line_2' => 'additional address',
            'postal_code' => 'postal code',
            'country_code' => 'country code',
            'company_email' => 'company email',
        ];
    }
}
