<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * GLOBAL PAYMENT METHODS - Tüm modüller için ödeme yöntemleri
     * PayTR, Stripe, Iyzico, PayPal, Manuel vb.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');

            // Basic Info
            $table->json('title')->comment('{"tr":"Kredi Kartı","en":"Credit Card"}');
            $table->string('slug')->unique()->comment('paytr-credit-card');
            $table->json('description')->nullable();

            // Gateway Info
            $table->enum('gateway', ['paytr', 'stripe', 'iyzico', 'paypal', 'manual'])
                  ->comment('Ödeme gateway');
            $table->enum('gateway_mode', ['test', 'live'])->default('test');
            $table->json('gateway_config')->nullable()->comment('API keys, merchant IDs (JSON)');

            // Payment Type Support
            $table->boolean('supports_purchase')->default(true)->comment('Satış ödemeleri');
            $table->boolean('supports_subscription')->default(false)->comment('Abonelik ödemeleri');
            $table->boolean('supports_donation')->default(false)->comment('Bağış ödemeleri');

            // Fees & Limits
            $table->decimal('fixed_fee', 10, 2)->default(0);
            $table->decimal('percentage_fee', 5, 2)->default(0);
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 14, 2)->nullable();

            // Installment
            $table->boolean('supports_installment')->default(false);
            $table->integer('max_installments')->default(1);
            $table->json('installment_options')->nullable();

            // Currency
            $table->json('supported_currencies')->comment('["TRY","USD","EUR"]');

            // Display
            $table->string('icon')->nullable();
            $table->string('logo_url')->nullable();
            $table->integer('sort_order')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_verification')->default(false);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('gateway');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
