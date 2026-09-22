<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('item_code', 100);
            $table->string('description');
            $table->string('unit_code', 20);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['seller_id', 'item_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
