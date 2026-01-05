<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alt şubelerde spot_enabled, spot_songs_between, spot_settings_version nullable yap
     * Bu field'ler sadece ana şubede kullanılıyor, alt şubeler parent'tan inherit ediyor
     */
    public function up(): void
    {
        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            // Alt şubeler için bu field'ler NULL olabilir (parent'tan alınır)
            $table->boolean('spot_enabled')->nullable()->default(null)->change();
            $table->unsignedInteger('spot_songs_between')->nullable()->default(null)->change();
            $table->unsignedInteger('spot_settings_version')->nullable()->default(null)->change();
        });
    }

    /**
     * Rollback: Field'leri NOT NULL yap, default değerlerle
     */
    public function down(): void
    {
        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            $table->boolean('spot_enabled')->nullable(false)->default(true)->change();
            $table->unsignedInteger('spot_songs_between')->nullable(false)->default(10)->change();
            $table->unsignedInteger('spot_settings_version')->nullable(false)->default(1)->change();
        });
    }
};
