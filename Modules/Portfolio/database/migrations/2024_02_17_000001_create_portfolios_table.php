<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id('portfolio_id');
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "baslik", "en": "title"}');
            $table->json('body')->nullable()->comment('Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');

            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at', 'created_at'], 'portfolios_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'portfolios_active_deleted_idx');
        });

        // JSON slug arama için virtual column indexes (MySQL 8.0+)
        // Dinamik olarak system_languages'dan alınır
        if (DB::getDriverName() === 'mysql') {
            $mysqlVersion = DB::selectOne('SELECT VERSION() as version')->version;
            $majorVersion = (int) explode('.', $mysqlVersion)[0];

            if ($majorVersion >= 8) {
                // Config'den sistem dillerini al
                $systemLanguages = config('modules.system_languages', ['tr', 'en']);

                foreach ($systemLanguages as $locale) {
                    DB::statement("
                        ALTER TABLE portfolios
                        ADD INDEX portfolios_slug_{$locale} (
                            (CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255)) COLLATE utf8mb4_unicode_ci)
                        )
                    ");
                }
            }
        }
    }

    public function down(): void
    {
        // Virtual column indexes otomatik drop olur
        Schema::dropIfExists('portfolios');
    }
};