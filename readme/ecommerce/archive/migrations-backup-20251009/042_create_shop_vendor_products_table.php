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
        if (Schema::hasTable('shop_vendor_products')) {
            return;
        }

        Schema::create('shop_vendor_products', function (Blueprint $table) {
            // Primary Key
            $table->id('vendor_product_id');

            // Relations
            $table->foreignId('vendor_id')->comment('Satıcı ID - shop_vendors ilişkisi');
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');

            // Pricing
            $table->decimal('vendor_price', 12, 2)->nullable()->comment('Satıcının fiyatı (₺)');
            $table->decimal('commission_amount', 10, 2)->default(0)->comment('Komisyon tutarı (₺)');
            $table->decimal('final_price', 12, 2')->nullable()->comment('Final fiyat (₺) - komisyon sonrası');

            // Stock (vendor bazlı stok)
            $table->integer('stock_quantity')->default(0)->comment('Stok miktarı');
            $table->boolean('in_stock')->default(true)->comment('Stokta var mı?');

            // Delivery
            $table->integer('delivery_days_min')->nullable()->comment('Minimum teslimat süresi (gün)');
            $table->integer('delivery_days_max')->nullable()->comment('Maximum teslimat süresi (gün)');

            // Status
            $table->boolean('is_primary')->default(false)->comment('Birincil satıcı mı? (bu üründe ana satıcı)');
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('vendor_id', 'idx_vendor');
            $table->index('product_id', 'idx_product');
            $table->index('is_primary', 'idx_primary');
            $table->index('is_active', 'idx_active');
            $table->unique(['vendor_id', 'product_id'], 'unique_vendor_product');

            // Foreign Keys
            $table->foreign('vendor_id')
                  ->references('vendor_id')
                  ->on('shop_vendors')
                  ->onDelete('cascade')
                  ->comment('Satıcı silinirse ilişkiler de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse ilişkiler de silinir');
        })
        ->comment('Satıcı-Ürün ilişkileri - Hangi satıcı hangi ürünü satıyor (marketplace)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_vendor_products');
    }
};
