<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Support\Facades\Log;

/**
 * ClearsResponseCache Trait
 *
 * Bu trait'i kullanan model her kaydedildiginde/guncellendiginde/silindiginde
 * Response Cache otomatik olarak temizlenir.
 *
 * Kullanim:
 * class Song extends Model {
 *     use ClearsResponseCache;
 * }
 *
 * NOT: Bu trait GUEST kullanicilar icin olan response cache'i temizler.
 * Auth kullanicilar icin response cache zaten kapali.
 */
trait ClearsResponseCache
{
    /**
     * Boot the trait
     */
    public static function bootClearsResponseCache(): void
    {
        // Model kaydedildiginde (create veya update)
        static::saved(function ($model) {
            self::clearResponseCacheQuietly($model, 'saved');
        });

        // Model silindiginde
        static::deleted(function ($model) {
            self::clearResponseCacheQuietly($model, 'deleted');
        });
    }

    /**
     * Response cache'i sessizce temizle (hata olursa log'la ama exception atma)
     */
    protected static function clearResponseCacheQuietly($model, string $event): void
    {
        try {
            // ResponseCache paketi yuklu mu kontrol et
            if (!class_exists(\Spatie\ResponseCache\Facades\ResponseCache::class)) {
                return;
            }

            // Tenant bazli cache temizleme
            $tenantId = tenant() ? tenant()->id : 'central';

            // Response cache'i temizle
            ResponseCache::clear();

            // Debug log (production'da kapatilabilir)
            if (config('app.debug')) {
                Log::debug("ResponseCache cleared", [
                    'model' => get_class($model),
                    'id' => $model->getKey(),
                    'event' => $event,
                    'tenant' => $tenantId,
                ]);
            }
        } catch (\Exception $e) {
            // Hata olursa sessizce log'la, uygulamayi kilitleme
            Log::warning("ResponseCache clear failed", [
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Manuel olarak response cache temizle
     */
    public function clearResponseCache(): void
    {
        self::clearResponseCacheQuietly($this, 'manual');
    }
}
