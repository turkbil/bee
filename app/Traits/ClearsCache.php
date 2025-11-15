<?php

namespace App\Traits;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Universal Cache Clear Trait
 *
 * Her model CRUD işleminde (create, update, delete) otomatik cache temizliği yapar
 *
 * Kullanım:
 * ```php
 * use App\Traits\ClearsCache;
 *
 * class Blog extends Model {
 *     use ClearsCache;
 * }
 * ```
 */
trait ClearsCache
{
    /**
     * Boot trait - Model event listeners ekle
     */
    public static function bootClearsCache(): void
    {
        // Created: Yeni kayıt eklendi
        static::created(function ($model) {
            $model->clearAllCaches('created');
        });

        // Updated: Kayıt güncellendi
        static::updated(function ($model) {
            $model->clearAllCaches('updated');
        });

        // Deleted: Kayıt silindi
        static::deleted(function ($model) {
            $model->clearAllCaches('deleted');
        });
    }

    /**
     * Tüm cache'leri temizle
     */
    protected function clearAllCaches(string $action = 'unknown'): void
    {
        try {
            // 1. View cache
            Artisan::call('view:clear');

            // 2. Response cache (Responsecache package)
            Artisan::call('responsecache:clear');

            // 3. Application cache
            Cache::flush();

            // 4. Config cache (dikkatli kullan!)
            // Artisan::call('config:clear'); // Production'da sorun yaratabilir, skip

            // 5. Route cache (dikkatli kullan!)
            // Artisan::call('route:clear'); // Production'da sorun yaratabilir, skip

            Log::info('✅ Universal Cache Cleared', [
                'model' => get_class($this),
                'action' => $action,
                'id' => $this->id ?? $this->getKey() ?? 'unknown',
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Cache Clear Failed', [
                'model' => get_class($this),
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Manuel cache temizleme (ihtiyaç olursa)
     */
    public function clearCache(): void
    {
        $this->clearAllCaches('manual');
    }
}
