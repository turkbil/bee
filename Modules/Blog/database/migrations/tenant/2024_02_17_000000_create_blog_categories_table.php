<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Kategori", "en": "Category"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "kategori", "en": "category"}');
            $table->json('description')->nullable()->comment('Çoklu dil açıklama: {"tr": "Açıklama", "en": "Description"}');
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('parent_id')->references('category_id')->on('blog_categories')->onDelete('cascade');

            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['is_active', 'deleted_at', 'sort_order'], 'blog_categories_active_deleted_sort_idx');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Tablo oluşturulduktan sonra
        // Dinamik olarak system_languages'dan alınır
        if (DB::getDriverName() === 'mysql') {
            try {
                $versionResult = DB::selectOne('SELECT VERSION() as version');

                if ($versionResult && isset($versionResult->version)) {
                    $version = $versionResult->version;

                    // MySQL 8.0+ veya MariaDB 10.5+ kontrolü
                    $isMariaDB = stripos($version, 'MariaDB') !== false;

                    if ($isMariaDB) {
                        // MariaDB için versiyon kontrolü (10.5+)
                        preg_match('/(\d+\.\d+)/', $version, $matches);
                        $mariaVersion = isset($matches[1]) ? (float) $matches[1] : 0;
                        $supportsJsonIndex = $mariaVersion >= 10.5;
                    } else {
                        // MySQL için versiyon kontrolü (8.0+)
                        $majorVersion = (int) explode('.', $version)[0];
                        $supportsJsonIndex = $majorVersion >= 8;
                    }

                    if ($supportsJsonIndex) {
                        // Config'den sistem dillerini al - SABİT DİL KODU YOK!
                        $systemLanguages = config('modules.system_languages');

                        if (!empty($systemLanguages) && is_array($systemLanguages)) {
                            foreach ($systemLanguages as $locale) {
                                DB::statement("
                                    ALTER TABLE blog_categories
                                    ADD INDEX blog_categories_slug_{$locale} (
                                        (CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255)) COLLATE utf8mb4_unicode_ci)
                                    )
                                ");
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Pretend modda veya hata durumunda skip et
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
