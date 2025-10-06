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
        Schema::connection('central')->create('ai_translation_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('module_name', 50);
            $table->string('table_name', 100);
            $table->json('translatable_fields')->comment('Çevrilecek alanlar listesi');
            $table->json('json_fields')->nullable()->comment('JSON tipindeki alanlar');
            $table->json('seo_fields')->nullable()->comment('SEO alanları mapping');
            $table->json('field_types')->comment('Alan tipleri: text, html, json');
            $table->json('max_lengths')->nullable()->comment('Alan maksimum uzunlukları');
            $table->json('special_rules')->nullable()->comment('Özel çeviri kuralları');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index'ler
            $table->unique(['module_name', 'table_name'], 'unique_module_table');
            $table->index(['is_active'], 'idx_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_translation_mappings');
    }
};