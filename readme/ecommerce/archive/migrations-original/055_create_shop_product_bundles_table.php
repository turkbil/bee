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
        if (Schema::hasTable('shop_product_bundles')) {
            return;
        }

        Schema::create('shop_product_bundles', function (Blueprint $table) {
            // Primary Key
            $table->id('product_bundle_id');

            // Relations
            $table->foreignId('bundle_product_id')->comment('Paket ürün ID - shop_products ilişkisi (product_type=bundle olan)');
            $table->foreignId('item_product_id')->comment('Paket içi ürün ID - shop_products ilişkisi');
            $table->foreignId('item_variant_id')->nullable()->comment('Paket içi varyant ID - shop_product_variants ilişkisi');

            // Quantity
            $table->integer('quantity')->default(1)->comment('Paket içinde kaç adet');

            // Pricing
            $table->boolean('can_be_sold_separately')->default(true)->comment('Ayrı satılabilir mi?');
            $table->decimal('individual_price', 12, 2)->nullable()->comment('Bireysel fiyat (₺) - ayrı satılıyorsa');
            $table->decimal('bundle_discount', 10, 2)->default(0)->comment('Paket indirimi (₺)');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_optional')->default(false)->comment('Opsiyonel mi? (müşteri seçebilir)');
            $table->boolean('is_default')->default(true)->comment('Varsayılan seçim mi?');

            // Stock
            $table->boolean('check_stock')->default(true)->comment('Stok kontrolü yapılsın mı?');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('bundle_product_id', 'idx_bundle');
            $table->index('item_product_id', 'idx_item');
            $table->index('item_variant_id', 'idx_variant');
            $table->index('sort_order', 'idx_sort');

            // Foreign Keys
            $table->foreign('bundle_product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Paket ürün silinirse içindekiler de silinir');

            $table->foreign('item_product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Paket içi ürün silinirse paketten de silinir');

            $table->foreign('item_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse paketten de silinir');
        })
        ->comment('Ürün paketleri - Paket ürünlerin içindeki ürünler (bundle items)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_bundles');
    }
};
