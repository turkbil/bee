<?php

namespace Tests\Feature\AI;

use Tests\TestCase;
use Modules\AI\app\Models\AIContentJob;
use Modules\AI\app\Jobs\AIContentGenerationJob;
use Modules\Page\App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class AIContentGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_ai_content_job()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $jobData = [
            'content_type' => 'page',
            'content_id' => $page->page_id,
            'target_language' => 'en',
            'source_language' => 'tr',
            'user_id' => $user->id,
            'tenant_id' => 'tenant_a'
        ];

        $job = AIContentJob::create($jobData + [
            'status' => 'pending',
            'parameters' => json_encode(['model' => 'claude-3-5-sonnet-20241022'])
        ]);

        $this->assertDatabaseHas('ai_content_jobs', [
            'content_type' => 'page',
            'content_id' => $page->page_id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_dispatches_ai_content_generation_job()
    {
        Queue::fake();

        $user = User::factory()->create();
        $page = Page::factory()->create();

        $jobData = [
            'content_type' => 'page',
            'content_id' => $page->page_id,
            'target_language' => 'en',
            'source_language' => 'tr',
            'user_id' => $user->id,
            'tenant_id' => 'tenant_a'
        ];

        $job = AIContentJob::create($jobData + [
            'status' => 'pending',
            'parameters' => json_encode(['model' => 'claude-3-5-sonnet-20241022'])
        ]);

        AIContentGenerationJob::dispatch($job);

        Queue::assertPushed(AIContentGenerationJob::class);
    }

    /** @test */
    public function it_handles_job_failure_gracefully()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $job = AIContentJob::create([
            'content_type' => 'page',
            'content_id' => $page->page_id,
            'target_language' => 'en',
            'source_language' => 'tr',
            'user_id' => $user->id,
            'tenant_id' => 'tenant_a',
            'status' => 'failed',
            'error_message' => 'API rate limit exceeded',
            'parameters' => json_encode(['model' => 'claude-3-5-sonnet-20241022'])
        ]);

        $this->assertEquals('failed', $job->status);
        $this->assertNotNull($job->error_message);
    }

    /** @test */
    public function it_tracks_processing_time()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $job = AIContentJob::create([
            'content_type' => 'page',
            'content_id' => $page->page_id,
            'target_language' => 'en',
            'source_language' => 'tr',
            'user_id' => $user->id,
            'tenant_id' => 'tenant_a',
            'status' => 'completed',
            'started_at' => now()->subMinutes(2),
            'completed_at' => now(),
            'parameters' => json_encode(['model' => 'claude-3-5-sonnet-20241022'])
        ]);

        $this->assertNotNull($job->started_at);
        $this->assertNotNull($job->completed_at);
        $this->assertTrue($job->completed_at->gt($job->started_at));
    }
}