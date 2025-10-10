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
        if (Schema::hasTable('shop_brands')) {
            return;
        }

        Schema::create('shop_brands', function (Blueprint $table) {
            // Primary Key
            $table->id('brand_id');

            // Basic Info - JSON çoklu dil
            $table->json('title')->comment('Marka başlığı: {"tr": "Marka Adı", "en": "Brand Name", "vs.": "..."}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "marka-adi", "en": "brand-name", "vs.": "..."}');
            $table->json('description')->nullable()->comment('Marka açıklaması: {"tr": "Açıklama metni", "en": "Description text", "vs.": "..."}');

            // Media & Links
            $table->string('logo_url')->nullable()->comment('Marka logosu URL');
            $table->string('website_url')->nullable()->comment('Resmi website URL');

            // Company Info
            $table->string('country_code', 2)->nullable()->comment('Ülke kodu (ISO 3166-1 alpha-2: TR, US, DE, vs.)');
            $table->integer('founded_year')->nullable()->comment('Kuruluş yılı');
            $table->string('headquarters')->nullable()->comment('Merkez ofis lokasyonu');

            // Certifications
            $table->json('certifications')->nullable()->comment('Sertifikalar: [{"name":"CE","year":2005}, {"name":"ISO 9001","year":2010}, ...]');

            // Display Options
            $table->boolean('is_active')->default(true)->index()->comment('Aktif/Pasif durumu');
            $table->boolean('is_featured')->default(false)->index()->comment('Öne çıkan marka');
            $table->integer('sort_order')->default(0)->index()->comment('Sıralama düzeni');

            // NOT: SEO ayarları Universal SEO sistemi üzerinden yönetilir (SeoManagement modülü)

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // İlave indeksler
            $table->index('country_code');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['is_active', 'deleted_at', 'sort_order'], 'shop_brands_active_deleted_sort_idx');
            $table->index(['is_featured', 'is_active'], 'shop_brands_featured_active_idx');
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

            // JSON index disabled - MariaDB/MySQL compatibility
            // if ($supportsJsonIndex) { ... }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_brands');
    }
};
