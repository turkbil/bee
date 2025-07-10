<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI Profile Sectors tablosunu yeniden oluştur
     * category_id sistemi ile parent-child ilişkisi
     */
    public function up(): void
    {
        // Önce mevcut tabloyu tamamen sil
        Schema::dropIfExists('ai_profile_sectors');
        
        // Yeni tablo yapısını oluştur
        Schema::create('ai_profile_sectors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Sektör benzersiz kodu');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Ana kategori ID (null=ana kategori)');
            $table->string('name')->comment('Sektör/kategori adı');
            $table->string('icon')->nullable()->comment('Sektör ikonu (CSS class)');
            $table->string('emoji')->nullable()->comment('Sektör emojisi');
            $table->string('color')->default('blue')->comment('Sektör rengi');
            $table->text('description')->nullable()->comment('Sektör açıklaması');
            $table->text('keywords')->nullable()->comment('Arama anahtar kelimeleri');
            $table->boolean('is_active')->default(true)->comment('Aktif mi?');
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('category_id')->references('id')->on('ai_profile_sectors')->onDelete('cascade');
            
            // Indexes
            $table->index(['is_active', 'category_id']);
            $table->index(['category_id', 'sort_order']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_profile_sectors');
    }
};
