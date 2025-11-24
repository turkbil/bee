<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menus')) {
            return;
        }

        Schema::create('menus', function (Blueprint $table) {
            $table->id('menu_id');
            $table->json('name')->comment('Çoklu dil menü adı: {"tr": "Ana Menü", "en": "Main Menu"}');
            $table->string('slug')->unique()->comment('SEF URL için: header-menu, footer-menu');
            $table->string('location')->default('header')->comment('Menü konumu: header, footer, sidebar');
            $table->boolean('is_default')->default(false)->comment('Ana menü koruması için');
            $table->boolean('is_active')->default(true)->index();
            $table->json('settings')->nullable()->comment('Menü ayarları: {"css_class": "navbar", "max_depth": 3}');
            $table->timestamps();
            $table->softDeletes(); // Eklenen
            
            // İndeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index(['is_active', 'location']);
            $table->index(['is_default', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};