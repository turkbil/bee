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
        if (Schema::hasTable('carts')) {
            return;
        }

        Schema::create('carts', function (Blueprint $table) {
            $table->id('cart_id');

            // User/Customer identification
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('device_id')->nullable();

            // Status
            $table->enum('status', ['active', 'abandoned', 'converted', 'merged'])->default('active')->index();
            $table->boolean('is_active')->default(true);

            // Cart totals
            $table->integer('items_count')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Coupon/Discount
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 12, 2)->default(0);

            // Conversion tracking
            $table->unsignedBigInteger('converted_to_order_id')->nullable();
            $table->timestamp('converted_at')->nullable();

            // Abandoned cart recovery
            $table->timestamp('abandoned_at')->nullable();
            $table->string('recovery_token')->nullable()->unique();
            $table->timestamp('recovery_email_sent_at')->nullable();
            $table->integer('recovery_email_count')->default(0);

            // Tracking
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Currency
            $table->string('currency_code', 3)->default('TRY');
            $table->unsignedBigInteger('currency_id')->nullable();

            // Metadata (JSON)
            $table->json('metadata')->nullable();

            // Timestamps
            $table->timestamps();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->softDeletes();

            // Indexes
            $table->index(['session_id', 'status']);
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
