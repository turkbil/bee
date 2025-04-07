<?php

namespace App\Traits;

use CyrildeWit\EloquentViewable\InteractsWithViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;

trait HasPageViews
{
    use InteractsWithViews;

    /**
     * Tekil ziyaretçileri izleme ayarı
     *
     * @return bool
     */
    public function shouldCountUnique(): bool
    {
        return true;
    }

    /**
     * Bir kullanıcının tekrar ziyaretini sayma süresi (saniye)
     *
     * @return int
     */
    public function getUniqueExpiration(): int
    {
        // 24 saat (saniye cinsinden)
        return 60 * 60 * 24;
    }

    /**
     * İzleme için özel collection tanımı
     *
     * @return string
     */
    public function getViewableCollection(): string
    {
        // Model sınıf adını küçük harflerle kullan (örn: page, portfolio)
        return strtolower(class_basename($this));
    }
}