<?php

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 1000, 50000);
        $amountPaid = fake()->randomFloat(2, 0, $total);
        $amountDue = max($total - $amountPaid, 0);

        $paymentStatus = match (true) {
            $amountPaid <= 0 => 'UNPAID',
            $amountPaid >= $total => 'PAID',
            default => 'PARTIALLY PAID',
        };

        return [
            'seller_id' => Seller::factory(),
            'buyer_id' => Buyer::factory(),
            'created_by' => User::factory(),

            'document_type' => 'INVOICE',
            'invoice_number' => 'INV-' . now()->year . '-' . fake()->unique()->numerify('######'),
            'invoice_date' => fake()->date(),
            'invoice_time' => fake()->time(),
            'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),

            'reference_number' => 'REF-' . fake()->numerify('######'),
            'purchase_order_number' => 'PO-' . fake()->numerify('######'),

            'currency_code' => 'PHP',
            'accounting_currency_code' => 'PHP',
            'exchange_rate' => 1,
            'exchange_rate_date' => now()->toDateString(),
            'exchange_rate_source' => 'SYSTEM',

            'gross_amount' => $total,
            'discount_amount' => 0,
            'taxable_amount' => $total,
            'vat_amount' => 0,
            'withholding_tax_amount' => 0,
            'other_tax_amount' => 0,
            'net_amount' => $total,
            'total_amount' => $total,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,

            'accounting_gross_amount' => $total,
            'accounting_taxable_amount' => $total,
            'accounting_vat_amount' => 0,
            'accounting_total_amount' => $total,
            'accounting_amount_due' => $amountDue,

            'payment_terms' => '30 Days',
            'payment_method' => 'BANK_TRANSFER',
            'payment_status' => $paymentStatus,

            'system_branch_code' => '000',
            'fiscal_year' => now()->year,
            'source' => 'SEEDER',
            'remarks' => 'Dummy invoice data',
        ];
    }
}
