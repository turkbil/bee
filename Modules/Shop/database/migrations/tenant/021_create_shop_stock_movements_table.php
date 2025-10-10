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
        if (Schema::hasTable('shop_stock_movements')) {
            return;
        }

        Schema::create('shop_stock_movements', function (Blueprint $table) {
            $table->comment('Stok hareketleri - Tüm stok giriş/çıkış/transfer kayıtları (audit trail)');

            // Primary Key
            $table->id('stock_movement_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');
            $table->foreignId('warehouse_id')->nullable()->comment('Depo ID - shop_warehouses ilişkisi');

            // Movement Type
            $table->enum('movement_type', [
                'in',               // Giriş
                'out',              // Çıkış
                'transfer',         // Transfer
                'adjustment',       // Düzeltme
                'return',           // İade
                'damage',           // Hasar
                'theft',            // Kayıp/Çalıntı
                'count'             // Sayım düzeltmesi
            ])->comment('Hareket tipi');

            // Reason/Reference
            $table->enum('reason', [
                'purchase',         // Satın alma
                'sale',             // Satış
                'return',           // İade
                'production',       // Üretim
                'damage',           // Hasar
                'adjustment',       // Düzeltme
                'transfer',         // Transfer
                'initial',          // İlk stok
                'count',            // Sayım
                'other'             // Diğer
            ])->comment('Hareket nedeni');

            $table->string('reference_type')->nullable()->comment('Referans tipi (Order, Purchase, Transfer)');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('Referans ID (ilgili sipariş/satınalma ID)');

            // Quantity
            $table->integer('quantity')->comment('Miktar (+ veya - olabilir)');
            $table->integer('quantity_before')->default(0)->comment('Hareket öncesi stok');
            $table->integer('quantity_after')->default(0)->comment('Hareket sonrası stok');

            // Cost Info
            $table->decimal('unit_cost', 12, 2)->nullable()->comment('Birim maliyet (₺)');
            $table->decimal('total_cost', 14, 2)->nullable()->comment('Toplam maliyet (₺)');

            // Transfer Info (if movement_type = transfer)
            $table->foreignId('from_warehouse_id')->nullable()->comment('Kaynak depo ID');
            $table->foreignId('to_warehouse_id')->nullable()->comment('Hedef depo ID');

            // User Info
            $table->foreignId('created_by_user_id')->nullable()->comment('Hareketi yapan kullanıcı ID');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('warehouse_id');
            $table->index('movement_type');
            $table->index('reason');
            $table->index(['reference_type', 'reference_id']);
            $table->index(['product_id', 'created_at']);

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse hareketleri de silinir');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse hareketleri de silinir');

            $table->foreign('warehouse_id')
                  ->references('warehouse_id')
                  ->on('shop_warehouses')
                  ->onDelete('cascade')
                  ->comment('Depo silinirse ID null olur');

            $table->foreign('from_warehouse_id')
                  ->references('warehouse_id')
                  ->on('shop_warehouses')
                  ->onDelete('cascade')
                  ->comment('Kaynak depo silinirse ID null olur');

            $table->foreign('to_warehouse_id')
                  ->references('warehouse_id')
                  ->on('shop_warehouses')
                  ->onDelete('cascade')
                  ->comment('Hedef depo silinirse ID null olur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_stock_movements');
    }
};
