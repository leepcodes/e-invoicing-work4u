<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 100, 5000);

        $gross = $quantity * $unitPrice;

        return [
            'invoice_id' => Invoice::factory(),

            'line_number' => 1,
            'item_code' => 'ITEM-' . fake()->unique()->numerify('######'),
            'description' => fake()->words(3, true),
            'quantity' => $quantity,
            'unit_code' => 'PCS',
            'unit_price' => $unitPrice,

            'gross_amount' => $gross,
            'discount_amount' => 0,
            'net_amount' => $gross,

            'tax_type' => 'VAT',
            'tax_category' => 'STANDARD',
            'tax_rate' => 12,
            'taxable_amount' => $gross,
            'tax_amount' => round($gross * 0.12, 2),

            'line_total' => round($gross * 1.12, 2),
        ];
    }
}
