<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\MenuManagement\App\Models\Menu;
use Throwable;

/**
 * 🗑️ Bulk Menu Delete Queue Job
 * 
 * MenuManagement modülünün bulk silme işlemleri için queue job:
 * - Toplu menü silme işlemleri için optimize edilmiş
 * - Hiyerarşik menü yapısı korunarak silme
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Page modülünden kopya alınmış template
 */
class BulkDeleteMenusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $menuIds Silinecek menü ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $menuIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK MENU DELETE STARTED', [
                'menu_ids' => $this->menuIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->menuIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_menus_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->menuIds), 'starting');

            // Her menü için silme işlemi
            foreach ($this->menuIds as $index => $menuId) {
                try {
                    // Menü var mı kontrol et
                    $menu = Menu::find($menuId);
                    if (!$menu) {
                        Log::warning("Menü bulunamadı: {$menuId}");
                        continue;
                    }

                    // Varsayılan menü kontrolü - ana menü silinemesin
                    if (isset($menu->is_default) && $menu->is_default) {
                        $errors[] = "Varsayılan menü silinemez: {$menu->name}";
                        $errorCount++;
                        continue;
                    }

                    // Alt menü öğelerini kontrol et
                    $menuItemsCount = $menu->menuItems()->count();
                    if ($menuItemsCount > 0) {
                        Log::warning("Menüde {$menuItemsCount} öğe var, tüm öğeler de silinecek: {$menu->name}");
                    }

                    // Silme işlemi - menü öğeleri otomatik cascade silinir
                    $forceDelete = $this->options['force_delete'] ?? false;
                    
                    if ($forceDelete) {
                        $menu->forceDelete();
                    } else {
                        $menu->delete();
                    }

                    $processedCount++;
                    
                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->menuIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->menuIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_menu' => $menu->name,
                        'menu_items_deleted' => $menuItemsCount
                    ]);

                    Log::info("✅ Menü silindi", [
                        'id' => $menuId,
                        'name' => $menu->name,
                        'menu_items_count' => $menuItemsCount,
                        'force_delete' => $forceDelete
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Menü silme hatası (ID: {$menuId}): " . $e->getMessage();
                    
                    Log::error("❌ Menü silme hatası", [
                        'menu_id' => $menuId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearMenuCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->menuIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK MENU DELETE COMPLETED', [
                'total_menus' => count($this->menuIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->menuIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK MENU DELETE FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Progress tracking
     */
    private function updateProgress(string $key, int $progress, int $total, string $status, array $data = []): void
    {
        Cache::put($key, [
            'progress' => $progress,
            'total' => $total,
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'data' => $data
        ], 600); // 10 dakika
    }

    /**
     * Cache temizleme
     */
    private function clearMenuCaches(): void
    {
        try {
            // Menu cache'leri temizle
            Cache::forget('menus_list');
            Cache::forget('menus_active');
            Cache::forget('menu_items_cache');
            Cache::forget('navigation_cache');
            
            // Pattern-based cache temizleme
            $patterns = [
                'menu_*',
                'menus_*',
                'navigation_*',
                'menu_items_*'
            ];
            
            foreach ($patterns as $pattern) {
                Cache::tags(['menus'])->flush();
            }

            Log::info('🗑️ Menu caches cleared after bulk delete');
            
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK MENU DELETE JOB FAILED', [
            'menu_ids' => $this->menuIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_menus_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->menuIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}