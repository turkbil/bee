<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id('portfolio_id');
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Başlık", "en": "Title"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "baslik", "en": "title"}');
            $table->json('body')->nullable()->comment('Çoklu dil içerik: {"tr": "İçerik", "en": "Content"}');
            $table->text('css')->nullable()->comment('CSS kodu - tüm dillerde ortak');
            $table->text('js')->nullable()->comment('JavaScript kodu - tüm dillerde ortak');
            $table->json('seo')->nullable()->comment('SEO verileri: {"tr": {"meta_title": "Başlık", "meta_description": "Açıklama", "keywords": [], "og_image": "image.jpg"}}');

            // Category relation
            $table->foreignId('category_id')->nullable()
                ->constrained('portfolio_categories', 'category_id')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('category_id');

            // Composite index'ler - Performans optimizasyonu
            $table->index(['category_id', 'is_active'], 'portfolios_category_active_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'portfolios_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'portfolios_active_deleted_idx');
            $table->index(['category_id', 'is_active', 'deleted_at'], 'portfolios_category_active_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
