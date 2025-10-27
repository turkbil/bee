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
        if (Schema::hasTable('shop_tags')) {
            return;
        }

        Schema::create('shop_tags', function (Blueprint $table) {
            // Primary Key
            $table->id('tag_id');

            // Basic Info
            $table->json('title')->comment('Etiket adı ({"tr":"Yeni Ürün","en":"New Product"})');
            $table->json('slug')->comment('URL-dostu slug (yeni-urun)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Tag Type
            $table->enum('tag_type', ['feature', 'promotion', 'category', 'attribute', 'custom'])
                  ->default('custom')
                  ->comment('Etiket tipi: feature=Özellik, promotion=Promosyon, category=Kategori, attribute=Nitelik, custom=Özel');

            // Display
            $table->string('color_code', 7)->nullable()->comment('Renk kodu (#FF5733)');
            $table->string('icon_class')->nullable()->comment('İkon sınıfı (fa fa-star)');
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Badge Settings
            $table->boolean('show_as_badge')->default(true)->comment('Rozet olarak göster');
            $table->enum('badge_position', ['top-left', 'top-right', 'bottom-left', 'bottom-right'])
                  ->default('top-right')
                  ->comment('Rozet pozisyonu');

            // NOT: SEO ayarları Universal SEO sistemi üzerinden yönetilir (SeoManagement modülü)

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan etiket mi?');

            // Statistics
            $table->integer('products_count')->default(0)->comment('Ürün sayısı (cache)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('slug', 'idx_slug');
            $table->index('tag_type', 'idx_type');
            $table->index('is_active', 'idx_active');
            $table->index('is_featured', 'idx_featured');
        })
        ->comment('Etiketler - Ürün etiketleri (Yeni, İndirimli, Çok Satan vb.)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_tags');
    }
};
