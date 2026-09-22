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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            //Invoice
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();


            //item fron items table
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();

            //Item
            $table->unsignedInteger('line_number');
            $table->string('item_code', 100)->nullable();
            $table->text('description');
            $table->decimal('quantity', 18, 4)->default(1);
            $table->string('unit_code', 20)->nullable();
            $table->decimal('unit_price', 18, 2)->default(0);

            //Amounts
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);

            //Tax
            $table->string('tax_type', 50)->nullable();
            $table->string('tax_category', 50)->nullable();
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);

            //Final line total
            $table->decimal('line_total', 18, 2)->default(0);
            $table->timestamps();

            //Prevent duplicate line numbers
            $table->unique(['invoice_id', 'line_number']);
            $table->index('item_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
