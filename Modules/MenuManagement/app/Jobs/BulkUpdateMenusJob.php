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
 * ✏️ Bulk Menu Update Queue Job
 * 
 * MenuManagement modülünün bulk güncelleme işlemleri için queue job:
 * - Toplu menü güncelleme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Page modülünden kopya alınmış template
 */
class BulkUpdateMenusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $menuIds Güncellenecek menü ID'leri
     * @param array $updateData Güncellenecek veriler
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (validate, etc.)
     */
    public function __construct(
        public array $menuIds,
        public array $updateData,
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
            Log::info('✏️ BULK MENU UPDATE STARTED', [
                'menu_ids' => $this->menuIds,
                'update_data' => $this->updateData,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->menuIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_update_menus_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->menuIds), 'starting');

            // Güvenlik kontrolü - güncellenebilir alanları kontrol et
            $allowedFields = $this->getAllowedUpdateFields();
            $filteredUpdateData = array_intersect_key($this->updateData, array_flip($allowedFields));

            if (empty($filteredUpdateData)) {
                throw new \InvalidArgumentException('Güncellenebilir geçerli alan bulunamadı');
            }

            // Her menü için güncelleme işlemi
            foreach ($this->menuIds as $index => $menuId) {
                try {
                    // Menü var mı kontrol et
                    $menu = Menu::find($menuId);
                    if (!$menu) {
                        Log::warning("Menü bulunamadı: {$menuId}");
                        continue;
                    }

                    // Varsayılan menü kontrolü - ana menü name değişmesin
                    if (isset($menu->is_default) && $menu->is_default && isset($filteredUpdateData['name'])) {
                        unset($filteredUpdateData['name']);
                        Log::warning("Varsayılan menü adı değiştirilemez: {$menu->name}");
                    }

                    // Özel validasyonlar
                    if ($this->options['validate'] ?? true) {
                        $this->validateUpdateData($menu, $filteredUpdateData);
                    }

                    // Güncelleme işlemi
                    $oldData = $menu->toArray();
                    $menu->update($filteredUpdateData);
                    
                    // Activity log
                    activity()
                        ->performedOn($menu)
                        ->causedBy(auth()->id())
                        ->withProperties([
                            'old' => $oldData,
                            'new' => $filteredUpdateData,
                            'bulk_operation' => true
                        ])
                        ->log('bulk_updated');

                    $processedCount++;
                    
                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->menuIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->menuIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_menu' => $menu->name
                    ]);

                    Log::info("✅ Menü güncellendi", [
                        'id' => $menuId,
                        'name' => $menu->name,
                        'updated_fields' => array_keys($filteredUpdateData)
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Menü güncelleme hatası (ID: {$menuId}): " . $e->getMessage();
                    
                    Log::error("❌ Menü güncelleme hatası", [
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
                'error_messages' => $errors,
                'updated_fields' => array_keys($filteredUpdateData)
            ]);

            Log::info('✅ BULK MENU UPDATE COMPLETED', [
                'total_menus' => count($this->menuIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's',
                'updated_fields' => array_keys($filteredUpdateData)
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->menuIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK MENU UPDATE FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Güncellenebilir alanları tanımla - Menu modülüne özel
     */
    private function getAllowedUpdateFields(): array
    {
        return [
            'is_active',
            'name',
            'location',
            'description',
            'sort_order',
            'max_depth',
            'auto_add',
            'css_class',
            'theme_location'
        ];
    }

    /**
     * Update data validasyonu - Menu modülüne özel
     */
    private function validateUpdateData(Menu $menu, array $updateData): void
    {
        // Name benzersizlik kontrolü
        if (isset($updateData['name'])) {
            $existingMenu = Menu::where('name', $updateData['name'])
                ->where('id', '!=', $menu->id)
                ->first();
                
            if ($existingMenu) {
                throw new \InvalidArgumentException("Menü adı zaten kullanımda: {$updateData['name']}");
            }
        }

        // Location benzersizlik kontrolü
        if (isset($updateData['location'])) {
            $existingMenu = Menu::where('location', $updateData['location'])
                ->where('id', '!=', $menu->id)
                ->first();
                
            if ($existingMenu) {
                throw new \InvalidArgumentException("Menü konumu zaten kullanımda: {$updateData['location']}");
            }
        }

        // Sort order kontrolü
        if (isset($updateData['sort_order']) && $updateData['sort_order'] < 0) {
            throw new \InvalidArgumentException('Sıralama 0 veya pozitif bir sayı olmalıdır');
        }

        // Max depth kontrolü
        if (isset($updateData['max_depth']) && ($updateData['max_depth'] < 1 || $updateData['max_depth'] > 10)) {
            throw new \InvalidArgumentException('Maksimum derinlik 1-10 arasında olmalıdır');
        }

        // Theme location kontrolü
        if (isset($updateData['theme_location'])) {
            $allowedLocations = ['header', 'footer', 'sidebar', 'primary', 'secondary'];
            if (!in_array($updateData['theme_location'], $allowedLocations)) {
                throw new \InvalidArgumentException('Geçersiz tema konumu: ' . $updateData['theme_location']);
            }
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

            Log::info('🗑️ Menu caches cleared after bulk update');
            
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK MENU UPDATE JOB FAILED', [
            'menu_ids' => $this->menuIds,
            'update_data' => $this->updateData,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_update_menus_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->menuIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}