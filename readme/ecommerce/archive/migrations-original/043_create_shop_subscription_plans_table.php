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
        if (Schema::hasTable('shop_subscription_plans')) {
            return;
        }

        Schema::create('shop_subscription_plans', function (Blueprint $table) {
            // Primary Key
            $table->id('subscription_plan_id');

            // Basic Info
            $table->json('title')->comment('Plan adı ({"tr":"Altın Paket","en":"Gold Package"})');
            $table->json('slug')->comment('URL-dostu slug (altin-paket)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');
            $table->json('features')->nullable()->comment('Özellikler listesi (JSON array çoklu dil)');

            // Pricing
            $table->decimal('price_daily', 10, 2)->nullable()->comment('Günlük fiyat (₺)');
            $table->decimal('price_weekly', 10, 2)->nullable()->comment('Haftalık fiyat (₺)');
            $table->decimal('price_monthly', 12, 2)->nullable()->comment('Aylık fiyat (₺)');
            $table->decimal('price_quarterly', 12, 2)->nullable()->comment('3 aylık fiyat (₺)');
            $table->decimal('price_yearly', 12, 2)->nullable()->comment('Yıllık fiyat (₺)');
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Trial Settings
            $table->boolean('has_trial')->default(false)->comment('Deneme süresi var mı?');
            $table->integer('trial_days')->default(0)->comment('Deneme süresi (gün)');
            $table->boolean('requires_payment_method')->default(true)->comment('Deneme için ödeme yöntemi gerekli mi?');

            // Limits & Quotas
            $table->integer('max_products')->nullable()->comment('Maximum ürün sayısı (null ise sınırsız)');
            $table->integer('max_orders')->nullable()->comment('Aylık maximum sipariş sayısı');
            $table->integer('max_storage_mb')->nullable()->comment('Depolama alanı (MB)');
            $table->json('custom_limits')->nullable()->comment('Özel limitler (JSON)');

            // Features
            $table->boolean('has_analytics')->default(false)->comment('Analitik var mı?');
            $table->boolean('has_priority_support')->default(false)->comment('Öncelikli destek var mı?');
            $table->boolean('has_api_access')->default(false)->comment('API erişimi var mı?');
            $table->json('enabled_features')->nullable()->comment('Aktif özellikler (JSON array)');

            // Billing
            $table->enum('default_billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
                  ->default('monthly')
                  ->comment('Varsayılan faturalama döngüsü');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan plan mı?');
            $table->boolean('is_popular')->default(false)->comment('Popüler plan mı?');
            $table->string('badge_text')->nullable()->comment('Rozet metni (En Popüler, Önerilen)');
            $table->string('highlight_color', 7)->nullable()->comment('Vurgu rengi (#FF5733)');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_public')->default(true)->comment('Herkese açık mı?');

            // Statistics
            $table->integer('subscribers_count')->default(0)->comment('Abone sayısı (cache)');

            // Additional Info
            $table->text('terms')->nullable()->comment('Kullanım şartları');
            $table->text('notes')->nullable()->comment('Admin notları');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('slug', 'idx_slug');
            $table->index('is_active', 'idx_active');
            $table->index('is_public', 'idx_public');
            $table->index('is_featured', 'idx_featured');
            $table->index('sort_order', 'idx_sort');

        })
        ->comment('Abonelik planları - Farklı abonelik paketleri (Temel, Profesyonel, Kurumsal)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_subscription_plans');
    }
};
