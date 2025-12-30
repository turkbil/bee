<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mevcut playlist_sector ve playlist_radio verilerini
     * yeni playlistables tablosuna taşır.
     *
     * Bu migration geri alınabilir (rollback) - eski tablolar korunur.
     */
    public function up(): void
    {
        // 1. Sector verilerini taşı
        if (Schema::hasTable('muzibu_playlist_sector')) {
            $sectors = DB::table('muzibu_playlist_sector')->get();

            foreach ($sectors as $sector) {
                DB::table('muzibu_playlistables')->insertOrIgnore([
                    'playlist_id' => $sector->playlist_id,
                    'playlistable_type' => 'sector',
                    'playlistable_id' => $sector->sector_id,
                    'position' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Log
            $count = count($sectors);
            if ($count > 0) {
                \Log::info("✅ Playlistables Migration: {$count} sector ilişkisi taşındı");
            }
        }

        // 2. Radio verilerini taşı
        if (Schema::hasTable('muzibu_playlist_radio')) {
            $radios = DB::table('muzibu_playlist_radio')->get();

            foreach ($radios as $radio) {
                DB::table('muzibu_playlistables')->insertOrIgnore([
                    'playlist_id' => $radio->playlist_id,
                    'playlistable_type' => 'radio',
                    'playlistable_id' => $radio->radio_id,
                    'position' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Log
            $count = count($radios);
            if ($count > 0) {
                \Log::info("✅ Playlistables Migration: {$count} radio ilişkisi taşındı");
            }
        }
    }

    public function down(): void
    {
        // Taşınan verileri sil (eski tablolar hala mevcut)
        DB::table('muzibu_playlistables')
            ->whereIn('playlistable_type', ['sector', 'radio'])
            ->delete();

        \Log::info("⏪ Playlistables Migration: sector ve radio verileri geri alındı");
    }
};
