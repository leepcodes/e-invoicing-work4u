<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //User
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],

            //Seller Profile Info
            'tin' => ['required', 'min:9'],
            'registered_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['required', 'string', 'max:255'],
            'branch_code' => ['required',  'string', 'min:3'],

            //Address
            'address_line_1' => ['required', 'string'],
            'address_line_2' => ['string', 'nullable'],
            'barangay' => ['required', 'string'],
            'city' => ['required', 'string'],
            'province' => ['required', 'string'],
            'postal_code' => ['required', 'string'],
            'country_code' => ['required', 'string'],

            //Contact
            'company_email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string'],
        ];
    }
}
