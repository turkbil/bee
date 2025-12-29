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

        // Horizon'da görünecek queue
        $this->onQueue('muzibu-abuse-scan');
    }

    /**
     * Execute the job.
     */
    public function handle(AbuseDetectionService $service): void
    {
        $periodLabel = $this->periodStart->format('d.m.Y') . ' - ' . $this->periodEnd->format('d.m.Y');

        try {
            Log::info("[AbuseDetection] Scanning user #{$this->userId} for period: {$periodLabel}");

            $report = $service->scanUser($this->userId, $this->periodStart, $this->periodEnd);

            if ($report) {
                Log::info("[AbuseDetection] User #{$this->userId} scanned", [
                    'status' => $report->status,
                    'plays' => $report->total_plays,
                    'overlaps' => $report->overlap_count,
                    'score' => $report->abuse_score,
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
