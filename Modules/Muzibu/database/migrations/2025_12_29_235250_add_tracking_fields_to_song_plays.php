<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Şarkı dinleme takibi için yeni alanlar ekleniyor
     */
    public function up(): void
    {
        Schema::table('muzibu_song_plays', function (Blueprint $table) {
            // Bitiş zamanı - şarkı ne zaman durdu/bitti
            $table->timestamp('ended_at')->nullable()->after('updated_at');

            // Dinleme süresi (saniye) - gerçekte ne kadar dinlendi
            $table->unsignedInteger('listened_duration')->nullable()->after('ended_at');

            // Atlandı mı - şarkı bitmeden geçildi mi
            $table->boolean('was_skipped')->default(false)->after('listened_duration');

            // Kaynak tipi - nereden çalındı (playlist, album, artist, search, radio, queue)
            $table->string('source_type', 50)->nullable()->after('was_skipped');

            // Kaynak ID - hangi playlist/album/artist vs.
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');

            // Index for abuse detection queries
            $table->index(['user_id', 'ended_at'], 'song_plays_user_ended_idx');
            $table->index(['source_type', 'source_id'], 'song_plays_source_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_song_plays', function (Blueprint $table) {
            $table->dropIndex('song_plays_user_ended_idx');
            $table->dropIndex('song_plays_source_idx');

            $table->dropColumn([
                'ended_at',
                'listened_duration',
                'was_skipped',
                'source_type',
                'source_id'
            ]);
        });
    }
};
