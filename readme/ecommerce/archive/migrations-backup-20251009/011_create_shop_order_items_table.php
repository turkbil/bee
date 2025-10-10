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
        if (Schema::hasTable('shop_order_items')) {
            return;
        }

        Schema::create('shop_order_items', function (Blueprint $table) {
            // Primary Key
            $table->id('order_item_id');

            // Relations
            $table->foreignId('order_id')->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('product_id')->nullable()->comment('Ürün ID - shop_products ilişkisi (ürün silinirse null)');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');

            // Product Snapshot (sipariş anındaki veriler)
            $table->string('sku')->comment('Ürün SKU (snapshot - ürün kodları değişebilir)');
            $table->string('model_number')->nullable()->comment('Model numarası (snapshot)');
            $table->json('product_name')->comment('Ürün adı (JSON snapshot - {"tr":"...", "en":"..."})');
            $table->json('product_image')->nullable()->comment('Ürün görseli (JSON snapshot - tek görsel)');

            // Variant Info (Snapshot)
            $table->json('variant_options')->nullable()->comment('Varyant seçenekleri ({"mast_height":"3000mm","battery":"150Ah"})');

            // Pricing (Sipariş anındaki fiyatlar)
            $table->decimal('unit_price', 12, 2)->comment('Birim fiyat (₺) - İndirim öncesi');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('Birim başına indirim tutarı (₺)');
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('İndirim yüzdesi (%)');
            $table->decimal('final_price', 12, 2)->comment('İndirimli birim fiyat (₺)');

            // Quantity
            $table->integer('quantity')->default(1)->comment('Adet');

            // Line Total
            $table->decimal('subtotal', 12, 2)->comment('Ara toplam (₺) - final_price * quantity');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi tutarı (₺)');
            $table->decimal('total_amount', 12, 2)->comment('Satır toplamı (₺) - subtotal + tax_amount');

            // Tax Info
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Vergi oranı (%) - 18, 8, 1, 0');
            $table->string('tax_class')->nullable()->comment('Vergi sınıfı (standard, reduced, zero)');

            // Customization (Özelleştirme)
            $table->json('customization_options')->nullable()->comment('Özelleştirme seçenekleri (JSON - renk, logo, vb)');
            $table->text('special_instructions')->nullable()->comment('Özel talimatlar');

            // Status
            $table->enum('item_status', ['pending', 'processing', 'ready', 'shipped', 'delivered', 'cancelled', 'refunded'])
                  ->default('pending')
                  ->comment('Ürün durumu (her ürün ayrı takip edilebilir)');

            // Refund/Return
            $table->boolean('is_refundable')->default(true)->comment('İade edilebilir mi?');
            $table->boolean('is_refunded')->default(false)->comment('İade edildi mi?');
            $table->decimal('refunded_amount', 12, 2)->default(0)->comment('İade edilen tutar (₺)');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('order_id', 'idx_order');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');
            $table->index('sku', 'idx_sku');
            $table->index('item_status', 'idx_status');
            $table->index(['order_id', 'product_id'], 'idx_order_product');

            // Foreign Keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ürünleri de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse ID null olur ama snapshot kalır');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('set null')
                  ->comment('Varyant silinirse ID null olur ama snapshot kalır');
        })
        ->comment('Sipariş ürünleri - Her siparişin içindeki ürünler (snapshot yaklaşımı)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_order_items');
    }
};
