<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds browser and platform columns to song_plays table
     * for proper device/browser tracking in abuse detection.
     */
    public function up(): void
    {
        Schema::table('muzibu_song_plays', function (Blueprint $table) {
            // Browser name (Chrome, Firefox, Safari, Brave, Edge, Opera, etc.)
            $table->string('browser', 50)->nullable()->after('device_type');

            // Platform/OS name (Windows, macOS, iOS, Android, Linux, etc.)
            $table->string('platform', 50)->nullable()->after('browser');

            // Index for abuse detection queries
            $table->index(['user_id', 'browser', 'created_at'], 'song_plays_user_browser_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_song_plays', function (Blueprint $table) {
            $table->dropIndex('song_plays_user_browser_idx');
            $table->dropColumn(['browser', 'platform']);
        });
    }
};
