<?php

/**
 * ======================================================================
 * MediaManagement Module - Helper Functions
 * ======================================================================
 */

if (!function_exists('generate_ai_cover')) {
    /**
     * Universal AI Cover Generator
     *
     * Herhangi bir model için otomatik Leonardo AI görsel üretimi (Queue)
     *
     * @param  object  $model  Eloquent Model (Song, Playlist, Blog, Product, etc.)
     * @param  string  $title  Görsel için başlık (Türkçe veya İngilizce)
     * @param  string|null  $type  İçerik tipi: 'song', 'playlist', 'album', 'blog', 'product', etc.
     * @return void
     *
     * Örnek Kullanım:
     * ```php
     * // Playlist için
     * $playlist = Playlist::create([...]);
     * generate_ai_cover($playlist, $playlist->title, 'playlist');
     *
     * // Song için
     * $song = Song::create([...]);
     * generate_ai_cover($song, $song->title, 'song');
     *
     * // Blog için
     * $blog = Blog::create([...]);
     * generate_ai_cover($blog, $blog->title, 'blog');
     *
     * // Type otomatik algılama
     * generate_ai_cover($model, 'Başlık'); // Model class'ından type algılar
     * ```
     *
     * Özellikler:
     * - Queue'de çalışır (yavaşlatma YOK!)
     * - 11 Altın Kural ile prompt genişletme
     * - Dinamik tema algılama
     * - Tenant-aware
     * - AI credit otomatik düşer
     */
    function generate_ai_cover($model, string $title, ?string $type = null): void
    {
        // Type otomatik algılama (verilmediyse)
        if (!$type) {
            $className = class_basename($model);
            $type = strtolower($className);
        }

        // Model ID al
        $modelId = $model->id ?? $model->{$model->getKeyName()} ?? null;

        if (!$modelId) {
            \Log::warning('generate_ai_cover: Model ID bulunamadı', [
                'model_class' => get_class($model),
            ]);
            return;
        }

        // ⚠️ FIX: Hero görseli zaten varsa job dispatch etme!
        // Bu kontrol gereksiz job oluşturmayı önler
        if (method_exists($model, 'hasMedia') && $model->hasMedia('hero')) {
            return; // Sessizce çık, log spam yapma
        }

        // Job dispatch et
        \Modules\MediaManagement\App\Jobs\GenerateAICover::dispatch(
            get_class($model),
            $modelId,
            $title,
            $type,
            auth()->id(),
            tenant('id')
        );

    }
}
