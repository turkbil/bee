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
        if (Schema::hasTable('shop_inventory')) {
            return;
        }

        Schema::create('shop_inventory', function (Blueprint $table) {
            // Primary Key
            $table->id('inventory_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');
            $table->foreignId('warehouse_id')->nullable()->comment('Depo ID - shop_warehouses ilişkisi');

            // Stock Levels
            $table->integer('quantity_on_hand')->default(0)->comment('Eldeki stok (fiziksel stok)');
            $table->integer('quantity_available')->default(0)->comment('Kullanılabilir stok (on_hand - reserved)');
            $table->integer('quantity_reserved')->default(0)->comment('Rezerve edilmiş stok (siparişteki ürünler)');
            $table->integer('quantity_incoming')->default(0)->comment('Yolda gelen stok');
            $table->integer('quantity_damaged')->default(0)->comment('Hasarlı/Kullanılamaz stok');

            // Reorder Settings
            $table->integer('reorder_level')->default(0)->comment('Yeniden sipariş seviyesi (bu seviyenin altına düşünce uyarı)');
            $table->integer('reorder_quantity')->default(0)->comment('Yeniden sipariş miktarı (kaç adet sipariş verilmeli)');
            $table->integer('safety_stock')->default(0)->comment('Güvenlik stoku (minimum tutulması gereken)');
            $table->integer('max_stock')->default(0)->comment('Maksimum stok seviyesi');

            // Costs (FIFO, LIFO, Average)
            $table->decimal('unit_cost', 12, 2)->default(0)->comment('Birim maliyet (₺)');
            $table->decimal('total_value', 14, 2)->default(0)->comment('Toplam değer (₺) - quantity_on_hand * unit_cost');
            $table->enum('costing_method', ['fifo', 'lifo', 'average', 'standard'])
                  ->default('average')
                  ->comment('Maliyet hesaplama yöntemi: fifo=İlk giren ilk çıkar, lifo=Son giren ilk çıkar, average=Ortalama, standard=Standart');

            // Physical Inventory
            $table->timestamp('last_counted_at')->nullable()->comment('Son sayım tarihi');
            $table->integer('last_counted_quantity')->nullable()->comment('Son sayımda tespit edilen miktar');
            $table->foreignId('last_counted_by_user_id')->nullable()->comment('Son sayımı yapan kullanıcı ID');

            // Stock Alerts
            $table->boolean('low_stock_alert')->default(false)->comment('Düşük stok uyarısı aktif mi?');
            $table->boolean('out_of_stock_alert')->default(false)->comment('Stok tükendi uyarısı aktif mi?');

            // Bin/Location
            $table->string('bin_location')->nullable()->comment('Raf/Konum bilgisi (A-12-3 gibi)');
            $table->string('aisle')->nullable()->comment('Koridor');
            $table->string('shelf')->nullable()->comment('Raf');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');
            $table->index('warehouse_id', 'idx_warehouse');
            $table->index('quantity_available', 'idx_available');
            $table->index('reorder_level', 'idx_reorder');
            $table->unique(['product_id', 'product_variant_id', 'warehouse_id'], 'unique_product_warehouse');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse envanter de silinir');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse envanter de silinir');

            $table->foreign('warehouse_id')
                  ->references('warehouse_id')
                  ->on('shop_warehouses')
                  ->onDelete('cascade')
                  ->comment('Depo silinirse envanter de silinir');
        })
        ->comment('Envanter - Ürün stok seviyeleri ve depo yönetimi');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_inventory');
    }
};
