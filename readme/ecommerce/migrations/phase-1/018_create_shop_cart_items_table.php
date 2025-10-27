<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('shop_cart_items')) {
            return;
        }

        Schema::create('shop_cart_items', function (Blueprint $table) {
            // Primary Key
            $table->id('cart_item_id');

            // Relations
            $table->foreignId('cart_id')->comment('Sepet ID - shop_carts ilişkisi');
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');

            // Quantity
            $table->integer('quantity')->default(1)->comment('Adet');

            // Pricing (Sepete eklendiği andaki fiyatlar)
            $table->decimal('unit_price', 12, 2)->comment('Birim fiyat (₺)');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('Birim başına indirim (₺)');
            $table->decimal('final_price', 12, 2)->comment('İndirimli birim fiyat (₺)');
            $table->decimal('subtotal', 12, 2)->comment('Ara toplam (₺) - final_price * quantity');

            // Tax
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi tutarı (₺)');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Vergi oranı (%)');

            // Total
            $table->decimal('total', 12, 2)->comment('Satır toplamı (₺) - subtotal + tax_amount');

            // Customization
            $table->json('customization_options')->nullable()->comment('Özelleştirme seçenekleri (JSON)');
            $table->text('special_instructions')->nullable()->comment('Özel talimatlar');

            // Stock Check
            $table->boolean('in_stock')->default(true)->comment('Stokta var mı? (sepete ekleme anında)');
            $table->timestamp('stock_checked_at')->nullable()->comment('Son stok kontrol tarihi');

            // Wishlist to Cart
            $table->boolean('moved_from_wishlist')->default(false)->comment('Favorilerden mi eklendi?');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('cart_id', 'idx_cart');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');
            $table->index(['cart_id', 'product_id'], 'idx_cart_product');

            // Foreign Keys
            $table->foreign('cart_id')
                  ->references('id')
                  ->on('shop_carts')
                  ->onDelete('cascade')
                  ->comment('Sepet silinirse ürünleri de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse sepetten de silinir');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse sepetten de silinir');
        })
        ->comment('Sepet ürünleri - Sepetteki her bir ürün');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_cart_items');
    }
};
