<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
            $table->string('email');
            $table->string('subject')->nullable();
            $table->enum('status', ['PENDING', 'PROCESSING', 'SENT', 'FAILED'])->default('PENDING');
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('sent_items')->default(0);
            $table->unsignedInteger('failed_items')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'buyer_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_jobs');
    }
};
