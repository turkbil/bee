<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tenant bağlamında çalışıyorsa, migration'ı çalıştırma
        if (config('database.default') !== config('tenancy.database.central_connection')) {
            return;
        }
        Schema::create('themes', function (Blueprint $table) {
            $table->id('theme_id');
            $table->string('name')->unique();              // Tema kod adı (default, kirmizi, mavi)
            $table->string('title');                       // Görünen tema adı  
            $table->string('slug')->unique();              // SEO dostu URL için slug
            $table->string('folder_name')->unique();       // Tema dosyalarının dizin adı
            $table->text('description')->nullable();       // Tema açıklaması
            $table->boolean('is_active')->default(true);   // Temanın aktif olup olmadığı
            $table->boolean('is_default')->default(false); // Varsayılan tema mı
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('is_default');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_default', 'is_active'], 'themes_default_active_idx');
            $table->index(['name', 'is_active'], 'themes_name_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};