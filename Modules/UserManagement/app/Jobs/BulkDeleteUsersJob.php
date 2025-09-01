<?php

declare(strict_types=1);

namespace Modules\UserManagement\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Throwable;

/**
 * 🗑️ Bulk User Delete Queue Job
 * 
 * UserManagement modülünün bulk silme işlemleri için queue job:
 * - Toplu kullanıcı silme işlemleri için optimize edilmiş
 * - Admin kullanıcı koruması
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Page modülünden kopya alınmış template
 */
class BulkDeleteUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $userIds Silinecek kullanıcı ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $userIds,
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
            Log::info('🗑️ BULK USER DELETE STARTED', [
                'user_ids' => $this->userIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->userIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_users_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->userIds), 'starting');

            // Her kullanıcı için silme işlemi
            foreach ($this->userIds as $index => $userId) {
                try {
                    // Kullanıcı var mı kontrol et
                    $user = User::find($userId);
                    if (!$user) {
                        Log::warning("Kullanıcı bulunamadı: {$userId}");
                        continue;
                    }

                    // Kendi kendini silme kontrolü
                    if ($userId == $this->userId) {
                        $errors[] = "Kendi hesabınızı silemezsiniz: {$user->name}";
                        $errorCount++;
                        continue;
                    }

                    // Super admin kontrolü
                    if ($user->hasRole('Super Admin')) {
                        $errors[] = "Super Admin kullanıcısı silinemez: {$user->name}";
                        $errorCount++;
                        continue;
                    }

                    // Admin kontrolü - sadece diğer admin'ler admin silebilir
                    if ($user->hasRole('Admin')) {
                        $currentUser = User::find($this->userId);
                        if (!$currentUser || !$currentUser->hasRole(['Super Admin', 'Admin'])) {
                            $errors[] = "Admin kullanıcısını silme yetkiniz yok: {$user->name}";
                            $errorCount++;
                            continue;
                        }
                    }

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;
                    $userName = $user->name;
                    $userEmail = $user->email;
                    
                    if ($forceDelete) {
                        $user->forceDelete();
                    } else {
                        $user->delete();
                    }

                    $processedCount++;
                    
                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->userIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->userIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_user' => $userName
                    ]);

                    Log::info("✅ Kullanıcı silindi", [
                        'id' => $userId,
                        'name' => $userName,
                        'email' => $userEmail,
                        'force_delete' => $forceDelete
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Kullanıcı silme hatası (ID: {$userId}): " . $e->getMessage();
                    
                    Log::error("❌ Kullanıcı silme hatası", [
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearUserCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->userIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK USER DELETE COMPLETED', [
                'total_users' => count($this->userIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->userIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK USER DELETE FAILED', [
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
    private function clearUserCaches(): void
    {
        try {
            // User cache'leri temizle
            Cache::forget('users_list');
            Cache::forget('users_active');
            Cache::forget('users_admin');
            Cache::forget('roles_cache');
            Cache::forget('permissions_cache');
            
            // Pattern-based cache temizleme
            $patterns = [
                'user_*',
                'users_*',
                'role_*',
                'roles_*',
                'permission_*',
                'permissions_*'
            ];
            
            foreach ($patterns as $pattern) {
                Cache::tags(['users', 'roles', 'permissions'])->flush();
            }

            Log::info('🗑️ User caches cleared after bulk delete');
            
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK USER DELETE JOB FAILED', [
            'user_ids' => $this->userIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_users_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->userIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}