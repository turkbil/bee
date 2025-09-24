<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use Modules\AI\app\Services\QueueOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueueOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QueueOptimizationService $queueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queueService = app(QueueOptimizationService::class);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(QueueOptimizationService::class, $this->queueService);
    }

    /** @test */
    public function it_determines_optimal_queue_for_job()
    {
        $jobData = [
            'type' => 'translation',
            'priority' => 'high',
            'estimated_duration' => 30
        ];

        $queue = $this->queueService->determineOptimalQueue($jobData);

        $this->assertIsString($queue);
        $this->assertContains($queue, ['translation', 'ai', 'critical', 'default']);
    }

    /** @test */
    public function it_calculates_job_priority_score()
    {
        $jobData = [
            'type' => 'translation',
            'tenant_id' => 1,
            'user_priority' => 'high',
            'estimated_tokens' => 1000
        ];

        $score = $this->queueService->calculatePriorityScore($jobData);

        $this->assertIsNumeric($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    /** @test */
    public function it_monitors_queue_health()
    {
        $health = $this->queueService->getQueueHealth();

        $this->assertIsArray($health);
        $this->assertArrayHasKey('status', $health);
        $this->assertArrayHasKey('pending_jobs', $health);
        $this->assertArrayHasKey('failed_jobs', $health);
    }

    /** @test */
    public function it_handles_queue_overflow()
    {
        $result = $this->queueService->handleOverflow('translation', 100);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('action_taken', $result);
        $this->assertArrayHasKey('redirected_to', $result);
    }
}