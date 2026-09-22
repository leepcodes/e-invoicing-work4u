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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            //Free - Pro - Business
            $table->string('code', 50)->unique();

            $table->string('name');
            $table->decimal('price_monthly', 12, 2)->default(0);

            // NULL = unlimited invoices
            $table->unsignedInteger('invoice_limit')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
