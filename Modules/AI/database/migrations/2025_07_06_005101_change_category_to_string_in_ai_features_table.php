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
        Schema::table('ai_features', function (Blueprint $table) {
            // Category enum'unu string'e çevir
            $table->string('category', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_features', function (Blueprint $table) {
            // Geri dönüş için enum'a çevir (opsiyonel)
            $table->enum('category', [
                'content-creation','seo-tools','translation','web-editor',
                'content-analysis','marketing','creative','business',
                'technical','education','health','legal','finance',
                'travel','food','sports','technology','entertainment','other'
            ])->change();
        });
    }
};
