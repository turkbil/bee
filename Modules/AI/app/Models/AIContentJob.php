<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Tenant;

/**
 * AI Content Job Model
 *
 * AI içerik üretimi job'larının durumunu takip eder
 */
class AIContentJob extends Model
{
    protected $table = 'ai_content_jobs';

    protected $fillable = [
        'session_id',
        'tenant_id',
        'user_id',
        'component',
        'parameters',
        'content_type',
        'page_title',
        'status',
        'progress_percentage',
        'progress_message',
        'generated_content',
        'credits_used',
        'meta_data',
        'error_message',
        'retry_count',
        'started_at',
        'completed_at',
        'failed_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'meta_data' => 'array',
        'progress_percentage' => 'integer',
        'credits_used' => 'integer',
        'retry_count' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    protected $dates = [
        'started_at',
        'completed_at',
        'failed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * İlişkiler
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scopes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByComponent(Builder $query, string $component): Builder
    {
        return $query->where('component', $component);
    }

    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Job durumunu güncelle
     */
    public function updateStatus(string $status, array $data = []): bool
    {
        $updateData = ['status' => $status];

        if ($status === 'processing' && !$this->started_at) {
            $updateData['started_at'] = now();
        }

        if ($status === 'completed') {
            $updateData['completed_at'] = now();
            $updateData['progress_percentage'] = 100;
        }

        if ($status === 'failed') {
            $updateData['failed_at'] = now();
        }

        // Ek verileri birleştir
        $updateData = array_merge($updateData, $data);

        return $this->update($updateData);
    }

    /**
     * Progress güncelle
     */
    public function updateProgress(int $percentage, string $message = ''): bool
    {
        return $this->update([
            'progress_percentage' => $percentage,
            'progress_message' => $message
        ]);
    }

    /**
     * Hata kaydet
     */
    public function recordError(string $error, bool $incrementRetry = true): bool
    {
        $data = [
            'status' => 'failed',
            'error_message' => $error,
            'failed_at' => now()
        ];

        if ($incrementRetry) {
            $data['retry_count'] = $this->retry_count + 1;
        }

        return $this->update($data);
    }

    /**
     * Başarılı sonucu kaydet
     */
    public function recordSuccess(string $content, int $creditsUsed, array $metaData = []): bool
    {
        return $this->update([
            'status' => 'completed',
            'generated_content' => $content,
            'credits_used' => $creditsUsed,
            'meta_data' => $metaData,
            'progress_percentage' => 100,
            'progress_message' => 'İçerik başarıyla üretildi',
            'completed_at' => now()
        ]);
    }

    /**
     * Job'un ne kadar sürdığünü hesapla
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        $endTime = $this->completed_at ?? $this->failed_at ?? now();
        return (int) $this->started_at->diffInSeconds($endTime);
    }

    /**
     * Job'un başarılı olup olmadığını kontrol et
     */
    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === 'completed' && !empty($this->generated_content);
    }

    /**
     * Kısa hata mesajı
     */
    public function getShortErrorAttribute(): ?string
    {
        if (!$this->error_message) {
            return null;
        }

        return \Str::limit($this->error_message, 100);
    }

    /**
     * İçerik uzunluğu
     */
    public function getContentLengthAttribute(): int
    {
        return strlen($this->generated_content ?? '');
    }

    /**
     * Session ID ile job bul
     */
    public static function findBySession(string $sessionId): ?self
    {
        return static::where('session_id', $sessionId)->first();
    }

    /**
     * Eski job'ları temizle (7 günden eski)
     */
    public static function cleanupOldJobs(int $days = 7): int
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Tenant istatistikleri
     */
    public static function getTenantStats(int $tenantId, int $days = 30): array
    {
        $baseQuery = static::forTenant($tenantId)
            ->where('created_at', '>=', now()->subDays($days));

        return [
            'total_jobs' => $baseQuery->count(),
            'completed_jobs' => $baseQuery->completed()->count(),
            'failed_jobs' => $baseQuery->failed()->count(),
            'total_credits_used' => $baseQuery->completed()->sum('credits_used'),
            'average_duration' => $baseQuery->completed()
                ->whereNotNull('started_at')
                ->whereNotNull('completed_at')
                ->get()
                ->avg('duration')
        ];
    }
}