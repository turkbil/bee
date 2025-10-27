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
        if (Schema::hasTable('shop_return_items')) {
            return;
        }

        Schema::create('shop_return_items', function (Blueprint $table) {
            // Primary Key
            $table->id('return_item_id');

            // Relations
            $table->foreignId('return_id')->comment('İade ID - shop_returns ilişkisi');
            $table->foreignId('order_item_id')->comment('Sipariş ürün ID - shop_order_items ilişkisi');
            $table->foreignId('product_id')->nullable()->comment('Ürün ID - shop_products ilişkisi');

            // Product Info (Snapshot)
            $table->string('sku')->comment('Ürün SKU (snapshot)');
            $table->json('product_name')->comment('Ürün adı (JSON snapshot)');

            // Quantity
            $table->integer('quantity_returned')->comment('İade edilen adet');
            $table->integer('quantity_approved')->default(0)->comment('Onaylanan adet');
            $table->integer('quantity_rejected')->default(0)->comment('Reddedilen adet');

            // Pricing
            $table->decimal('unit_price', 12, 2)->comment('Birim fiyat (₺) - orijinal sipariş fiyatı');
            $table->decimal('refund_amount', 12, 2)->default(0)->comment('İade tutarı (₺)');

            // Condition
            $table->enum('item_condition', ['unopened', 'opened_unused', 'used', 'damaged'])
                  ->nullable()
                  ->comment('Ürün durumu: unopened=Açılmamış, opened_unused=Açık kullanılmamış, used=Kullanılmış, damaged=Hasarlı');

            $table->text('condition_notes')->nullable()->comment('Durum notları');

            // Restocking
            $table->boolean('can_restock')->default(true)->comment('Yeniden stoklanabilir mi?');
            $table->integer('restocked_quantity')->default(0)->comment('Stoklanan adet');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('return_id', 'idx_return');
            $table->index('order_item_id', 'idx_order_item');
            $table->index('product_id', 'idx_product');

            // Foreign Keys
            $table->foreign('return_id')
                  ->references('return_id')
                  ->on('shop_returns')
                  ->onDelete('cascade')
                  ->comment('İade silinirse ürünleri de silinir');

            $table->foreign('order_item_id')
                  ->references('id')
                  ->on('shop_order_items')
                  ->onDelete('cascade')
                  ->comment('Sipariş ürünü silinirse iade de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse ID null olur');
        })
        ->comment('İade ürünleri - İade edilen her bir ürün');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_return_items');
    }
};
