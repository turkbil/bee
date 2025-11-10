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
            $table->string('hls_path', 500)->nullable()->after('file_path')->comment('HLS playlist path (muzibu/songs/hls/song-{id}/playlist.m3u8)');
            $table->boolean('hls_converted')->default(false)->after('hls_path')->index()->comment('HLS conversion status (lazy conversion)');
            $table->unsignedInteger('bitrate')->nullable()->after('duration')->comment('Audio bitrate in kbps');
            $table->json('metadata')->nullable()->after('bitrate')->comment('Additional metadata (sample_rate, channels, etc.)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_songs', function (Blueprint $table) {
            $table->dropColumn(['hls_path', 'hls_converted', 'bitrate', 'metadata']);
        });
    }
};
