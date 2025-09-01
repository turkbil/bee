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

class BulkUpdateSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public array $settingIds,
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

            $totalItems = count($this->settingIds);
            $processedItems = 0;
            $successCount = 0;
            $errors = [];

            // İzin verilen alanlar
            $allowedFields = [
                'key', 'value', 'type', 'description',
                'is_active', 'is_public', 'validation_rules',
                'options', 'group_id', 'sort_order'
            ];

            // Güvenlik kontrolü: Sadece izin verilen alanlar
            $updateData = array_intersect_key($this->updateData, array_flip($allowedFields));

            if (empty($updateData)) {
                throw new \Exception('Güncelleme için geçerli alan bulunamadı');
            }

            DB::beginTransaction();

            foreach ($this->settingIds as $settingId) {
                try {
                    $setting = SettingsValue::find($settingId);
                    
                    if (!$setting) {
                        $errors[] = "Ayar bulunamadı: ID {$settingId}";
                        continue;
                    }

                    // Güncelleme kısıtlaması kontrolü
                    if ($this->hasUpdateRestriction($setting, $updateData)) {
                        $errors[] = "Güncelleme kısıtlaması: {$setting->key} güncellenemez";
                        continue;
                    }

                    // Key benzersizlik kontrolü
                    if ($this->hasKeyConflict($setting, $updateData)) {
                        $errors[] = "Key çakışması: {$updateData['key'] ?? $setting->key}";
                        continue;
                    }

                    // Değer validasyonu
                    if ($this->hasValidationError($setting, $updateData)) {
                        $errors[] = "Validation hatası: {$setting->key}";
                        continue;
                    }

                    // Ayarı güncelle
                    $oldData = $setting->toArray();
                    $setting->update($updateData);

                    // Activity log
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($setting)
                        ->withProperties([
                            'old' => $oldData,
                            'attributes' => $setting->fresh()->toArray(),
                            'bulk_operation' => true
                        ])
                        ->log('bulk_updated');

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Ayar güncelleme hatası ({$setting->key ?? "ID: {$settingId}"}): " . $e->getMessage();
                    Log::error("BulkUpdateSettings Error", [
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
                    'current_action' => "Ayar güncelleniyor ({$processedItems}/{$totalItems})"
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
                'message' => "{$successCount} ayar başarıyla güncellendi",
                'errors' => $errors
            ], 300);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('BulkUpdateSettings Job Failed', [
                'error' => $e->getMessage(),
                'tenant' => $this->tenantId,
                'setting_ids_count' => count($this->settingIds)
            ]);

            Cache::put($this->cacheKey, [
                'progress' => 0,
                'status' => 'failed',
                'message' => 'Toplu güncelleme başarısız: ' . $e->getMessage()
            ], 300);

            throw $e;
        }
    }

    private function hasUpdateRestriction($setting, array $updateData): bool
    {
        // Sistem ayarları sadece belirli alanları güncelleyebilir
        if (isset($setting->is_system) && $setting->is_system) {
            $restrictedFields = ['key', 'type'];
            if (array_intersect_key($updateData, array_flip($restrictedFields))) {
                return true;
            }
        }

        // Korumalı ayarların key'i değiştirilemez
        $protectedKeys = [
            'app_name', 'app_url', 'database_host', 'mail_host'
        ];
        
        if (isset($setting->key, $updateData['key']) 
            && in_array($setting->key, $protectedKeys) 
            && $updateData['key'] !== $setting->key) {
            return true;
        }

        // Güvenlik ayarlarının değeri doğrudan güncellenemez
        if (isset($setting->key) && str_contains($setting->key, 'password')) {
            if (isset($updateData['value'])) {
                return true; // Password değerleri özel işlemden geçmeli
            }
        }

        return false;
    }

    private function hasKeyConflict($setting, array $updateData): bool
    {
        // Key benzersizlik kontrolü
        if (isset($updateData['key'])) {
            $exists = SettingsValue::where('key', $updateData['key'])
                ->where('id', '!=', $setting->id)
                ->exists();
            
            if ($exists) {
                return true;
            }
        }

        return false;
    }

    private function hasValidationError($setting, array $updateData): bool
    {
        // Type kontrolü
        if (isset($updateData['type'], $updateData['value'])) {
            switch ($updateData['type']) {
                case 'boolean':
                    if (!in_array($updateData['value'], ['0', '1', 'true', 'false', true, false])) {
                        return true;
                    }
                    break;
                case 'integer':
                    if (!is_numeric($updateData['value'])) {
                        return true;
                    }
                    break;
                case 'json':
                    if (!is_array($updateData['value']) && !is_valid_json($updateData['value'])) {
                        return true;
                    }
                    break;
            }
        }

        // Validation rules kontrolü
        if (isset($setting->validation_rules, $updateData['value'])) {
            // Basit validation kuralları kontrolü
            $rules = json_decode($setting->validation_rules, true);
            if ($rules && isset($rules['required']) && $rules['required'] && empty($updateData['value'])) {
                return true;
            }
        }

        return false;
    }

    public function failed(\Exception $exception): void
    {
        Log::error('BulkUpdateSettings Job Failed', [
            'error' => $exception->getMessage(),
            'tenant' => $this->tenantId,
            'setting_ids_count' => count($this->settingIds)
        ]);

        Cache::put($this->cacheKey, [
            'progress' => 0,
            'status' => 'failed',
            'message' => 'Toplu güncelleme başarısız oldu: ' . $exception->getMessage()
        ], 300);
    }
}

// Helper function
if (!function_exists('is_valid_json')) {
    function is_valid_json($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}