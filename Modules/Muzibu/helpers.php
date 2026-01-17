<?php

/**
 * ======================================================================
 * Muzibu Module - Helper Functions
 * ======================================================================
 */

if (!function_exists('muzibu_generate_ai_cover')) {
    /**
     * Muzibu AI Cover Generator
     *
     * Muzibu modeli için otomatik Leonardo AI görsel üretimi (Queue)
     * Universal generate_ai_cover() wrapper'ı
     *
     * @param  object  $model  Muzibu Model (Song, Playlist, Album, Genre, Artist, Radio, Sector)
     * @param  string  $title  Görsel için başlık
     * @param  string  $type  İçerik tipi: 'song', 'playlist', 'album', 'genre', 'artist', 'radio', 'sector'
     * @return void
     *
     * Örnek Kullanım:
     * ```php
     * // Song için
     * $song = Song::create([...]);
     * muzibu_generate_ai_cover($song, $song->title, 'song');
     *
     * // Playlist için
     * $playlist = Playlist::create([...]);
     * muzibu_generate_ai_cover($playlist, $playlist->title, 'playlist');
     *
     * // Genre için
     * $genre = Genre::create([...]);
     * muzibu_generate_ai_cover($genre, $genre->title, 'genre');
     * ```
     */
    function muzibu_generate_ai_cover($model, string $title, string $type): void
    {
        // Song için özel GenerateSongCover job'u kullan (genre bazlı prompt desteği)
        if ($type === 'song' && $model instanceof \Modules\Muzibu\App\Models\Song) {
            // Hero görseli zaten varsa job dispatch etme
            if (method_exists($model, 'hasMedia') && $model->hasMedia('hero')) {
                return;
            }

            \Modules\Muzibu\App\Jobs\GenerateSongCover::dispatch(
                songId: $model->song_id,
                songTitle: $title,
                artistName: $model->album?->artist?->title,
                genreName: $model->genre?->title,
                userId: auth()->id(),
                tenantId: tenant('id'),
                genreId: $model->genre_id, // Genre bazlı özel prompt için
                forceRegenerate: false
            );
            return;
        }

        // Diğer tipler için universal helper'ı kullan
        generate_ai_cover($model, $title, $type);
    }
}
