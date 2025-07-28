<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id('portfolio_category_id');              // Özel primary key
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Kategori Başlığı", "en": "Category Title", "ar": "عنوان الفئة"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "kategori-slug", "en": "category-slug", "ar": "فئة-slug"}');
            $table->json('body')->nullable()->comment('Çoklu dil içerik: {"tr": "İçerik", "en": "Content", "ar": "المحتوى"}');
            $table->integer('order')->default(0);             // Sıralama (varsayılan: 0)
            $table->boolean('is_active')->default(true);      // Aktiflik durumu (varsayılan: true)
            $table->timestamps();                             // created_at ve updated_at
            $table->softDeletes();                            // deleted_at (yumuşak silme)
            
            // İlave indeksler
            $table->index('order');
            $table->index('is_active');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at'], 'portfolio_categories_active_deleted_idx');
            $table->index(['is_active', 'deleted_at', 'order'], 'portfolio_categories_active_deleted_order_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_categories');
    }
};