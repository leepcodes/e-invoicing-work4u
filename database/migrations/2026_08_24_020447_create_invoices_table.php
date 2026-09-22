<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            //Ownership
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();

            //buyer from buyers table
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();

            // Buyer Snapshot
            $table->string('buyer_name', 255)->nullable();
            $table->string('buyer_tin', 50)->nullable();
            $table->string('buyer_trade_name', 255)->nullable();
            $table->string('buyer_address_line_1', 255)->nullable();
            $table->string('buyer_address_line_2', 255)->nullable();
            $table->string('buyer_barangay', 150)->nullable();
            $table->string('buyer_city', 150)->nullable();
            $table->string('buyer_province', 150)->nullable();
            $table->string('buyer_postal_code', 20)->nullable();
            $table->char('buyer_country_code', 2)->default('PH');
            $table->string('buyer_email', 255)->nullable();
            $table->string('buyer_phone', 50)->nullable();

            //Document
            $table->string('document_type', 50)->default('INVOICE');

            $table->string('invoice_number', 100);
            $table->date('invoice_date');
            $table->time('invoice_time')->nullable();
            $table->date('due_date')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('purchase_order_number', 100)->nullable();

            //Currency
            $table->char('currency_code', 3)->default('PHP');
            $table->char('accounting_currency_code', 3)->default('PHP');
            $table->decimal('exchange_rate', 18, 8)->nullable();
            $table->date('exchange_rate_date')->nullable();
            $table->string('exchange_rate_source', 100)->nullable();

            //Invoice Totals
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('vat_amount', 18, 2)->default(0);
            $table->decimal('withholding_tax_amount', 18, 2)->default(0);
            $table->decimal('other_tax_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('amount_paid', 18, 2)->default(0);
            $table->decimal('amount_due', 18, 2)->default(0);

            //Accounting Totals
            $table->decimal('accounting_gross_amount', 18, 2)->default(0);
            $table->decimal('accounting_taxable_amount', 18, 2)->default(0);
            $table->decimal('accounting_vat_amount', 18, 2)->default(0);
            $table->decimal('accounting_total_amount', 18, 2)->default(0);
            $table->decimal('accounting_amount_due', 18, 2)->default(0);

            //Payment
            $table->string('payment_terms', 100)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_status', 30)->default('UNPAID');

            //System
            $table->string('created_by', 100)->nullable();
            $table->string('system_branch_code', 50)->nullable();
            $table->string('fiscal_year', 20)->nullable();
            $table->string('source', 50)->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Invoice number unique per seller
            $table->unique(['seller_id','invoice_number']);
            $table->index(['seller_id','invoice_date']);
            $table->index(['seller_id','payment_status']);
            $table->index('due_date');
            $table->index('reference_number');
            $table->index('purchase_order_number');
            $table->index(['seller_id', 'reference_number', 'invoice_date'], 'invoices_dedup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
