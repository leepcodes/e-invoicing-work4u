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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();

            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->restrictOnDelete();

            $table->string('status', 30)->default('active');

            //Free Trial
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('trial_ends_at')->nullable();

            //Period Start and End of Subscrition
            $table->dateTime('current_period_start')->nullable();
            $table->dateTime('current_period_end')->nullable();

            // Number of invoices created in current billing period
            $table->unsignedInteger('invoice_count_current_period')->default(0);

            // Payment provider references
            $table->string('external_subscription_id')->nullable();
            $table->string('external_customer_id')->nullable();

            $table->timestamps();
            $table->index(['seller_id','status']);

            $table->index('current_period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
