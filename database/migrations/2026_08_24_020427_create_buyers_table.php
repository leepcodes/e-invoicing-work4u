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
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();

            //Foreign Seller
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();

            // Business Information
            $table->string('tin', 50)->nullable();
            $table->string('registered_name');
            $table->string('trade_name')->nullable();
            $table->string('customer_code', 100)->nullable();

            // Address
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country_code', 3)->default('PH');

            // Contact
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            $table->timestamps();

            // Tenant isolation
            $table->index(['seller_id','tin']);

            $table->index(['seller_id','customer_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
