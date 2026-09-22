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
        Schema::create('csv_import_jobs', function (Blueprint $table) {
            $table->id();

            // Seller / Tenant
            $table->foreignId('seller_id')
                ->constrained('sellers')
                ->cascadeOnDelete();

            // Import information
            $table->string('file_name');
            $table->string('file_path');

            // Import status
            $table->string('status')->default('PENDING');

            // Progress
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('successful_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);

            // Error information
            $table->text('error_message')->nullable();

            // Timestamps for processing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_import_jobs');
    }
};
