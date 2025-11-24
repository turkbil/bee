<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_song_plays')) {
            return;
        }

        Schema::create('muzibu_song_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained('muzibu_songs', 'song_id')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->comment('IPv4 or IPv6 address');
            $table->string('user_agent')->nullable()->comment('Browser user agent');
            $table->string('device_type')->nullable()->comment('mobile, tablet, desktop');
            $table->timestamps();

            // İlave indeksler
            $table->index('song_id');
            $table->index('user_id');
            $table->index('ip_address');
            $table->index('created_at');
            $table->index('device_type');

            // Composite index'ler - Analytics queries için
            $table->index(['song_id', 'created_at'], 'muzibu_song_plays_song_created_idx');
            $table->index(['user_id', 'created_at'], 'muzibu_song_plays_user_created_idx');
            $table->index(['created_at', 'song_id'], 'muzibu_song_plays_created_song_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_song_plays');
    }
};
