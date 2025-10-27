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
        if (Schema::hasTable('shop_price_lists')) {
            return;
        }

        Schema::create('shop_price_lists', function (Blueprint $table) {
            // Primary Key
            $table->id('price_list_id');

            // Basic Info
            $table->json('title')->comment('Fiyat listesi adı ({"tr":"VIP Müşteri Fiyatları","en":"VIP Customer Prices"})');
            $table->string('code')->unique()->comment('Fiyat listesi kodu (VIP-2024)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Price List Type
            $table->enum('price_list_type', ['standard', 'promotional', 'seasonal', 'customer_group', 'dealer', 'wholesale', 'retail'])
                  ->default('standard')
                  ->comment('Fiyat listesi tipi: standard=Standart, promotional=Promosyon, seasonal=Sezonluk, customer_group=Müşteri grubu, dealer=Bayi, wholesale=Toptan, retail=Perakende');

            // Target Audience
            $table->enum('target_type', ['all', 'customer_group', 'customer', 'region', 'custom'])
                  ->default('all')
                  ->comment('Hedef kitle: all=Herkes, customer_group=Müşteri grubu, customer=Belirli müşteriler, region=Bölge, custom=Özel');

            $table->json('target_ids')->nullable()->comment('Hedef ID\'ler (JSON array - customer_group_ids veya customer_ids)');

            // Currency & Base Price
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');
            $table->enum('price_calculation', ['fixed', 'markup', 'discount'])
                  ->default('fixed')
                  ->comment('Fiyat hesaplama: fixed=Sabit fiyat, markup=Artış, discount=İndirim');

            $table->decimal('markup_percentage', 5, 2)->default(0)->comment('Artış yüzdesi (%) - base_price üzerine');
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('İndirim yüzdesi (%) - base_price üzerinden');

            // Validity Period
            $table->timestamp('valid_from')->nullable()->comment('Geçerlilik başlangıç tarihi');
            $table->timestamp('valid_until')->nullable()->comment('Geçerlilik bitiş tarihi');

            // Priority (multiple price lists için hangisi uygulanacak)
            $table->integer('priority')->default(0)->comment('Öncelik (yüksek değer önce uygulanır)');

            // Tax Settings
            $table->boolean('prices_include_tax')->default(false)->comment('Fiyatlar vergi dahil mi?');

            // Minimum Order
            $table->decimal('minimum_order_amount', 12, 2)->nullable()->comment('Minimum sipariş tutarı (₺)');
            $table->integer('minimum_order_quantity')->nullable()->comment('Minimum sipariş adedi');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_default')->default(false)->comment('Varsayılan fiyat listesi mi?');

            // Additional Info
            $table->text('terms')->nullable()->comment('Şartlar ve koşullar');
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('code', 'idx_code');
            $table->index('price_list_type', 'idx_type');
            $table->index('target_type', 'idx_target_type');
            $table->index('is_active', 'idx_active');
            $table->index('is_default', 'idx_default');
            $table->index('priority', 'idx_priority');
            $table->index(['valid_from', 'valid_until'], 'idx_validity');
        })
        ->comment('Fiyat listeleri - Farklı müşteri grupları ve kampanyalar için fiyat tanımları');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_price_lists');
    }
};
