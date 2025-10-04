<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_categories', function (Blueprint $table) {
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
            $table->foreign('parent_id')->references('category_id')->on('portfolio_categories')->onDelete('cascade');

            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['is_active', 'deleted_at', 'sort_order'], 'portfolio_categories_active_deleted_sort_idx');
        });

        // JSON slug indexes (MySQL 8.0+) - Tablo oluşturulduktan sonra
        // Dinamik olarak system_languages'dan alınır
        if (DB::getDriverName() === 'mysql') {
            $mysqlVersion = DB::selectOne('SELECT VERSION() as version')->version;
            $majorVersion = (int) explode('.', $mysqlVersion)[0];

            if ($majorVersion >= 8) {
                // Config'den sistem dillerini al
                $systemLanguages = config('modules.system_languages', ['tr', 'en']);

                foreach ($systemLanguages as $locale) {
                    DB::statement("
                        ALTER TABLE portfolio_categories
                        ADD INDEX portfolio_categories_slug_{$locale} (
                            (CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255)) COLLATE utf8mb4_unicode_ci)
                        )
                    ");
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_categories');
    }
};
