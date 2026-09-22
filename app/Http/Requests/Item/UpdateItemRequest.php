<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()?->seller !== null;
    }

    public function rules(): array
    {
        return [
            'item_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items')
                    ->ignore($this->route('item')->id)
                    ->where('seller_id', auth()->user()->seller->id),
            ],

            'description' => [
                'required',
                'string',
                'max:255',
            ],

            'unit_code' => [
                'required',
                'string',
                'max:20',
            ],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
