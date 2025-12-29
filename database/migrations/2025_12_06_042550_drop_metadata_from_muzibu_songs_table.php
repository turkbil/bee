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
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->dropColumn(['metadata', 'bitrate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->longText('metadata')->nullable()->comment('Audio metadata (DEPRECATED - Removed)');
            $table->unsignedInteger('bitrate')->nullable()->comment('Audio bitrate in kbps (DEPRECATED - Removed, use fixed 256 kbps)');
        });
    }
};
