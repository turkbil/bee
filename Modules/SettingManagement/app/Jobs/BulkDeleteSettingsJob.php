<?php

namespace Modules\SettingManagement\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SettingManagement\app\Models\SettingsValue;
use Spatie\Activitylog\Models\Activity;

class BulkDeleteSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public array $settingIds,
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

            $totalItems = count($this->settingIds);
            $processedItems = 0;
            $successCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($this->settingIds as $settingId) {
                try {
                    $setting = SettingsValue::find($settingId);
                    
                    if (!$setting) {
                        $errors[] = "Ayar bulunamadı: ID {$settingId}";
                        continue;
                    }

                    // Kritik ayarları koruma
                    if ($this->isProtectedSetting($setting)) {
                        $errors[] = "Korumalı ayar silinemez: {$setting->key}";
                        continue;
                    }

                    // Sistem ayarları kontrolü
                    if ($this->isSystemSetting($setting)) {
                        $errors[] = "Sistem ayarı silinemez: {$setting->key}";
                        continue;
                    }

                    // Activity log kaydet
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($setting)
                        ->withProperties([
                            'deleted_data' => $setting->toArray(),
                            'bulk_operation' => true
                        ])
                        ->log('bulk_deleted');

                    $setting->delete();
                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Ayar silme hatası ({$setting->key ?? "ID: {$settingId}"}): " . $e->getMessage();
                    Log::error("BulkDeleteSettings Error", [
                        'setting_id' => $settingId,
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
                    'current_action' => "Ayar siliniyor ({$processedItems}/{$totalItems})"
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
                'message' => "{$successCount} ayar başarıyla silindi",
                'errors' => $errors
            ], 300);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('BulkDeleteSettings Job Failed', [
                'error' => $e->getMessage(),
                'tenant' => $this->tenantId,
                'setting_ids_count' => count($this->settingIds)
            ]);

            Cache::put($this->cacheKey, [
                'progress' => 0,
                'status' => 'failed',
                'message' => 'Toplu silme başarısız: ' . $e->getMessage()
            ], 300);

            throw $e;
        }
    }

    private function isProtectedSetting($setting): bool
    {
        // Kritik sistem ayarları
        $protectedKeys = [
            'app_name', 'app_url', 'app_debug', 'app_env',
            'database_host', 'database_name', 'database_username',
            'mail_host', 'mail_port', 'mail_username',
            'cache_driver', 'session_driver', 'queue_driver',
            'tenant_domain', 'admin_email', 'system_timezone'
        ];

        if (isset($setting->key) && in_array($setting->key, $protectedKeys)) {
            return true;
        }

        // Güvenlik ile ilgili ayarlar
        if (isset($setting->key) && str_contains($setting->key, 'password')) {
            return true;
        }

        if (isset($setting->key) && str_contains($setting->key, 'secret')) {
            return true;
        }

        return false;
    }

    private function isSystemSetting($setting): bool
    {
        // Sistem tarafından yönetilen ayarlar
        if (isset($setting->is_system) && $setting->is_system) {
            return true;
        }

        // Grup bazında sistem ayarları
        $systemGroups = ['system', 'security', 'database', 'mail'];
        if (isset($setting->group) && in_array($setting->group, $systemGroups)) {
            return true;
        }

        // Özel sistem prefix'leri
        if (isset($setting->key) && str_starts_with($setting->key, 'system_')) {
            return true;
        }

        return false;
    }

    public function failed(\Exception $exception): void
    {
        Log::error('BulkDeleteSettings Job Failed', [
            'error' => $exception->getMessage(),
            'tenant' => $this->tenantId,
            'setting_ids_count' => count($this->settingIds)
        ]);

        Cache::put($this->cacheKey, [
            'progress' => 0,
            'status' => 'failed',
            'message' => 'Toplu silme başarısız oldu: ' . $exception->getMessage()
        ], 300);
    }
}