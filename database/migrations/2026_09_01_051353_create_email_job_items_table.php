<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_job_id')->constrained('email_jobs')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['PENDING', 'PROCESSING', 'SENT', 'FAILED'])->default('PENDING');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['email_job_id', 'invoice_id']);
            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_job_items');
    }
};
