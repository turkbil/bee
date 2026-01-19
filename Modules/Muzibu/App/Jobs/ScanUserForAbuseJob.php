<?php

namespace Modules\Muzibu\App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Services\AbuseDetectionService;

/**
 * Suistimal Tarama Job'u - Ping-Pong Sistemi v2
 *
 * Bu job Horizon üzerinden çalışır ve kullanıcının
 * hesap paylaşımı yapıp yapmadığını tespit eder.
 *
 * Early Exit: Tek fingerprint'li kullanıcılar bu job'a
 * gönderilmeden önce quickCheck ile CLEAN işaretlenir.
 * Bu job sadece birden fazla fingerprint'li kullanıcılar için çalışır.
 *
 * @see AbuseDetectionService
 */
class ScanUserForAbuseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Taranacak kullanıcı ID
     */
    protected int $userId;

    /**
     * Tarama periyodu başlangıç tarihi
     */
    protected Carbon $periodStart;

    /**
     * Tarama periyodu bitiş tarihi
     */
    protected Carbon $periodEnd;

    /**
     * Tenant ID for multi-tenancy support
     */
    public ?int $tenantId = null;

    /**
     * Create a new job instance.
     *
     * @param int $userId Taranacak kullanıcı ID
     * @param Carbon $periodStart Dönem başlangıcı
     * @param Carbon $periodEnd Dönem sonu
     */
    public function __construct(int $userId, Carbon $periodStart, Carbon $periodEnd)
    {
        $this->userId = $userId;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;

        // Save tenant context
        $this->tenantId = tenant('id');

        // Horizon'da görünecek queue
        $this->onQueue('muzibu-abuse-scan');
    }

    /**
     * Execute the job.
     *
     * NOT: Bu job'a gelen kullanıcılar zaten Early Exit'i geçmiştir.
     * Yani birden fazla fingerprint'e sahiptirler ve detaylı analiz gerekir.
     */
    public function handle(AbuseDetectionService $service): void
    {
        // Restore tenant context
        if ($this->tenantId && (!tenant() || tenant('id') != $this->tenantId)) {
            tenancy()->initialize($this->tenantId);
        }

        $periodLabel = $this->periodStart->format('d.m.Y') . ' - ' . $this->periodEnd->format('d.m.Y');

        try {
            Log::info("[AbuseDetection] Scanning user #{$this->userId} for period: {$periodLabel}");

            // Detaylı analiz yap (3 pattern kontrolü)
            $report = $service->scanUser($this->userId, $this->periodStart, $this->periodEnd);

            if ($report) {
                // Pattern bilgilerini logla
                $patterns = $report->patterns_json ?? [];
                $detectedPatterns = [];

                if ($patterns['ping_pong']['detected'] ?? false) {
                    $detectedPatterns[] = 'ping_pong';
                }
                if ($patterns['concurrent_different']['detected'] ?? false) {
                    $detectedPatterns[] = 'concurrent_different';
                }
                if ($patterns['split_stream']['detected'] ?? false) {
                    $detectedPatterns[] = 'split_stream';
                }

                Log::info("[AbuseDetection] User #{$this->userId} scanned", [
                    'status' => $report->status,
                    'plays' => $report->total_plays,
                    'score' => $report->abuse_score,
                    'patterns_detected' => $detectedPatterns,
                ]);
            } else {
                Log::info("[AbuseDetection] User #{$this->userId} has no plays in period");
            }
        } catch (\Exception $e) {
            Log::error("[AbuseDetection] Error scanning user #{$this->userId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[AbuseDetection] Job failed for user #{$this->userId}", [
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'abuse-detection',
            'user:' . $this->userId,
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
