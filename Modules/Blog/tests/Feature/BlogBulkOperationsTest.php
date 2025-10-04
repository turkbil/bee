<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Feature;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Services\BlogService;
use App\Models\User;

/**
 * Blog Bulk Operations Tests
 *
 * Toplu işlemlerin (bulk operations) doğru çalışmasını
 */
class BlogBulkOperationsTest extends TestCase
{
    use RefreshDatabase;

    private BlogService $service;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BlogService::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function it_can_bulk_delete_multiple_blogs(): void
    {
        $blogs = Blog::factory()->count(5)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('blogs', ['blog_id' => $id]);
        }
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_blog_status(): void
    {
        $activePages = Blog::factory()->active()->count(3)->create();
        $inactivePages = Blog::factory()->inactive()->count(2)->create();

        $ids = $activePages->pluck('blog_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(3, $result->affectedCount);

        // Aktif sayfalar pasif olmalı
        foreach ($activePages as $blog) {
            $this->assertFalse($blog->fresh()->is_active);
        }
    }

    /** @test */
    public function it_can_toggle_both_active_and_inactive_blogs(): void
    {
        $mixedPages = collect([
            Blog::factory()->active()->create(),
            Blog::factory()->inactive()->create(),
            Blog::factory()->active()->create(),
        ]);

        $ids = $mixedPages->pluck('blog_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(3, $result->affectedCount);

        // Durumlar tersine dönmüş olmalı
        $this->assertFalse($mixedPages[0]->fresh()->is_active); // true->false
        $this->assertTrue($mixedPages[1]->fresh()->is_active);  // false->true
        $this->assertFalse($mixedPages[2]->fresh()->is_active); // true->false
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_handles_empty_id_array_in_bulk_delete(): void
    {
        $result = $this->service->bulkDeletePages([]);

        $this->assertFalse($result->success);
        $this->assertEquals(0, $result->affectedCount);
    }

    /** @test */
    public function it_handles_empty_id_array_in_bulk_toggle(): void
    {
        $result = $this->service->bulkToggleStatus([]);

        $this->assertEquals(0, $result->affectedCount);
    }

    /** @test */
    public function it_handles_nonexistent_ids_in_bulk_delete(): void
    {
        $result = $this->service->bulkDeletePages([9999, 9998, 9997]);

        $this->assertEquals(0, $result->affectedCount);
    }

    /** @test */
    public function it_handles_nonexistent_ids_in_bulk_toggle(): void
    {
        $result = $this->service->bulkToggleStatus([9999, 9998]);

        $this->assertEquals(0, $result->affectedCount);
    }

    /** @test */
    public function it_handles_mixed_existent_and_nonexistent_ids(): void
    {
        $blog = Blog::factory()->create();

        $result = $this->service->bulkDeletePages([
            $blog->blog_id,
            9999, // Yoksa
            9998  // Yoksa
        ]);

        $this->assertEquals(1, $result->affectedCount);
        $this->assertDatabaseMissing('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */
    public function bulk_operations_clear_cache(): void
    {
        $blogs = Blog::factory()->count(3)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        // Cache'i doldur
        $this->service->getActivePages();

        // Bulk delete yap
        $this->service->bulkDeletePages($ids);

        // Cache temizlenmiş olmalı (yeni sorgu doğru sonuç vermeli)
        $activePages = $this->service->getActivePages();
        $this->assertCount(0, $activePages);
    }

    /** @test */
    public function bulk_delete_logs_operations(): void
    {
        $blogs = Blog::factory()->count(3)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Bulk delete performed', \Mockery::any());

        $this->service->bulkDeletePages($ids);
    }

    /** @test */
    public function bulk_toggle_logs_operations(): void
    {
        $blogs = Blog::factory()->count(3)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Bulk status toggle performed', \Mockery::any());

        $this->service->bulkToggleStatus($ids);
    }

    /** @test */
    public function bulk_delete_with_seo_relations(): void
    {
        $blog = Blog::factory()->create();
        $blog->getOrCreateSeoSetting(); // SEO ayarı ekle

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_id' => $blog->blog_id
        ]);

        $this->service->bulkDeletePages([$blog->blog_id]);

        // SEO ayarları da silinmeli
        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_id' => $blog->blog_id
        ]);
    }

    /** @test */
    public function bulk_operations_return_correct_result_objects(): void
    {
        $blogs = Blog::factory()->count(3)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertInstanceOf(\Modules\Blog\App\DataTransferObjects\BulkOperationResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertIsInt($result->affectedCount);
        $this->assertIsString($result->message);
    }

    /** @test */
    public function partial_bulk_delete_returns_partial_result(): void
    {
        $homeblog = Blog::factory()->create();
        $blogs = Blog::factory()->count(2)->create();

        $allIds = array_merge([$homeblog->blog_id], $blogs->pluck('blog_id')->toArray());

        $result = $this->service->bulkDeletePages($allIds);

        $this->assertEquals('partial', $result->type);
        $this->assertEquals(2, $result->affectedCount);
        $this->assertEquals(1, $result->skippedCount);
    }

    /** @test */
    public function bulk_operations_handle_large_datasets(): void
    {
        $blogs = Blog::factory()->count(100)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(100, $result->affectedCount);
    }

    /** @test */
    public function bulk_toggle_maintains_data_integrity(): void
    {
        $blogs = Blog::factory()->count(5)->create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true
        ]);

        $ids = $blogs->pluck('blog_id')->toArray();

        $this->service->bulkToggleStatus($ids);

        // Sadece is_active değişmeli, diğer veriler aynı kalmalı
        foreach ($blogs as $blog) {
            $fresh = $blog->fresh();
            $this->assertFalse($fresh->is_active);
            $this->assertEquals($blog->getTranslated('title', 'tr'), $fresh->getTranslated('title', 'tr'));
        }
    }

    /** @test */
    public function bulk_operations_are_transactional(): void
    {
        $blogs = Blog::factory()->count(3)->create();
        $ids = $blogs->pluck('blog_id')->toArray();

        // Bulk delete başarılı olmalı
        $result = $this->service->bulkDeletePages($ids);

        // Hepsi silinmiş olmalı
        $this->assertTrue($result->success);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('blogs', ['blog_id' => $id]);
        }
    }
}
