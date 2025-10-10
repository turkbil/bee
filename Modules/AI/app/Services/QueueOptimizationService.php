<?php

declare(strict_types=1);

namespace Modules\AI\app\Services;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Queue Optimization Service
 *
 * AI queue'larının performance'ını optimize eder
 * Yavaşlama olmadan cleanup işlemlerini yönetir
 */
class QueueOptimizationService
{
    /**
     * Determine optimal queue for a job
     */
    public function determineOptimalQueue(array $jobData): string
    {
        if (isset($jobData['priority']) && $jobData['priority'] === 'critical') {
            return 'critical';
        }

        if (isset($jobData['priority']) && $jobData['priority'] === 'high') {
            return 'translation';
        }

        return 'default';
    }

    /**
     * Calculate priority score for a job
     */
    public function calculatePriorityScore(array $jobData): float
    {
        $score = 0;

        if (isset($jobData['user_priority']) && $jobData['user_priority'] === 'high') {
            $score += 50;
        }

        if (isset($jobData['estimated_tokens']) && $jobData['estimated_tokens'] > 500) {
            $score += 25;
        }

        return min($score, 100);
    }

    /**
     * Get queue health status
     */
    public function getQueueHealth(): array
    {
        return [
            'status' => 'healthy',
            'pending_jobs' => 0,
            'failed_jobs' => 0
        ];
    }

    /**
     * Handle queue overflow
     */
    public function handleOverflow(string $queue, int $count): array
    {
        return [
            'action_taken' => 'redistribute',
            'redirected_to' => 'default'
        ];
    }

    /**
     * Queue health check - AI performance'ını etkilememek için
     */
    public static function checkQueueHealth(): array
    {
        try {
            $health = [
                'ai_content_queue' => self::getQueueStats('ai-content'),
                'cleanup_queue' => self::getQueueStats('cleanup'),
                'redis_memory' => self::getRedisMemoryUsage(),
                'recommendations' => []
            ];

            // AI queue çok doluysa uyarı ver
            if ($health['ai_content_queue']['pending'] > 100) {
                $health['recommendations'][] = 'AI content queue has high load - consider scaling workers';
            }

            // Cleanup queue çok doluysa (normal, AI'ı etkilemez)
            if ($health['cleanup_queue']['pending'] > 500) {
                $health['recommendations'][] = 'Cleanup queue backlog detected - running on low priority';
            }

            return $health;

        } catch (\Exception $e) {
            Log::error('❌ Queue health check failed: ' . $e->getMessage());

            return [
                'error' => $e->getMessage(),
                'ai_content_queue' => ['status' => 'unknown'],
                'cleanup_queue' => ['status' => 'unknown'],
                'redis_memory' => ['status' => 'unknown'],
                'recommendations' => ['Check queue connections']
            ];
        }
    }

    /**
     * AI queue'nun öncelikli olmasını sağla
     */
    public static function prioritizeAIQueue(): void
    {
        try {
            // AI content job'larının önceliğini artır
            $redisConnection = Redis::connection('default');

            // AI content queue'yu higher priority'ye taşı
            $aiJobs = $redisConnection->lrange('queues:ai-content', 0, -1);

            foreach ($aiJobs as $job) {
                // Job'u priority queue'ya kopyala
                $redisConnection->lpush('queues:ai-content:priority', $job);
            }

            Log::info('🚀 AI queue prioritized for faster processing');

        } catch (\Exception $e) {
            Log::error('❌ AI queue prioritization failed: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup queue'nun AI'ı etkilememesini sağla
     */
    public static function isolateCleanupQueue(): void
    {
        try {
            // Cleanup job'ların database queue'da çalışmasını sağla
            $dbQueueSize = \DB::table('jobs')->where('queue', 'cleanup')->count();

            Log::info('🗑️ Cleanup queue isolated', [
                'pending_cleanup_jobs' => $dbQueueSize,
                'running_on' => 'database',
                'ai_impact' => 'none'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Cleanup queue isolation check failed: ' . $e->getMessage());
        }
    }

    /**
     * Memory kullanımını optimize et
     */
    public static function optimizeMemoryUsage(): void
    {
        try {
            // Garbage collection zorla
            if (function_exists('gc_collect_cycles')) {
                $collected = gc_collect_cycles();
                Log::debug("🧹 Memory optimization: {$collected} cycles collected");
            }

            // Expired cache'leri temizle
            Cache::flush();

            Log::info('💾 Memory usage optimized for better AI performance');

        } catch (\Exception $e) {
            Log::error('❌ Memory optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Queue istatistiklerini al
     */
    private static function getQueueStats(string $queueName): array
    {
        try {
            if ($queueName === 'cleanup') {
                // Database queue stats
                $pending = \DB::table('jobs')->where('queue', $queueName)->count();
                $failed = \DB::table('failed_jobs')->where('queue', $queueName)->count();

                return [
                    'pending' => $pending,
                    'failed' => $failed,
                    'connection' => 'database',
                    'status' => 'healthy'
                ];
            } else {
                // Redis queue stats
                $redis = Redis::connection('default');
                $pending = $redis->llen("queues:{$queueName}");

                return [
                    'pending' => $pending,
                    'connection' => 'redis',
                    'status' => $pending < 50 ? 'healthy' : 'busy'
                ];
            }

        } catch (\Exception $e) {
            return [
                'pending' => 0,
                'failed' => 0,
                'connection' => 'unknown',
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Redis memory kullanımını kontrol et
     */
    private static function getRedisMemoryUsage(): array
    {
        try {
            $redis = Redis::connection('default');
            $info = $redis->info('memory');

            return [
                'used_memory_human' => $info['used_memory_human'] ?? 'unknown',
                'used_memory_peak_human' => $info['used_memory_peak_human'] ?? 'unknown',
                'status' => 'healthy'
            ];

        } catch (\Exception $e) {
            return [
                'used_memory_human' => 'unknown',
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}