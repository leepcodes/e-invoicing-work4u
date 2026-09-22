<?php

namespace App\Http\Requests\Invoice;

use App\Models\Buyer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->seller !== null;
    }

    public function rules(): array
    {
        return [
            'customer_type'         => ['nullable', 'in:existing,one_time'],
            // Ownership — buyer only; seller_id is resolved server-side
            // from the authenticated user, never trusted from the client.
            'buyer_id'              => ['nullable', 'integer', 'exists:buyers,id', 'required_if:customer_type,existing'],

            'buyer_name'            => ['nullable','string','max:255'],
            'buyer_tin'             => ['nullable','string','max:50',],
            'buyer_trade_name'      => ['nullable', 'string', 'max:255'],
            'buyer_address_line_1'  => ['nullable', 'string', 'max:255'],
            'buyer_address_line_2'  => ['nullable', 'string', 'max:255'],
            'buyer_barangay'        => ['nullable', 'string', 'max:255'],
            'buyer_city'            => ['nullable', 'string', 'max:255'],
            'buyer_province'        => ['nullable', 'string', 'max:255'],
            'buyer_postal_code'     => ['nullable', 'string', 'max:20'],
            'buyer_phone'           => ['nullable', 'string', 'max:50'],
            'buyer_email'           => ['nullable', 'email', 'max:255'],

            // Document
            'document_type'         => ['nullable', 'string', 'max:50'],
            'invoice_number'        => ['nullable', 'string', 'max:100'],
            'invoice_date'          => ['nullable', 'date'],
            'invoice_time'          => ['nullable', 'date_format:H:i'],
            'due_date'              => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'reference_number'      => ['nullable', 'string', 'max:100'],
            'purchase_order_number' => ['nullable', 'string', 'max:100'],

            // Currency
            'currency_code'            => ['required', 'string', 'size:3'],
            'accounting_currency_code' => ['nullable', 'string', 'size:3'],
            'exchange_rate'            => ['nullable', 'numeric', 'min:0'],
            'exchange_rate_date'       => ['nullable', 'date'],
            'exchange_rate_source'     => ['nullable', 'string', 'max:100'],

            // Payment — totals (gross/net/vat/total/amount_due) are
            // computed server-side in InvoiceService::create(), never
            // trusted from the client.
            'amount_paid'    => ['nullable', 'numeric', 'min:0'],
            'payment_terms'  => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['nullable', 'string', 'max:50'],

            // System (optional, currently unused by the frontend)
            'system_branch_code' => ['nullable', 'string', 'max:50'],
            'fiscal_year'        => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'source'             => ['nullable', 'string', 'max:50'],
            'remarks'            => ['nullable', 'string', 'max:1000'],

            // Invoice Items — only raw inputs the form collects.
            // Line-level totals are computed server-side.
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.item_code'       => ['nullable', 'string', 'max:100'],
            'items.*.description'     => ['required', 'string', 'max:500'],
            'items.*.quantity'        => ['required', 'numeric', 'gt:0'],
            'items.*.unit_code'       => ['required', 'string', 'max:20'],
            'items.*.unit_price'      => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_type'        => ['nullable', 'string', 'max:50'],
            'items.*.tax_category'    => ['nullable', 'string', 'max:50'],
            'items.*.tax_rate'        => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'buyer_id.required'            => 'Please select a buyer.',
            'buyer_id.exists'              => 'The selected buyer does not exist.',
            'invoice_number.required'      => 'Invoice number is required.',
            'invoice_date.required'        => 'Invoice date is required.',
            'due_date.after_or_equal'      => 'The due date must be on or after the invoice date.',
            'currency_code.size'           => 'Currency code must be exactly 3 characters.',
            'items.required'               => 'At least one invoice item is required.',
            'items.min'                    => 'An invoice must contain at least one item.',
            'items.*.description.required' => 'Each invoice item must have a description.',
            'items.*.quantity.gt'          => 'Item quantity must be greater than zero.',
        ];
    }

    /**
     * Make sure the selected buyer actually belongs to the
     * logged-in seller, not just any seller.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $sellerId = auth()->user()?->seller?->id;
            $buyerId = $this->input('buyer_id');

            if (! $buyerId || ! $sellerId) {
                return;
            }

            $ownsBuyer = Buyer::where('id', $buyerId)
                ->where('seller_id', $sellerId)
                ->exists();

            if (! $ownsBuyer) {
                $validator->errors()->add(
                    'buyer_id',
                    'The selected buyer does not belong to your account.'
                );
            }
        });
    }
}
