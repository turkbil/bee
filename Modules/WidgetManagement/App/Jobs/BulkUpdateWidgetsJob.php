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

class BulkUpdateWidgetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public array $widgetIds,
        public array $updateData,
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

            // İzin verilen alanlar
            $allowedFields = [
                'title', 'content', 'status', 'is_active',
                'position', 'order', 'widget_area', 'widget_type',
                'settings', 'css_class', 'visibility'
            ];

            // Güvenlik kontrolü: Sadece izin verilen alanlar
            $updateData = array_intersect_key($this->updateData, array_flip($allowedFields));

            if (empty($updateData)) {
                throw new \Exception('Güncelleme için geçerli alan bulunamadı');
            }

            DB::beginTransaction();

            foreach ($this->widgetIds as $widgetId) {
                try {
                    $widget = Widget::find($widgetId);
                    
                    if (!$widget) {
                        $errors[] = "Widget bulunamadı: ID {$widgetId}";
                        continue;
                    }

                    // Korumalı widget kontrolü
                    if ($this->hasUpdateRestriction($widget, $updateData)) {
                        $errors[] = "Güncelleme kısıtlaması: {$widget->title} güncellenemez";
                        continue;
                    }

                    // Benzersizlik kontrolleri
                    if ($this->hasUniquenessConflict($widget, $updateData)) {
                        $errors[] = "Benzersizlik çakışması: {$widget->title}";
                        continue;
                    }

                    // Widget'i güncelle
                    $oldData = $widget->toArray();
                    $widget->update($updateData);

                    // Activity log
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($widget)
                        ->withProperties([
                            'old' => $oldData,
                            'attributes' => $widget->fresh()->toArray(),
                            'bulk_operation' => true
                        ])
                        ->log('bulk_updated');

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Widget güncelleme hatası ({$widget->title ?? "ID: {$widgetId}"}): " . $e->getMessage();
                    Log::error("BulkUpdateWidgets Error", [
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
                    'current_action' => "Widget güncelleniyor ({$processedItems}/{$totalItems})"
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
                'message' => "{$successCount} widget başarıyla güncellendi",
                'errors' => $errors
            ], 300);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('BulkUpdateWidgets Job Failed', [
                'error' => $e->getMessage(),
                'tenant' => $this->tenantId,
                'widget_ids_count' => count($this->widgetIds)
            ]);

            Cache::put($this->cacheKey, [
                'progress' => 0,
                'status' => 'failed',
                'message' => 'Toplu güncelleme başarısız: ' . $e->getMessage()
            ], 300);

            throw $e;
        }
    }

    private function hasUpdateRestriction($widget, array $updateData): bool
    {
        // Sistem widget'ları güncelleme kısıtlaması
        if (isset($widget->is_system) && $widget->is_system) {
            $restrictedFields = ['widget_type', 'position', 'widget_area'];
            if (array_intersect_key($updateData, array_flip($restrictedFields))) {
                return true;
            }
        }

        // Default widget'lar için kısıtlama
        if (isset($widget->is_default) && $widget->is_default) {
            if (isset($updateData['is_active']) && !$updateData['is_active']) {
                return true; // Default widget'lar devre dışı bırakılamaz
            }
        }

        return false;
    }

    private function hasUniquenessConflict($widget, array $updateData): bool
    {
        // Widget pozisyon benzersizliği
        if (isset($updateData['position']) && isset($updateData['widget_area'])) {
            $exists = Widget::where('position', $updateData['position'])
                ->where('widget_area', $updateData['widget_area'])
                ->where('id', '!=', $widget->id)
                ->exists();
            
            if ($exists) {
                return true;
            }
        }

        // Title benzersizliği (aynı area'da)
        if (isset($updateData['title']) && isset($widget->widget_area)) {
            $exists = Widget::where('title', $updateData['title'])
                ->where('widget_area', $widget->widget_area)
                ->where('id', '!=', $widget->id)
                ->exists();
            
            if ($exists) {
                return true;
            }
        }

        return false;
    }

    public function failed(\Exception $exception): void
    {
        Log::error('BulkUpdateWidgets Job Failed', [
            'error' => $exception->getMessage(),
            'tenant' => $this->tenantId,
            'widget_ids_count' => count($this->widgetIds)
        ]);

        Cache::put($this->cacheKey, [
            'progress' => 0,
            'status' => 'failed',
            'message' => 'Toplu güncelleme başarısız oldu: ' . $exception->getMessage()
        ], 300);
    }
}