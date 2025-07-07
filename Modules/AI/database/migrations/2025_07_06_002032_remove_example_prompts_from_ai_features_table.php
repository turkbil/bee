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
            // example_prompts alanını kaldır (hiçbir yerde kullanılmıyor)
            $table->dropColumn('example_prompts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_features', function (Blueprint $table) {
            // Geri alma durumu için tekrar ekle
            $table->json('example_prompts')->nullable()->after('example_inputs');
        });
    }
};
