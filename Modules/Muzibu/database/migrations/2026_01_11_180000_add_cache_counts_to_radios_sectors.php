<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add cache count fields to muzibu_sectors
     *
     * Bu migration şu tabloları günceller:
     * - muzibu_sectors: songs_count, total_duration
     *
     * NOT: Radio'da cache count OLMASIN (kullanıcı isteği)
     *
     * NULL = henüz hesaplanmamış (lazy calculation ile doldurulacak)
     * 0 = hesaplandı, gerçekten 0
     */
    public function up(): void
    {
        // Sectors
        if (Schema::hasTable('muzibu_sectors')) {
            Schema::table('muzibu_sectors', function (Blueprint $table) {
                if (!Schema::hasColumn('muzibu_sectors', 'songs_count')) {
                    $table->unsignedInteger('songs_count')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('muzibu_sectors', 'total_duration')) {
                    $table->unsignedInteger('total_duration')->nullable()->after('songs_count')
                        ->comment('Total duration in seconds');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('muzibu_sectors')) {
            Schema::table('muzibu_sectors', function (Blueprint $table) {
                $table->dropColumn(['songs_count', 'total_duration']);
            });
        }
    }
};
