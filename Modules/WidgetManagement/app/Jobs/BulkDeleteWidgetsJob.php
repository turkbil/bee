<?php

namespace Modules\WidgetManagement\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\WidgetManagement\app\Models\Widget;
use Spatie\Activitylog\Models\Activity;

class BulkDeleteWidgetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public array $widgetIds,
        public string $tenantId,
        public int $userId,
        public string $cacheKey
    ) {}

    public function handle(): void
    {
        try {
            // Tenant context ayarla
            if (!empty($this->tenantId) && $this->tenantId !== 'central') {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            $totalItems = count($this->widgetIds);
            $processedItems = 0;
            $successCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($this->widgetIds as $widgetId) {
                try {
                    $widget = Widget::find($widgetId);
                    
                    if (!$widget) {
                        $errors[] = "Widget bulunamadı: ID {$widgetId}";
                        continue;
                    }

                    // Widget koruması kontrolü
                    if ($this->isProtectedWidget($widget)) {
                        $errors[] = "Korumalı widget silinemez: {$widget->title}";
                        continue;
                    }

                    // Widget'a bağlı öğeleri kontrol et
                    if ($this->hasRelatedItems($widget)) {
                        $errors[] = "Widget'a bağlı öğeler var: {$widget->title}";
                        continue;
                    }

                    // Activity log kaydet
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($widget)
                        ->withProperties([
                            'deleted_data' => $widget->toArray(),
                            'bulk_operation' => true
                        ])
                        ->log('bulk_deleted');

                    $widget->delete();
                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Widget silme hatası ({$widget->title ?? "ID: {$widgetId}"}): " . $e->getMessage();
                    Log::error("BulkDeleteWidgets Error", [
                        'widget_id' => $widgetId,
                        'error' => $e->getMessage(),
                        'tenant' => $this->tenantId
                    ]);
                }

                $processedItems++;
                $progress = ($processedItems / $totalItems) * 100;

                // Progress güncelle
                Cache::put($this->cacheKey, [
                    'progress' => round($progress, 2),
                    'processed' => $processedItems,
                    'total' => $totalItems,
                    'success_count' => $successCount,
                    'error_count' => count($errors),
                    'status' => 'processing',
                    'current_action' => "Widget siliniyor ({$processedItems}/{$totalItems})"
                ], 300);
            }

            DB::commit();

            // Başarı durumu
            Cache::put($this->cacheKey, [
                'progress' => 100,
                'processed' => $totalItems,
                'total' => $totalItems,
                'success_count' => $successCount,
                'error_count' => count($errors),
                'status' => 'completed',
                'message' => "{$successCount} widget başarıyla silindi",
                'errors' => $errors
            ], 300);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('BulkDeleteWidgets Job Failed', [
                'error' => $e->getMessage(),
                'tenant' => $this->tenantId,
                'widget_ids_count' => count($this->widgetIds)
            ]);

            Cache::put($this->cacheKey, [
                'progress' => 0,
                'status' => 'failed',
                'message' => 'Toplu silme başarısız: ' . $e->getMessage()
            ], 300);

            throw $e;
        }
    }

    private function isProtectedWidget($widget): bool
    {
        // Sistem widget'ları veya default widget'lar korumalı olabilir
        if (isset($widget->is_system) && $widget->is_system) {
            return true;
        }

        if (isset($widget->is_default) && $widget->is_default) {
            return true;
        }

        // Özel widget türleri korumalı olabilir
        $protectedTypes = ['system', 'core', 'essential'];
        if (isset($widget->type) && in_array($widget->type, $protectedTypes)) {
            return true;
        }

        return false;
    }

    private function hasRelatedItems($widget): bool
    {
        // Widget'a bağlı öğeler varsa kontrol et
        // Örnek: widget_items tablosu
        if (method_exists($widget, 'widgetItems') && $widget->widgetItems()->count() > 0) {
            return true;
        }

        // Diğer ilişkiler kontrol edilebilir
        return false;
    }

    public function failed(\Exception $exception): void
    {
        Log::error('BulkDeleteWidgets Job Failed', [
            'error' => $exception->getMessage(),
            'tenant' => $this->tenantId,
            'widget_ids_count' => count($this->widgetIds)
        ]);

        Cache::put($this->cacheKey, [
            'progress' => 0,
            'status' => 'failed',
            'message' => 'Toplu silme başarısız oldu: ' . $exception->getMessage()
        ], 300);
    }
}