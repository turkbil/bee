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
        // ADMİN PANEL DİLLERİ (Central)
        Schema::create('admin_languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // tr, en
            $table->string('name'); // Turkish, English
            $table->string('native_name'); // Türkçe, English
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->string('flag_icon')->nullable(); // flag emoji
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Performans için indexler
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_languages');
    }
};