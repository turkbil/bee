<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Unit;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\ServiceCategory;
use Modules\Service\App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * ServiceCategoryObserver Unit Tests
 */
class ServiceCategoryObserverTest extends TestCase
{
    /** @test */
    public function it_auto_generates_slug_on_creating(): void
    {
        $category = ServiceCategory::factory()->make([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development'],
            'slug' => null
        ]);

        $category->save();

        $this->assertNotNull($category->slug);
        $this->assertEquals('web-gelistirme', $category->getTranslated('slug', 'tr'));
        $this->assertEquals('web-development', $category->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_sets_default_is_active_on_creating(): void
    {
        $category = ServiceCategory::factory()->make([
            'is_active' => null
        ]);

        $category->save();

        $this->assertTrue($category->is_active);
    }

    /** @test */
    public function it_sets_default_sort_order_on_creating(): void
    {
        $category = ServiceCategory::factory()->make([
            'sort_order' => null
        ]);

        $category->save();

        $this->assertEquals(0, $category->sort_order);
    }

    /** @test */
    public function it_logs_on_creating(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Service Category creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category created successfully', \Mockery::any());

        ServiceCategory::factory()->create();
    }

    /** @test */
    public function it_clears_cache_on_created(): void
    {
        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        ServiceCategory::factory()->create();
    }

    /** @test */
    public function it_generates_unique_slug_on_updating_if_taken(): void
    {
        $category1 = ServiceCategory::factory()->create([
            'slug' => ['tr' => 'web-gelistirme']
        ]);

        $category2 = ServiceCategory::factory()->create([
            'slug' => ['tr' => 'mobil-gelistirme']
        ]);

        // category2'nin slug'ını category1 ile aynı yapmaya çalış
        $category2->slug = ['tr' => 'web-gelistirme'];
        $category2->save();

        // Observer otomatik olarak benzersiz slug oluşturmalı
        $this->assertStringContainsString('web-gelistirme', $category2->fresh()->getTranslated('slug', 'tr'));
        $this->assertNotEquals('web-gelistirme', $category2->fresh()->getTranslated('slug', 'tr'));
    }

    /** @test */
    public function it_logs_on_updating(): void
    {
        $category = ServiceCategory::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category updated successfully', \Mockery::any());

        $category->title = ['tr' => 'Güncellenmiş Başlık'];
        $category->save();
    }

    /** @test */
    public function it_clears_cache_on_updated(): void
    {
        $category = ServiceCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        $category->title = ['tr' => 'Güncellenmiş Başlık'];
        $category->save();
    }

    /** @test */
    public function it_validates_title_minimum_length_on_saving(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('en az 2 karakter');

        $category = ServiceCategory::factory()->make([
            'title' => ['tr' => 'A'] // Çok kısa
        ]);

        $category->save();
    }

    /** @test */
    public function it_auto_trims_long_title_on_saving(): void
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')
            ->once()
            ->with('Service Category title auto-trimmed', \Mockery::any());

        $longTitle = str_repeat('A', 200); // 200 karakter

        $category = ServiceCategory::factory()->make([
            'title' => ['tr' => $longTitle]
        ]);

        $category->save();

        $this->assertEquals(191, strlen($category->getTranslated('title', 'tr')));
    }

    /** @test */
    public function it_prevents_deleting_category_with_services(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bu kategoriye ait servicelar var');

        $category = ServiceCategory::factory()->create();
        
        Service::factory()->create([
            'service_category_id' => $category->category_id
        ]);

        $category->delete();
    }

    /** @test */
    public function it_allows_deleting_category_without_services(): void
    {
        $category = ServiceCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')->atLeast()->once();

        $result = $category->delete();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_deletes_seo_setting_on_deleted(): void
    {
        $category = ServiceCategory::factory()->create();
        $category->getOrCreateSeoSetting();

        $seoSettingId = $category->seoSetting->id;

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')->atLeast()->once();

        $category->delete();

        $this->assertDatabaseMissing('seo_settings', ['id' => $seoSettingId]);
    }

    /** @test */
    public function it_logs_on_deleting(): void
    {
        $category = ServiceCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category deleted successfully', \Mockery::any());

        $category->delete();
    }

    /** @test */
    public function it_clears_cache_on_deleted(): void
    {
        $category = ServiceCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')->atLeast()->once();

        $category->delete();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_restores_soft_deleted_category(): void
    {
        $category = ServiceCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')->atLeast()->once();

        $category->delete();

        $category->restore();

        $this->assertNull($category->deleted_at);
    }

    /** @test */
    public function it_logs_on_restoring(): void
    {
        $category = ServiceCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')->atLeast()->once();

        $category->delete();

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category restoring', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service Category restored successfully', \Mockery::any());

        $category->restore();
    }

    /** @test */
    public function it_force_deletes_category(): void
    {
        $category = ServiceCategory::factory()->create();
        $categoryId = $category->category_id;

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->twice();

        $category->forceDelete();

        $this->assertDatabaseMissing('service_categories', [
            'category_id' => $categoryId
        ]);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
