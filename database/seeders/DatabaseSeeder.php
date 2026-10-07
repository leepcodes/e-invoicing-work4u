<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // SPECIFIC USER
        // =========================================================

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
        ]);

        // =========================================================
        // SELLER
        // =========================================================

        $seller = Seller::factory()->create([
            'user_id' => $user->id,
            'registered_name' => 'Test Seller Company',
            'trade_name' => 'Test Seller',
            'branch_code' => '000',
        ]);


        // =========================================================
        // SPECIFIC SELLER
        // =========================================================

        $testBuyer = Buyer::factory()->create([
            'seller_id' => $seller->id,
            'tin' => '123-456-789-000',
            'registered_name' => 'ABC TRADING INC.',
            'trade_name' => 'ABC TRADING',
        ]);

        // =========================================================
        // BUYERS
        // Create Seller create 1000 buyers
        // =========================================================

        $buyers = Buyer::factory()
            ->count(500)
            ->create([
                'seller_id' => $seller->id,
            ]);

        // =========================================================
        // INVOICES
        // Create 5,000 invoices
        // =========================================================

        foreach (range(1, 1000) as $index) {

            // Reuse buyers
            $buyer = $buyers[($index - 1) % $buyers->count()];

            $invoice = Invoice::factory()->create([
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'created_by' => $user->id,

                'invoice_number' => sprintf(
                    'INV-%d-%06d',
                    now()->year,
                    $index
                ),
            ]);

            // =====================================================
            // 2 ITEMS PER INVOICE
            // =====================================================

            InvoiceItem::factory()
                ->count(2)
                ->sequence(
                    ['line_number' => 1],
                    ['line_number' => 2],
                )
                ->create([
                    'invoice_id' => $invoice->id,
                ]);
        }
    }
}
