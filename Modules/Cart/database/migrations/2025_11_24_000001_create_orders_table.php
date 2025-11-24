<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cart_orders')) {
            return;
        }

        Schema::create('cart_orders', function (Blueprint $table) {
            $table->id('order_id');

            // Order Number
            $table->string('order_number')->unique()->comment('Sipariş numarası (ORD-2024-00001)');

            // User (müşteri)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Order Type
            $table->enum('order_type', ['sale', 'subscription', 'service', 'digital'])
                  ->default('sale')
                  ->comment('Sipariş tipi');

            $table->enum('order_source', ['web', 'admin', 'mobile', 'api'])
                  ->default('web');

            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'ready',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending');

            // Pricing
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('currency', 3)->default('TRY');

            // Payment
            $table->enum('payment_status', ['pending', 'partially_paid', 'paid', 'refunded', 'failed'])
                  ->default('pending');
            $table->decimal('paid_amount', 12, 2)->default(0);

            // Shipping
            $table->boolean('requires_shipping')->default(true)->comment('Kargo gerekli mi?');
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Coupon
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 12, 2)->default(0);

            // Customer Info (Snapshot)
            $table->string('customer_name')->nullable();
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_company')->nullable();
            $table->string('customer_tax_office')->nullable();
            $table->string('customer_tax_number')->nullable();

            // Addresses (JSON Snapshot)
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();

            // Agreements
            $table->boolean('agreed_terms')->default(false);
            $table->boolean('agreed_privacy')->default(false);
            $table->boolean('agreed_marketing')->default(false);

            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('metadata')->nullable();

            // IP & Browser
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Dates
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_orders');
    }
};
