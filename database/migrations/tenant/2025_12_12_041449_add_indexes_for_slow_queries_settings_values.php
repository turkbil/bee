<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Slow query fix: settings_values query taking 104-105ms
     * Query: SELECT * FROM settings_values WHERE setting_id = ? LIMIT 1
     * Solution: Add index on setting_id column
     */
    public function up(): void
    {
        Schema::table('settings_values', function (Blueprint $table) {
            // Index for setting_id lookups
            $table->index('setting_id', 'idx_settings_values_setting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings_values', function (Blueprint $table) {
            $table->dropIndex('idx_settings_values_setting_id');
        });
    }
};
