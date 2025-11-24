<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cart_order_items')) {
            return;
        }

        Schema::create('cart_order_items', function (Blueprint $table) {
            $table->id('order_item_id');

            // Order relation
            $table->foreignId('order_id')
                  ->constrained('cart_orders', 'order_id')
                  ->onDelete('cascade');

            // Polymorphic relation (ShopProduct, Subscription, Service, etc.)
            $table->morphs('orderable');

            // Item Info (Snapshot)
            $table->string('item_title');
            $table->string('item_sku')->nullable();
            $table->string('item_image')->nullable();
            $table->text('item_description')->nullable();

            // Quantity & Pricing
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);

            // Currency (original)
            $table->string('original_currency', 3)->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('conversion_rate', 10, 4)->default(1);

            // Status
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'refunded'])
                  ->default('pending');

            // Digital delivery
            $table->boolean('is_digital')->default(false);
            $table->string('download_url')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamp('download_expires_at')->nullable();

            // Metadata
            $table->json('options')->nullable()->comment('Varyant seçenekleri vs.');
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_id');
            // NOT: morphs() zaten orderable_type + orderable_id için index oluşturur
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_order_items');
    }
};
