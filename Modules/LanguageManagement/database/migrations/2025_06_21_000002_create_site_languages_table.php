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
        // SİTE DİLLERİ (Tenant - Frontend/İçerik Dilleri)
        Schema::create('site_languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10); // tr, en, de, fr, es, vs...
            $table->string('name'); // Turkish, English, German, French
            $table->string('native_name'); // Türkçe, English, Deutsch, Français
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->string('flag_icon')->nullable(); // flag emoji
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Varsayılan site dili
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Her tenant kendi dil kodlarını yönetir (unique değil)
            $table->index(['code', 'is_active']);
            $table->index(['is_default']);
            $table->index(['sort_order']);
            
            // Sadece 1 varsayılan dil olabilir
            $table->unique(['is_default'], 'site_languages_unique_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_languages');
    }
};