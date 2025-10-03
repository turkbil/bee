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
            $table->json('name')->comment('Çoklu dil başlık: {"tr": "Kategori", "en": "Category"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "kategori", "en": "category"}');
            $table->json('description')->nullable()->comment('Çoklu dil açıklama: {"tr": "Açıklama", "en": "Description"}');
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();

            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['is_active', 'deleted_at', 'sort_order'], 'portfolio_categories_active_deleted_sort_idx');
        });

        // JSON slug indexes (MySQL 8.0+) - Tablo oluşturulduktan sonra
        DB::statement("ALTER TABLE portfolio_categories ADD INDEX portfolio_categories_slug_tr ((cast(json_unquote(json_extract(slug, '$.tr')) as char(255)) collate utf8mb4_unicode_ci))");
        DB::statement("ALTER TABLE portfolio_categories ADD INDEX portfolio_categories_slug_en ((cast(json_unquote(json_extract(slug, '$.en')) as char(255)) collate utf8mb4_unicode_ci))");
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_categories');
    }
};
