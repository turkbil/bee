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
        if (Schema::hasTable('shop_search_logs')) {
            return;
        }

        Schema::create('shop_search_logs', function (Blueprint $table) {
            // Primary Key
            $table->id('search_log_id');

            // Search Query
            $table->string('search_term')->comment('Arama terimi');
            $table->string('normalized_term')->nullable()->comment('Normalize edilmiş terim (lowercase, trim)');

            // User Info
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');
            $table->string('session_id')->nullable()->comment('Oturum ID');

            // Results
            $table->integer('results_count')->default(0)->comment('Sonuç sayısı');
            $table->boolean('has_results')->default(true)->comment('Sonuç bulundu mu?');

            // Filters Applied
            $table->json('filters_applied')->nullable()->comment('Uygulanan filtreler (JSON)');
            $table->string('sort_by')->nullable()->comment('Sıralama (price_asc, popular, newest)');

            // Interaction
            $table->boolean('clicked_result')->default(false)->comment('Sonuçlara tıklandı mı?');
            $table->foreignId('clicked_product_id')->nullable()->comment('Tıklanan ürün ID');
            $table->integer('clicked_position')->nullable()->comment('Tıklanan sonucun pozisyonu');

            // Conversion
            $table->boolean('converted_to_cart')->default(false)->comment('Sepete ekleme yapıldı mı?');
            $table->boolean('converted_to_purchase')->default(false)->comment('Satın alma yapıldı mı?');

            // Device & Location
            $table->string('device_type')->nullable()->comment('Cihaz tipi (desktop, mobile, tablet)');
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('search_term', 'idx_term');
            $table->index('normalized_term', 'idx_normalized');
            $table->index('customer_id', 'idx_customer');
            $table->index('session_id', 'idx_session');
            $table->index('has_results', 'idx_has_results');
            $table->index('created_at', 'idx_created');
            $table->index(['search_term', 'created_at'], 'idx_term_date');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur');

            $table->foreign('clicked_product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse ID null olur');
        })
        ->comment('Arama logları - Kullanıcı arama sorguları ve istatistikleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_search_logs');
    }
};
