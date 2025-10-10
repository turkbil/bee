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
        if (Schema::hasTable('shop_categories')) {
            return;
        }

        Schema::create('shop_categories', function (Blueprint $table) {
            // Primary Key
            $table->id('category_id');

            // Hierarchy
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Üst kategori ID (null ise ana kategori)');

            // Basic Info - JSON çoklu dil
            $table->json('title')->comment('Kategori başlığı: {"tr": "Elektronik", "en": "Electronics", "vs.": "..."}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "elektronik", "en": "electronics", "vs.": "..."}');
            $table->json('description')->nullable()->comment('Kategori açıklaması: {"tr": "Açıklama metni", "en": "Description text", "vs.": "..."}');

            // Media
            $table->string('image_url')->nullable()->comment('Kategori görseli URL');
            $table->string('icon_class')->nullable()->comment('İkon sınıfı (fa-laptop, bi-phone, vb)');

            // Hierarchy Data
            $table->integer('level')->default(1)->comment('Seviye (1=Ana, 2=Alt, 3=Alt-Alt)');
            $table->string('path')->nullable()->comment('Hiyerarşik yol (1.2.5 formatında)');

            // Display Options
            $table->integer('sort_order')->default(0)->index()->comment('Sıralama düzeni');
            $table->boolean('is_active')->default(true)->index()->comment('Aktif/Pasif durumu');
            $table->boolean('show_in_menu')->default(true)->comment('Menüde göster');
            $table->boolean('show_in_homepage')->default(false)->comment('Anasayfada göster');

            // NOT: SEO ayarları Universal SEO sistemi üzerinden yönetilir (SeoManagement modülü)

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('shop_categories')
                  ->onDelete('cascade');

            // İlave indeksler
            $table->index('level');
            $table->index('path');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['is_active', 'deleted_at', 'sort_order'], 'shop_categories_active_deleted_sort_idx');
            $table->index(['parent_id', 'is_active'], 'shop_categories_parent_active_idx');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Tablo oluşturulduktan sonra
        // Dinamik olarak system_languages'dan alınır
        if (DB::getDriverName() === 'mysql') {
            $version = DB::selectOne('SELECT VERSION() as version')->version;

            // MySQL 8.0+ veya MariaDB 10.5+ kontrolü
            $isMariaDB = stripos($version, 'MariaDB') !== false;

            if ($isMariaDB) {
                // MariaDB için versiyon kontrolü (10.5+)
                preg_match('/(\d+\.\d+)/', $version, $matches);
                $mariaVersion = isset($matches[1]) ? (float) $matches[1] : 0;
                $supportsJsonIndex = false; // Disabled for MariaDB compatibility
            } else {
                // MySQL için versiyon kontrolü (8.0+)
                $majorVersion = (int) explode('.', $version)[0];
                $supportsJsonIndex = false; // Disabled for MySQL compatibility
            }

            // JSON index oluşturma devre dışı - MariaDB/MySQL syntax uyumsuzluğu
            // Gerekirse daha sonra manuel olarak oluşturulabilir
            // if ($supportsJsonIndex) {
            //     $systemLanguages = config('modules.system_languages', ['tr', 'en']);
            //     foreach ($systemLanguages as $locale) {
            //         DB::statement("ALTER TABLE shop_categories ADD INDEX shop_categories_slug_{$locale} ((CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255))))");
            //     }
            // }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_categories');
    }
};
