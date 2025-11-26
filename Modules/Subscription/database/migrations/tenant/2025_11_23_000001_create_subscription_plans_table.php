<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_plans')) {
            return;
        }
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id('subscription_plan_id');
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->json('features')->nullable();
            $table->decimal('price_daily', 10, 2)->default(0);
            $table->decimal('price_weekly', 10, 2)->default(0);
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_quarterly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->decimal('compare_price_monthly', 10, 2)->nullable();
            $table->decimal('compare_price_yearly', 10, 2)->nullable();
            $table->string('currency', 10)->default('TRY');
            $table->boolean('has_trial')->default(false);
            $table->integer('trial_days')->default(0);
            $table->integer('device_limit')->default(1);
            $table->boolean('requires_payment_method')->default(false);
            $table->integer('max_products')->nullable();
            $table->integer('max_orders')->nullable();
            $table->integer('max_storage_mb')->nullable();
            $table->json('custom_limits')->nullable();
            $table->boolean('has_analytics')->default(false);
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->json('enabled_features')->nullable();
            $table->enum('default_billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->string('badge_text')->nullable();
            $table->string('highlight_color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('subscribers_count')->default(0);
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
