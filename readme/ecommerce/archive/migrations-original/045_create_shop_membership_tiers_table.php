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
        if (Schema::hasTable('shop_membership_tiers')) {
            return;
        }

        Schema::create('shop_membership_tiers', function (Blueprint $table) {
            // Primary Key
            $table->id('membership_tier_id');

            // Basic Info
            $table->json('title')->comment('Seviye adı ({"tr":"Bronz Üye","en":"Bronze Member"})');
            $table->json('slug')->comment('URL-dostu slug (bronz-uye)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Tier Level
            $table->integer('level')->unique()->comment('Seviye (1=Bronz, 2=Gümüş, 3=Altın, 4=Platin)');

            // Requirements (Nasıl ulaşılır)
            $table->decimal('minimum_spent', 14, 2)->default(0)->comment('Minimum harcama (₺)');
            $table->integer('minimum_orders')->default(0)->comment('Minimum sipariş sayısı');
            $table->integer('required_points')->default(0)->comment('Gerekli puan');

            // Benefits (Avantajlar)
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('İndirim yüzdesi (%)');
            $table->boolean('free_shipping')->default(false)->comment('Ücretsiz kargo');
            $table->decimal('cashback_percentage', 5, 2)->default(0)->comment('Cashback yüzdesi (%)');
            $table->decimal('points_multiplier', 5, 2)->default(1)->comment('Puan çarpanı (1.5 = %50 fazla puan)');

            // Early Access
            $table->boolean('early_access_sales')->default(false)->comment('Kampanyalara erken erişim');
            $table->boolean('exclusive_products')->default(false)->comment('Özel ürünlere erişim');
            $table->boolean('priority_support')->default(false)->comment('Öncelikli destek');

            // Validity
            $table->integer('validity_days')->nullable()->comment('Geçerlilik süresi (gün) - null ise süresiz');

            // Display
            $table->string('icon')->nullable()->comment('İkon dosya yolu veya sınıfı');
            $table->string('badge_image')->nullable()->comment('Rozet görseli');
            $table->string('color_code', 7)->nullable()->comment('Renk kodu (#FF5733)');
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Statistics
            $table->integer('members_count')->default(0)->comment('Üye sayısı (cache)');

            // Additional Info
            $table->json('features')->nullable()->comment('Özellikler listesi (JSON array çoklu dil)');
            $table->text('terms')->nullable()->comment('Kullanım şartları');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('slug', 'idx_slug');
            $table->index('level', 'idx_level');
            $table->index('is_active', 'idx_active');
            $table->index('minimum_spent', 'idx_min_spent');

        })
        ->comment('Üyelik seviyeleri - Bronz, Gümüş, Altın, Platin gibi sadakat seviyeleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_membership_tiers');
    }
};
