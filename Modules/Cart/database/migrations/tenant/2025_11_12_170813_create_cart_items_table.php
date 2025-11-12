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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');

            // Cart relationship
            $table->unsignedBigInteger('cart_id')->index();
            $table->boolean('is_active')->default(true);

            // Polymorphic item relationship (universal: Shop, Subscription, Service, etc.)
            $table->morphs('cartable'); // Creates cartable_type, cartable_id

            // Product/Variant (for Shop items - optional)
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('product_variant_id')->nullable();

            // Quantity
            $table->integer('quantity')->default(1);

            // Pricing
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            // Tax
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Currency
            $table->unsignedBigInteger('currency_id')->nullable();

            // Customization
            $table->json('customization_options')->nullable();
            $table->text('special_instructions')->nullable();

            // Stock tracking
            $table->boolean('in_stock')->default(true);
            $table->timestamp('stock_checked_at')->nullable();

            // Flags
            $table->boolean('moved_from_wishlist')->default(false);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['cart_id', 'is_active']);
            // morphs() already creates index for cartable_type, cartable_id

            // Foreign key
            $table->foreign('cart_id')->references('cart_id')->on('carts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
