<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TENANT DİLLERİ (Frontend/İçerik Dilleri)
        Schema::create('tenant_languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10); // tr, en, de, fr, es, vs...
            $table->string('name'); // Turkish, English, German, French
            $table->string('native_name'); // Türkçe, English, Deutsch, Français
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->string('flag_icon')->nullable(); // flag emoji
            $table->boolean('is_active')->default(true)->comment('1=Sitede gözükür, 0=Sadece admin panelde hazırlık');
            $table->boolean('is_visible')->default(true)->comment('3 SEVİYELİ DİL SİSTEMİ: false=Hiçbir yerde gözükmeyen dünya dilleri, true=Admin panelde en azından görünen');
            $table->boolean('is_main_language')->default(true)->comment('Ana dil kategorisi mi? (visible=false olanlar için)');
            $table->boolean('is_default')->default(false); // RTL eklentisinden
            $table->boolean('is_rtl')->default(false);
            $table->string('flag_emoji', 10)->nullable();
            // is_default kaldırıldı - artık tenants.tenant_default_locale'de tutuluyor
            $table->enum('url_prefix_mode', ['none', 'except_default', 'all'])
                ->default('except_default')
                ->comment('URL prefix strategy: none=no prefix, except_default=prefix except default lang, all=prefix for all');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Her tenant kendi dil kodlarını yönetir
            $table->unique('code'); // Her tenant'ta kod unique
            $table->index(['code', 'is_active']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_languages');
    }
};