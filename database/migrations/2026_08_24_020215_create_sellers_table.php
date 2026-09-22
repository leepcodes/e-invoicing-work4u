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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();

            //Foreign to users
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Business Information
            $table->string('tin', 50);
            $table->string('registered_name');
            $table->string('trade_name')->nullable();
            $table->string('branch_code', 20)->nullable();

            // Address
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country_code', 3)->default('PH');

            // Contact
            $table->string('company_email')->nullable();
            $table->string('phone', 50)->nullable();

            $table->string('logo')->nullable();

            $table->timestamps();

            // One seller profile per user
            $table->unique('user_id');

            $table->index('tin');
            $table->index('registered_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
