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
        if (Schema::hasTable('shop_banners')) {
            return;
        }

        Schema::create('shop_banners', function (Blueprint $table) {
            // Primary Key
            $table->id('banner_id');

            // Basic Info
            $table->json('title')->comment('Banner başlığı (JSON çoklu dil)');
            $table->json('subtitle')->nullable()->comment('Alt başlık (JSON çoklu dil)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Banner Type
            $table->enum('banner_type', ['slider', 'hero', 'promotional', 'category', 'popup'])
                  ->default('slider')
                  ->comment('Banner tipi: slider=Slider, hero=Hero, promotional=Promosyon, category=Kategori, popup=Popup');

            // Position
            $table->enum('position', ['home_slider', 'home_top', 'home_middle', 'home_bottom', 'category_top', 'product_sidebar', 'checkout'])
                  ->default('home_slider')
                  ->comment('Konum');

            // Images
            $table->string('image_desktop')->nullable()->comment('Masaüstü görsel');
            $table->string('image_tablet')->nullable()->comment('Tablet görsel');
            $table->string('image_mobile')->nullable()->comment('Mobil görsel');

            // Link
            $table->string('link_url')->nullable()->comment('Link URL');
            $table->boolean('link_new_tab')->default(false)->comment('Yeni sekmede açılsın mı?');
            $table->json('button_text')->nullable()->comment('Buton metni (JSON çoklu dil)');

            // Display Settings
            $table->string('background_color', 7)->nullable()->comment('Arkaplan rengi (#FF5733)');
            $table->string('text_color', 7)->nullable()->comment('Metin rengi (#FFFFFF)');
            $table->enum('text_align', ['left', 'center', 'right'])->default('left')->comment('Metin hizalama');

            // Schedule
            $table->timestamp('start_date')->nullable()->comment('Başlangıç tarihi');
            $table->timestamp('end_date')->nullable()->comment('Bitiş tarihi');

            // Target Audience
            $table->json('customer_group_ids')->nullable()->comment('Hedef müşteri grupları (JSON array)');
            $table->json('show_on_pages')->nullable()->comment('Gösterilecek sayfalar (JSON array - ["home","category","product"])');

            // Display Rules
            $table->integer('display_order')->default(0)->comment('Gösterim sırası');
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Animation
            $table->string('animation_type')->nullable()->comment('Animasyon tipi (fade, slide, zoom)');
            $table->integer('animation_duration')->default(5000)->comment('Animasyon süresi (ms)');

            // Statistics
            $table->integer('view_count')->default(0)->comment('Görüntülenme sayısı');
            $table->integer('click_count')->default(0)->comment('Tıklanma sayısı');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('banner_type', 'idx_type');
            $table->index('position', 'idx_position');
            $table->index('is_active', 'idx_active');
            $table->index('display_order', 'idx_order');
            $table->index(['start_date', 'end_date'], 'idx_schedule');

        })
        ->comment('Bannerlar - Slider, hero, promosyon bannerları');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_banners');
    }
};
