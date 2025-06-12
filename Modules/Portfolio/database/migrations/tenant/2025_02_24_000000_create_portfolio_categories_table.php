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
            $table->string('title', 255);                     // Kategori başlığı (255 karakter)
            $table->string('slug')->unique();                 // URL slug (benzersiz)
            $table->text('body')->nullable();                 // Kategori açıklaması (text olarak)
            $table->integer('order')->default(0);             // Sıralama (varsayılan: 0)
            $table->string('metakey', 255)->nullable();       // Meta anahtar kelimeler (255 karakter)
            $table->string('metadesc', 255)->nullable();      // Meta açıklama (255 karakter)
            $table->boolean('is_active')->default(true);      // Aktiflik durumu (varsayılan: true)
            $table->timestamps();                             // created_at ve updated_at
            $table->softDeletes();                            // deleted_at (yumuşak silme)
            
            // İlave indeksler
            $table->index('title');
            $table->index('order');
            $table->index('is_active');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at'], 'portfolio_categories_active_deleted_idx');
            $table->index(['is_active', 'deleted_at', 'order'], 'portfolio_categories_active_deleted_order_idx');
            $table->index(['slug', 'is_active', 'deleted_at'], 'portfolio_categories_slug_active_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_categories');
    }
};