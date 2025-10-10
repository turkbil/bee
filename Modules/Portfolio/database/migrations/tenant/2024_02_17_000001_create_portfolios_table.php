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
            $table->foreignId('portfolio_category_id')->nullable()->constrained('portfolio_categories', 'category_id')->nullOnDelete();
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "baslik", "en": "title"}');
            $table->json('body')->nullable()->comment('Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // İlave indeksler
            $table->index('portfolio_category_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');

            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at', 'created_at'], 'portfolios_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'portfolios_active_deleted_idx');
        });

        // JSON slug arama için virtual column indexes (MySQL 8.0+ / MariaDB 10.5+)
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

            if ($supportsJsonIndex) {
                // Config'den sistem dillerini al
                $systemLanguages = config('modules.system_languages', ['tr', 'en']);

                foreach ($systemLanguages as $locale) {
                    DB::statement("
                        CREATE INDEX portfolios_slug_{$locale}_idx ON portfolios (
                            (CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255)))
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
