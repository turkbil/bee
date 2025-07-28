<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id');
            $table->json('title')->comment('Çoklu dil başlık: {"tr": "Duyuru Başlığı", "en": "Announcement Title", "ar": "عنوان الإعلان"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "duyuru-basligi", "en": "announcement-title", "ar": "عنوان-الإعلان"}');
            $table->json('body')->nullable()->comment('Çoklu dil içerik: {"tr": "Duyuru İçeriği", "en": "Announcement Content", "ar": "محتوى الإعلان"}');
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at'], 'announcements_active_deleted_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'announcements_active_deleted_created_idx');
            
            // JSON slug arama için virtual column index (MySQL 5.7+) - Optional
            // $table->rawIndex('(CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, "$.tr")) AS CHAR(255)))', 'announcements_slug_tr_idx');
            // $table->rawIndex('(CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, "$.en")) AS CHAR(255)))', 'announcements_slug_en_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};