<?php

declare(strict_types=1);

namespace Modules\Favorite\Tests\Feature;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use Modules\Favorite\App\Services\FavoriteService;
use App\Models\User;

/**
 * Favorite Bulk Operations Tests
 *
 * Toplu işlemlerin (bulk operations) doğru çalışmasını
 */
class FavoriteBulkOperationsTest extends TestCase
{
    use RefreshDatabase;

    private FavoriteService $service;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(FavoriteService::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function it_can_bulk_delete_multiple_favorites(): void
    {
        $favorites = Favorite::factory()->count(5)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(5, $result->affectedCount);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('favorites', ['favorite_id' => $id]);
        }
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_can_bulk_toggle_favorite_status(): void
    {
        $activePages = Favorite::factory()->active()->count(3)->create();
        $inactivePages = Favorite::factory()->inactive()->count(2)->create();

        $ids = $activePages->pluck('favorite_id')->toArray();

        $result = $this->service->bulkToggleStatus($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(3, $result->affectedCount);

        // Aktif sayfalar pasif olmalı
        foreach ($activePages as $favorite) {
            $this->assertFalse($favorite->fresh()->is_active);
        }
    }

    /** @test */
    public function it_can_toggle_both_active_and_inactive_favorites(): void
    {
        $mixedPages = collect([
            Favorite::factory()->active()->create(),
            Favorite::factory()->inactive()->create(),
            Favorite::factory()->active()->create(),
        ]);

        $ids = $mixedPages->pluck('favorite_id')->toArray();

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
        $favorite = Favorite::factory()->create();

        $result = $this->service->bulkDeletePages([
            $favorite->favorite_id,
            9999, // Yoksa
            9998  // Yoksa
        ]);

        $this->assertEquals(1, $result->affectedCount);
        $this->assertDatabaseMissing('favorites', ['favorite_id' => $favorite->favorite_id]);
    }

    /** @test */
    public function bulk_operations_clear_cache(): void
    {
        $favorites = Favorite::factory()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

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
        $favorites = Favorite::factory()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Bulk delete performed', \Mockery::any());

        $this->service->bulkDeletePages($ids);
    }

    /** @test */
    public function bulk_toggle_logs_operations(): void
    {
        $favorites = Favorite::factory()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Bulk status toggle performed', \Mockery::any());

        $this->service->bulkToggleStatus($ids);
    }

    /** @test */
    public function bulk_delete_with_seo_relations(): void
    {
        $favorite = Favorite::factory()->create();
        $favorite->getOrCreateSeoSetting(); // SEO ayarı ekle

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_id' => $favorite->favorite_id
        ]);

        $this->service->bulkDeletePages([$favorite->favorite_id]);

        // SEO ayarları da silinmeli
        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_id' => $favorite->favorite_id
        ]);
    }

    /** @test */
    public function bulk_operations_return_correct_result_objects(): void
    {
        $favorites = Favorite::factory()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertInstanceOf(\Modules\Favorite\App\DataTransferObjects\BulkOperationResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertIsInt($result->affectedCount);
        $this->assertIsString($result->message);
    }

    /** @test */
    public function partial_bulk_delete_returns_partial_result(): void
    {
        $homefavorite = Favorite::factory()->create();
        $favorites = Favorite::factory()->count(2)->create();

        $allIds = array_merge([$homefavorite->favorite_id], $favorites->pluck('favorite_id')->toArray());

        $result = $this->service->bulkDeletePages($allIds);

        $this->assertEquals('partial', $result->type);
        $this->assertEquals(2, $result->affectedCount);
        $this->assertEquals(1, $result->skippedCount);
    }

    /** @test */
    public function bulk_operations_handle_large_datasets(): void
    {
        $favorites = Favorite::factory()->count(100)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        $result = $this->service->bulkDeletePages($ids);

        $this->assertTrue($result->success);
        $this->assertEquals(100, $result->affectedCount);
    }

    /** @test */
    public function bulk_toggle_maintains_data_integrity(): void
    {
        $favorites = Favorite::factory()->count(5)->create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'is_active' => true
        ]);

        $ids = $favorites->pluck('favorite_id')->toArray();

        $this->service->bulkToggleStatus($ids);

        // Sadece is_active değişmeli, diğer veriler aynı kalmalı
        foreach ($favorites as $favorite) {
            $fresh = $favorite->fresh();
            $this->assertFalse($fresh->is_active);
            $this->assertEquals($favorite->getTranslated('title', 'tr'), $fresh->getTranslated('title', 'tr'));
        }
    }

    /** @test */
    public function bulk_operations_are_transactional(): void
    {
        $favorites = Favorite::factory()->count(3)->create();
        $ids = $favorites->pluck('favorite_id')->toArray();

        // Bulk delete başarılı olmalı
        $result = $this->service->bulkDeletePages($ids);

        // Hepsi silinmiş olmalı
        $this->assertTrue($result->success);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('favorites', ['favorite_id' => $id]);
        }
    }
}
