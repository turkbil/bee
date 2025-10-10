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
        if (Schema::hasTable('shop_analytics')) {
            return;
        }

        Schema::create('shop_analytics', function (Blueprint $table) {
            // Primary Key
            $table->id('analytic_id');

            // Event Type
            $table->enum('event_type', [
                'page_view',        // Sayfa görüntüleme
                'product_view',     // Ürün görüntüleme
                'add_to_cart',      // Sepete ekleme
                'remove_from_cart', // Sepetten çıkarma
                'add_to_wishlist',  // Favorilere ekleme
                'search',           // Arama
                'filter',           // Filtreleme
                'click',            // Tıklama
                'purchase',         // Satın alma
                'custom'            // Özel
            ])->comment('Olay tipi');

            // Related Entity
            $table->string('entity_type')->nullable()->comment('İlgili entity tipi (Product, Category, Page)');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('İlgili entity ID');

            // User Info
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');
            $table->string('session_id')->nullable()->comment('Oturum ID');

            // Event Data
            $table->string('event_value')->nullable()->comment('Olay değeri (search query, clicked element)');
            $table->json('event_data')->nullable()->comment('Olay verileri (JSON)');

            // Page Info
            $table->string('page_url')->nullable()->comment('Sayfa URL');
            $table->string('referrer_url')->nullable()->comment('Yönlendiren URL');

            // Device & Location
            $table->string('device_type')->nullable()->comment('Cihaz tipi (desktop, mobile, tablet)');
            $table->string('browser')->nullable()->comment('Tarayıcı');
            $table->string('os')->nullable()->comment('İşletim sistemi');
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');
            $table->string('country_code', 2)->nullable()->comment('Ülke kodu');
            $table->string('city')->nullable()->comment('Şehir');

            // UTM Parameters
            $table->string('utm_source')->nullable()->comment('UTM kaynak');
            $table->string('utm_medium')->nullable()->comment('UTM medyum');
            $table->string('utm_campaign')->nullable()->comment('UTM kampanya');
            $table->string('utm_term')->nullable()->comment('UTM terim');
            $table->string('utm_content')->nullable()->comment('UTM içerik');

            // Conversion
            $table->boolean('converted')->default(false)->comment('Dönüştü mü? (satın alma yapıldı mı)');
            $table->decimal('conversion_value', 12, 2)->nullable()->comment('Dönüşüm değeri (₺)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('event_type', 'idx_event_type');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index('customer_id', 'idx_customer');
            $table->index('session_id', 'idx_session');
            $table->index('created_at', 'idx_created');
            $table->index(['event_type', 'created_at'], 'idx_event_date');
            $table->index('device_type', 'idx_device');
            $table->index('utm_campaign', 'idx_utm_campaign');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama analitik kalır');
        })
        ->comment('Analitikler - Kullanıcı davranışları ve olay takibi');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_analytics');
    }
};
