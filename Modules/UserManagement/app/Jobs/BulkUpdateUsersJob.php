<?php

namespace Modules\UserManagement\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\app\Models\User;
use Spatie\Activitylog\Models\Activity;

class BulkUpdateUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public array $userIds,
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

            $totalItems = count($this->userIds);
            $processedItems = 0;
            $successCount = 0;
            $errors = [];

            // İzin verilen alanlar (güvenlik)
            $allowedFields = [
                'name', 'email', 'email_verified_at', 
                'is_active', 'status', 'profile_photo_path'
            ];

            // Güvenlik kontrolü: Sadece izin verilen alanlar
            $updateData = array_intersect_key($this->updateData, array_flip($allowedFields));

            if (empty($updateData)) {
                throw new \Exception('Güncelleme için geçerli alan bulunamadı');
            }

            DB::beginTransaction();

            foreach ($this->userIds as $userId) {
                try {
                    $user = User::find($userId);
                    
                    if (!$user) {
                        $errors[] = "Kullanıcı bulunamadı: ID {$userId}";
                        continue;
                    }

                    // Güvenlik kontrolleri
                    if ($this->hasSecurityRestriction($user, $updateData)) {
                        $errors[] = "Güvenlik kısıtlaması: {$user->name} güncellenemez";
                        continue;
                    }

                    // Email benzersizlik kontrolü
                    if (isset($updateData['email']) && $this->emailExists($updateData['email'], $userId)) {
                        $errors[] = "Email zaten kullanımda: {$updateData['email']}";
                        continue;
                    }

                    // Kullanıcıyı güncelle
                    $oldData = $user->toArray();
                    $user->update($updateData);

                    // Activity log
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($user)
                        ->withProperties([
                            'old' => $oldData,
                            'attributes' => $user->fresh()->toArray(),
                            'bulk_operation' => true
                        ])
                        ->log('bulk_updated');

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Kullanıcı güncelleme hatası ({$user->name ?? "ID: {$userId}"}): " . $e->getMessage();
                    Log::error("BulkUpdateUsers Error", [
                        'user_id' => $userId,
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
                    'current_action' => "Kullanıcı güncelleniyor ({$processedItems}/{$totalItems})"
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
                'message' => "{$successCount} kullanıcı başarıyla güncellendi",
                'errors' => $errors
            ], 300);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('BulkUpdateUsers Job Failed', [
                'error' => $e->getMessage(),
                'tenant' => $this->tenantId,
                'user_ids_count' => count($this->userIds)
            ]);

            Cache::put($this->cacheKey, [
                'progress' => 0,
                'status' => 'failed',
                'message' => 'Toplu güncelleme başarısız: ' . $e->getMessage()
            ], 300);

            throw $e;
        }
    }

    private function hasSecurityRestriction(User $user, array $updateData): bool
    {
        // Kendi kendini güncelleme kontrolü - bazı alanlar kısıtlı
        if ($user->id == $this->userId) {
            $restrictedFields = ['is_active', 'status'];
            if (array_intersect_key($updateData, array_flip($restrictedFields))) {
                return true;
            }
        }

        // Super Admin kontrolü - durumu değiştirilemez
        if ($user->hasRole('Super Admin') && isset($updateData['is_active'])) {
            return true;
        }

        // Admin rolü kontrolü - sadece admin ve üstü güncelleyebilir
        if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
            $currentUser = User::find($this->userId);
            if (!$currentUser->hasAnyRole(['Admin', 'Super Admin'])) {
                return true;
            }
        }

        return false;
    }

    private function emailExists(string $email, int $excludeUserId): bool
    {
        return User::where('email', $email)
            ->where('id', '!=', $excludeUserId)
            ->exists();
    }

    public function failed(\Exception $exception): void
    {
        Log::error('BulkUpdateUsers Job Failed', [
            'error' => $exception->getMessage(),
            'tenant' => $this->tenantId,
            'user_ids_count' => count($this->userIds)
        ]);

        Cache::put($this->cacheKey, [
            'progress' => 0,
            'status' => 'failed',
            'message' => 'Toplu güncelleme başarısız oldu: ' . $exception->getMessage()
        ], 300);
    }
}