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
        if (Schema::hasTable('shop_campaigns')) {
            return;
        }

        Schema::create('shop_campaigns', function (Blueprint $table) {
            $table->comment('Kampanyalar - İndirim kampanyaları, flaş satışlar, sezonluk kampanyalar');

            // Primary Key
            $table->id('campaign_id');

            // Basic Info
            $table->json('title')->comment('Kampanya adı (JSON çoklu dil)');
            $table->string('slug')->unique()->comment('URL-dostu slug');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Campaign Type
            $table->enum('campaign_type', [
                'discount',         // İndirim
                'bogo',            // Al 1 Öde 1
                'bundle',          // Paket
                'gift',            // Hediye
                'flash_sale',      // Flaş indirim
                'clearance',       // Stok tasfiyesi
                'seasonal'         // Sezonluk
            ])->default('discount')->comment('Kampanya tipi');

            // Discount Settings
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('İndirim yüzdesi (%)');
            $table->decimal('discount_amount', 12, 2)->nullable()->comment('İndirim tutarı (₺)');

            // Scope
            $table->enum('applies_to', ['all', 'categories', 'products', 'brands'])
                  ->default('all')
                  ->comment('Nerelere uygulanır');

            $table->json('category_ids')->nullable()->comment('Kategori ID\'leri (JSON array)');
            $table->json('product_ids')->nullable()->comment('Ürün ID\'leri (JSON array)');
            $table->json('brand_ids')->nullable()->comment('Marka ID\'leri (JSON array)');

            // Conditions
            $table->decimal('minimum_order_amount', 12, 2)->nullable()->comment('Minimum sipariş tutarı (₺)');
            $table->integer('minimum_items')->nullable()->comment('Minimum ürün adedi');

            // Validity Period
            $table->timestamp('start_date')->nullable()->comment('Başlangıç tarihi');
            $table->timestamp('end_date')->nullable()->comment('Bitiş tarihi');

            // Usage Limits
            $table->integer('usage_limit_total')->nullable()->comment('Toplam kullanım limiti');
            $table->integer('usage_limit_per_customer')->nullable()->comment('Müşteri başına limit');
            $table->integer('used_count')->default(0)->comment('Kullanım sayısı');

            // Display
            $table->string('badge_text')->nullable()->comment('Rozet metni (Kampanya, %50 İndirim)');
            $table->string('badge_color', 7)->nullable()->comment('Rozet rengi (#FF5733)');
            $table->string('banner_image')->nullable()->comment('Banner görseli');

            // Priority
            $table->integer('priority')->default(0)->comment('Öncelik (yüksek değer önce uygulanır)');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan kampanya mı?');

            // NOT: SEO ayarları Universal SEO sistemi üzerinden yönetilir (SeoManagement modülü)

            // Statistics
            $table->integer('view_count')->default(0)->comment('Görüntülenme sayısı');
            $table->decimal('total_sales', 14, 2)->default(0)->comment('Toplam satış (₺)');

            // Additional Info
            $table->text('terms')->nullable()->comment('Kullanım şartları');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('slug');
            $table->index('campaign_type');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('priority');
            $table->index(['start_date', 'end_date']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_campaigns');
    }
};
