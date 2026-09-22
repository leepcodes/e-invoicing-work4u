<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2)->unique();
            $table->string('country_name');
            $table->char('currency_code', 3);
            $table->string('currency_name');
            $table->string('symbol', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('currency_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
