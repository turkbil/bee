<?php

namespace Modules\Studio\Listeners;

use Modules\Studio\Events\StudioWidgetUpdated;
use Illuminate\Support\Facades\Log;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioCacheService;

class StudioWidgetUpdatedListener
{
    protected $widgetService;
    protected $cacheService;
    
    /**
     * Create the event listener.
     */
    public function __construct(StudioWidgetService $widgetService, StudioCacheService $cacheService)
    {
        $this->widgetService = $widgetService;
        $this->cacheService = $cacheService;
    }
    
    /**
     * Handle the event.
     */
    public function handle(StudioWidgetUpdated $event): void
    {
        // Widget önbelleğini temizle
        $this->widgetService->clearCache();
        $this->cacheService->clearByType('widget');
        
        // Log
        Log::info('Widget güncellendi', [
            'widget_id' => $event->widgetId,
            'user_id' => $event->userId ?? 'Bilinmeyen',
            'timestamp' => $event->timestamp
        ]);
        
        // Widget verilerini al
        try {
            $widget = null;
            
            if (class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                $widget = \Modules\WidgetManagement\App\Models\Widget::find($event->widgetId);
            }
            
            if ($widget) {
                // Etkilenen sayfaların önbelleğini temizle
                if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                    // Widget kullanılan sayfaları tespit et ve önbelleklerini temizle
                    $this->clearAffectedPages($widget);
                }
            }
        } catch (\Exception $e) {
            Log::error('Widget güncellemesi sonrası işlem hatası: ' . $e->getMessage());
        }
    }
    
    /**
     * Widget'ın kullanıldığı sayfaların önbelleğini temizle
     * 
     * @param mixed $widget Widget modeli
     * @return void
     */
    protected function clearAffectedPages($widget): void
    {
        // Widget'ın kullanıldığı sayfaları tespit et
        // Bu örnekte basit bir yaklaşım kullanıyoruz
        // Gerçek uygulamada widget-sayfa ilişkilerini takip eden bir yapı olmalı
        
        try {
            // Page modülü yüklüyse
            if (class_exists('Modules\Page\App\Models\Page')) {
                // Widget adını veya ID'sini içeren sayfaları bul
                $pages = \Modules\Page\App\Models\Page::where('body', 'like', '%widget-' . $widget->id . '%')
                    ->orWhere('body', 'like', '%widget-id="' . $widget->id . '"%')
                    ->orWhere('body', 'like', '%data-widget-id="' . $widget->id . '"%')
                    ->get();
                
                foreach ($pages as $page) {
                    // Sayfa önbelleğini temizle
                    \Spatie\ResponseCache\Facades\ResponseCache::forget("/pages/{$page->slug}");
                    
                    // Aktivite kaydı
                    if (function_exists('activity')) {
                        activity()
                            ->performedOn($page)
                            ->withProperties(['widget_id' => $widget->id])
                            ->log('widget güncellendi - önbellek temizlendi');
                    }
                }
                
                Log::info('Widget güncelleme sonrası etkilenen sayfalar temizlendi', [
                    'widget_id' => $widget->id,
                    'affected_page_count' => $pages->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Etkilenen sayfaların önbelleği temizlenirken hata: ' . $e->getMessage());
        }
    }
}