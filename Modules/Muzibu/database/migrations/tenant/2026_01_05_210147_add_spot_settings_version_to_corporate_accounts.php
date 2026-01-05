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
        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            // Version field: Ayarlar her değiştiğinde artırılır
            if (!Schema::hasColumn('muzibu_corporate_accounts', 'spot_settings_version')) {
                $table->unsignedInteger('spot_settings_version')
                    ->default(1)
                    ->after('spot_is_paused')
                    ->comment('Ayarlar/anonslar her değiştiğinde artırılır (sync için)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('muzibu_corporate_accounts', 'spot_settings_version')) {
                $table->dropColumn('spot_settings_version');
            }
        });
    }
};
