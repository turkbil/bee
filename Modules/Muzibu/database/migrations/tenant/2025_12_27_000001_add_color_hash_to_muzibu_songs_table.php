<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tenant database migration - muzibu_songs tablosuna color_hash kolonu ekler
     *
     * color_hash formatı: "hue1,hue2,hue3" (3 renkli gradient için 3 HSL hue değeri)
     * Örnek: "45,85,125" → Sarı-Yeşil-Turkuaz gradient
     */
    public function up(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->string('color_hash', 50)->nullable()->after('play_count')
                ->comment('3 renkli gradient için HSL hue değerleri (format: hue1,hue2,hue3)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->dropColumn('color_hash');
        });
    }
};
