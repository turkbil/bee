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
        if (Schema::hasTable('shop_shipping_methods')) {
            return;
        }

        Schema::create('shop_shipping_methods', function (Blueprint $table) {
            // Primary Key
            $table->id('shipping_method_id');

            // Basic Info
            $table->json('title')->comment('Kargo yöntemi adı ({"tr":"MNG Kargo","en":"MNG Cargo"})');
            $table->json('slug')->comment('URL-dostu slug (mng-kargo)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Carrier Info
            $table->string('carrier_code')->nullable()->comment('Kargo firma kodu (MNG, YURTICI, ARAS, UPS, DHL)');
            $table->string('tracking_url_template')->nullable()->comment('Takip URL şablonu ({tracking_number} değişkeni ile)');

            // Shipping Type
            $table->enum('shipping_type', [
                'standard',         // Standart kargo
                'express',          // Hızlı kargo
                'same_day',         // Aynı gün teslimat
                'pickup',           // Mağazadan teslim alma
                'freight',          // Ağır nakliye (forklift için)
                'international',    // Uluslararası
                'custom'            // Özel teslimat
            ])->default('standard')->comment('Kargo tipi');

            // Pricing
            $table->enum('pricing_type', ['flat_rate', 'weight_based', 'price_based', 'zone_based', 'free', 'custom'])
                  ->default('flat_rate')
                  ->comment('Fiyatlama tipi: flat_rate=Sabit, weight_based=Ağırlığa göre, price_based=Tutara göre, zone_based=Bölgeye göre, free=Ücretsiz, custom=Özel');

            $table->decimal('base_cost', 10, 2)->default(0)->comment('Temel maliyet (₺) - flat_rate için');
            $table->decimal('cost_per_kg', 8, 2)->default(0)->comment('Kg başına ücret (₺) - weight_based için');
            $table->decimal('free_shipping_threshold', 12, 2)->nullable()->comment('Ücretsiz kargo minimum tutar (₺)');

            // Pricing Rules (JSON)
            $table->json('pricing_rules')->nullable()->comment('Fiyatlama kuralları (JSON - [{\"min_weight\":0,\"max_weight\":5,\"cost\":25}])');
            $table->json('zone_pricing')->nullable()->comment('Bölge bazlı fiyatlar (JSON - [{\"zones\":[\"İstanbul\",\"Ankara\"],\"cost\":30}])');

            // Limits
            $table->decimal('min_order_amount', 12, 2)->nullable()->comment('Minimum sipariş tutarı (₺)');
            $table->decimal('max_order_amount', 14, 2)->nullable()->comment('Maximum sipariş tutarı (₺)');
            $table->decimal('max_weight', 10, 2)->nullable()->comment('Maximum ağırlık (kg)');
            $table->json('restricted_postal_codes')->nullable()->comment('Kısıtlı posta kodları (JSON array)');

            // Delivery Time
            $table->integer('estimated_days_min')->nullable()->comment('Minimum teslimat süresi (gün)');
            $table->integer('estimated_days_max')->nullable()->comment('Maximum teslimat süresi (gün)');
            $table->json('delivery_time_text')->nullable()->comment('Teslimat süresi metni (JSON - {"tr":"2-3 iş günü"})');

            // Geographic Coverage
            $table->boolean('domestic_only')->default(true)->comment('Sadece yurt içi mi?');
            $table->json('supported_countries')->nullable()->comment('Desteklenen ülkeler (JSON - ["TR","DE","FR"])');
            $table->json('supported_cities')->nullable()->comment('Desteklenen şehirler (JSON - ["İstanbul","Ankara"])');

            // Display
            $table->string('icon')->nullable()->comment('İkon dosya yolu veya sınıfı');
            $table->string('logo_url')->nullable()->comment('Logo URL');
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Status & Rules
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('requires_address')->default(true)->comment('Adres gerektirir mi?');
            $table->boolean('requires_phone')->default(true)->comment('Telefon gerektirir mi?');

            // Tax
            $table->boolean('is_taxable')->default(false)->comment('Kargo ücreti vergiye tabi mi?');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Vergi oranı (%)');

            // API Integration
            $table->json('api_config')->nullable()->comment('API ayarları (JSON - API keys, endpoints, vb)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('slug', 'idx_slug');
            $table->index('carrier_code', 'idx_carrier');
            $table->index('shipping_type', 'idx_type');
            $table->index('is_active', 'idx_active');
            $table->index('sort_order', 'idx_sort');
        })
        ->comment('Kargo yöntemleri - MNG, Yurtiçi, Aras, özel nakliye vb.');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_shipping_methods');
    }
};
