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
        if (Schema::hasTable('shop_wishlists')) {
            return;
        }

        Schema::create('shop_wishlists', function (Blueprint $table) {
            // Primary Key
            $table->id('wishlist_id');

            // Relations
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi (null ise misafir)');
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');

            // Guest Session (for non-logged users)
            $table->string('session_id')->nullable()->comment('Oturum ID (misafir kullanıcılar için)');

            // Wishlist Type
            $table->enum('wishlist_type', ['default', 'cart_later', 'compare', 'gift'])
                  ->default('default')
                  ->comment('Liste tipi: default=Favoriler, cart_later=Sonra al, compare=Karşılaştır, gift=Hediye listesi');

            // Price Alert
            $table->boolean('price_alert_enabled')->default(false)->comment('Fiyat düşünce bildirim gönderilsin mi?');
            $table->decimal('target_price', 12, 2)->nullable()->comment('Hedef fiyat (₺) - bu fiyata düşünce bildir');

            // Stock Alert
            $table->boolean('stock_alert_enabled')->default(false)->comment('Stok gelince bildirim gönderilsin mi?');

            // Notes
            $table->text('notes')->nullable()->comment('Kullanıcı notları');

            // Priority
            $table->integer('priority')->default(0)->comment('Öncelik (kullanıcının kendi sıralaması için)');

            // Share Settings
            $table->boolean('is_public')->default(false)->comment('Herkese açık mı?');
            $table->string('share_token')->nullable()->unique()->comment('Paylaşım token (link ile paylaşım için)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');
            $table->index('session_id', 'idx_session');
            $table->index('wishlist_type', 'idx_type');
            $table->index('share_token', 'idx_share_token');
            $table->index(['customer_id', 'product_id'], 'idx_customer_product');
            $table->index(['session_id', 'product_id'], 'idx_session_product');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse favori listesi de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse favorilerden de silinir');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse favorilerden de silinir');
        })
        ->comment('Favori listesi - Müşterilerin beğendikleri ürünler');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_wishlists');
    }
};
