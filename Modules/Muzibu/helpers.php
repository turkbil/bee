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
        // Universal helper'ı kullan
        generate_ai_cover($model, $title, $type);
    }
}
