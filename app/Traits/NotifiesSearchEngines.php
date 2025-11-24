<?php

namespace App\Traits;

use App\Services\IndexNowService;
use Illuminate\Support\Facades\Log;

/**
 * NotifiesSearchEngines Trait
 *
 * Model oluşturulduğunda/güncellendiğinde arama motorlarına bildirim gönderir
 * Observer'larda veya Model boot metodunda kullanılabilir
 *
 * Kullanım:
 * 1. Observer'da: $this->notifySearchEngines($model);
 * 2. Model'de: use NotifiesSearchEngines; (boot metodunda otomatik çalışır)
 */
trait NotifiesSearchEngines
{
    /**
     * Model için arama motorlarına bildirim gönder
     */
    protected function notifySearchEngines($model): void
    {
        // Sadece aktif modeller için
        if (isset($model->is_active) && !$model->is_active) {
            return;
        }

        // Queue'ya at (blocking olmasın)
        dispatch(function () use ($model) {
            try {
                IndexNowService::submitModel($model);
            } catch (\Exception $e) {
                Log::warning('IndexNow notification failed', [
                    'model' => get_class($model),
                    'id' => $model->getKey(),
                    'error' => $e->getMessage()
                ]);
            }
        })->afterResponse(); // Response gönderildikten sonra çalış
    }

    /**
     * URL için arama motorlarına bildirim gönder
     */
    protected function notifySearchEnginesForUrl(string $url): void
    {
        dispatch(function () use ($url) {
            try {
                IndexNowService::submitUrl($url);
            } catch (\Exception $e) {
                Log::warning('IndexNow URL notification failed', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            }
        })->afterResponse();
    }

    /**
     * Boot metodunda otomatik bildirim (Model'de kullanıldığında)
     */
    public static function bootNotifiesSearchEngines(): void
    {
        // Model oluşturulduğunda
        static::created(function ($model) {
            if (method_exists($model, 'getUrl') && ($model->is_active ?? true)) {
                dispatch(function () use ($model) {
                    IndexNowService::submitModel($model);
                })->afterResponse();
            }
        });

        // Model güncellendiğinde
        static::updated(function ($model) {
            if (method_exists($model, 'getUrl') && ($model->is_active ?? true)) {
                // Sadece önemli alanlar değiştiyse bildir
                $importantFields = ['title', 'slug', 'body', 'content', 'description', 'is_active'];
                $dirty = array_keys($model->getDirty());

                $hasImportantChange = !empty(array_intersect($dirty, $importantFields));

                if ($hasImportantChange) {
                    dispatch(function () use ($model) {
                        IndexNowService::submitModel($model);
                    })->afterResponse();
                }
            }
        });
    }
}
