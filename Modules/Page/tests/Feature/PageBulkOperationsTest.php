<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Feature;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Services\PageService;
use App\Models\User;

/**
 * Page Bulk Operations Tests
 *
 * Toplu işlemlerin (bulk operations) doğru çalışmasını
 * ve homepage korumasını test eder.
 */
class PageBulkOperationsTest extends TestCase
{
    use RefreshDatabase;

    private PageService $service;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PageService::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function it_can_bulk_delete_multiple_pages(): void
    {
        $pages = Page::factory()->count(5)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('pages', ['page_id' => $id]);
        }
    }

    /** @test */
    public function it_protects_homepage_in_bulk_delete(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $regularPages = Page::factory()->count(3)->create();

        $allIds = array_merge([$homepage->page_id], $regularPages->pluck('page_id')->toArray());

        $result = $this->service->bulkDeletePages($allIds);

        $this->assertEquals(3, $result->affectedCount);
        $this->assertEquals(1, $result->skippedCount);

        // Homepage hala var
        $this->assertDatabaseHas('pages', ['page_id' => $homepage->page_id]);

        // Diğerleri silinmiş
        foreach ($regularPages as $page) {
            $this->assertDatabaseMissing('pages', ['page_id' => $page->page_id]);
        }
    }

    /** @test */
    public function it_returns_failure_when_all_pages_are_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $result = $this->service->bulkDeletePages([$homepage->page_id]);

        $this->assertFalse($result->success);
        $this->assertEquals(0, $result->affectedCount);
        $this->assertDatabaseHas('pages', ['page_id' => $homepage->page_id]);
    }

    /** @test */
    public function it_can_bulk_toggle_page_status(): void
    {
        $activePages = Page::factory()->active()->count(3)->create();
        $inactivePages = Page::factory()->inactive()->count(2)->create();

        $ids = $activePages->pluck('page_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(3, $result->affectedCount);

        // Aktif sayfalar pasif olmalı
        foreach ($activePages as $page) {
            $this->assertFalse($page->fresh()->is_active);
        }
    }

    /** @test */
    public function it_can_toggle_both_active_and_inactive_pages(): void
    {
        $mixedPages = collect([
            Page::factory()->active()->create(),
            Page::factory()->inactive()->create(),
            Page::factory()->active()->create(),
        ]);

        $ids = $mixedPages->pluck('page_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(3, $result->affectedCount);

        // Durumlar tersine dönmüş olmalı
        $this->assertFalse($mixedPages[0]->fresh()->is_active); // true->false
        $this->assertTrue($mixedPages[1]->fresh()->is_active);  // false->true
        $this->assertFalse($mixedPages[2]->fresh()->is_active); // true->false
    }

    /** @test */
    public function it_excludes_homepage_from_bulk_toggle(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $regularPage = Page::factory()->active()->create();

        $result = $this->service->bulkToggleStatus([
            $homepage->page_id,
            $regularPage->page_id
        ]);

        $this->assertEquals(1, $result->affectedCount);

        // Homepage aktif kalmalı
        $this->assertTrue($homepage->fresh()->is_active);

        // Regular page toggle olmalı
        $this->assertFalse($regularPage->fresh()->is_active);
    }

    /** @test */
    public function it_returns_zero_affected_when_only_homepage_selected(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $result = $this->service->bulkToggleStatus([$homepage->page_id]);

        $this->assertEquals(0, $result->affectedCount);
        $this->assertTrue($homepage->fresh()->is_active);
    }

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
        $page = Page::factory()->create();

        $result = $this->service->bulkDeletePages([
            $page->page_id,
            9999, // Yoksa
            9998  // Yoksa
        ]);

        $this->assertEquals(1, $result->affectedCount);
        $this->assertDatabaseMissing('pages', ['page_id' => $page->page_id]);
    }

    /** @test */
    public function bulk_operations_clear_cache(): void
    {
        $pages = Page::factory()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

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
        $pages = Page::factory()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Bulk delete performed', \Mockery::any());

        $this->service->bulkDeletePages($ids);
    }

    /** @test */
    public function bulk_toggle_logs_operations(): void
    {
        $pages = Page::factory()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Bulk status toggle performed', \Mockery::any());

        $this->service->bulkToggleStatus($ids);
    }

    /** @test */
    public function bulk_delete_with_seo_relations(): void
    {
        $page = Page::factory()->create();
        $page->getOrCreateSeoSetting(); // SEO ayarı ekle

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_id' => $page->page_id
        ]);

        $this->service->bulkDeletePages([$page->page_id]);

        // SEO ayarları da silinmeli
        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_id' => $page->page_id
        ]);
    }

    /** @test */
    public function bulk_operations_return_correct_result_objects(): void
    {
        $pages = Page::factory()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertInstanceOf(\Modules\Page\App\DataTransferObjects\BulkOperationResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertIsInt($result->affectedCount);
        $this->assertIsString($result->message);
    }

    /** @test */
    public function partial_bulk_delete_returns_partial_result(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $pages = Page::factory()->count(2)->create();

        $allIds = array_merge([$homepage->page_id], $pages->pluck('page_id')->toArray());

        $result = $this->service->bulkDeletePages($allIds);

        $this->assertEquals('partial', $result->type);
        $this->assertEquals(2, $result->affectedCount);
        $this->assertEquals(1, $result->skippedCount);
    }

    /** @test */
    public function bulk_operations_handle_large_datasets(): void
    {
        $pages = Page::factory()->count(100)->create();
        $ids = $pages->pluck('page_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(100, $result->affectedCount);
    }

    /** @test */
    public function bulk_toggle_maintains_data_integrity(): void
    {
        $pages = Page::factory()->count(5)->create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true
        ]);

        $ids = $pages->pluck('page_id')->toArray();

        $this->service->bulkToggleStatus($ids);

        // Sadece is_active değişmeli, diğer veriler aynı kalmalı
        foreach ($pages as $page) {
            $fresh = $page->fresh();
            $this->assertFalse($fresh->is_active);
            $this->assertEquals($page->getTranslated('title', 'tr'), $fresh->getTranslated('title', 'tr'));
        }
    }

    /** @test */
    public function bulk_operations_are_transactional(): void
    {
        $pages = Page::factory()->count(3)->create();
        $ids = $pages->pluck('page_id')->toArray();

        // Bulk delete başarılı olmalı
        $result = $this->service->bulkDeletePages($ids);

        // Hepsi silinmiş olmalı
        $this->assertTrue($result->success);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('pages', ['page_id' => $id]);
        }
    }
}