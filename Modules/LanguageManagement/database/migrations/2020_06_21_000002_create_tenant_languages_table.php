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
            $table->boolean('is_active')->default(true);
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