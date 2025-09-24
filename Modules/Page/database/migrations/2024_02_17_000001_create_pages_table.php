<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id('page_id');
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "baslik", "en": "title"}');
            $table->json('body')->nullable()->comment('Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}');
            $table->text('css')->nullable()->comment('CSS kodu - tüm dillerde ortak');
            $table->text('js')->nullable()->comment('JavaScript kodu - tüm dillerde ortak');
            // NOT: Eski string formatındaki veriler convert_pages_body_to_json.php ile JSON'a dönüştürülür
            $table->json('seo')->nullable()->comment('SEO verileri: {"tr": {"meta_title": "Başlık", "meta_description": "Açıklama", "keywords": [], "og_image": "image.jpg"}}');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_homepage')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // JSON kolonlar için özel indeksler oluşturulamaz, ancak sanal kolonlar kullanılabilir
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_homepage', 'is_active', 'deleted_at'], 'pages_homepage_active_deleted_idx');
            $table->index(['is_homepage', 'deleted_at', 'is_active'], 'pages_homepage_deleted_active_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'pages_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'pages_active_deleted_idx');
            
            // JSON slug arama için virtual column index (MySQL 5.7+)
            // $table->rawIndex('(CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, "$.tr")) AS CHAR(255)))', 'pages_slug_tr_idx');
            // $table->rawIndex('(CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, "$.en")) AS CHAR(255)))', 'pages_slug_en_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};