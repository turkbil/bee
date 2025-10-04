<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Unit;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * PortfolioCategoryObserver Unit Tests
 */
class PortfolioCategoryObserverTest extends TestCase
{
    /** @test */
    public function it_auto_generates_slug_on_creating(): void
    {
        $category = PortfolioCategory::factory()->make([
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
        $category = PortfolioCategory::factory()->make([
            'is_active' => null
        ]);

        $category->save();

        $this->assertTrue($category->is_active);
    }

    /** @test */
    public function it_sets_default_sort_order_on_creating(): void
    {
        $category = PortfolioCategory::factory()->make([
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
            ->with('Portfolio Category creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio Category created successfully', \Mockery::any());

        PortfolioCategory::factory()->create();
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

        PortfolioCategory::factory()->create();
    }

    /** @test */
    public function it_generates_unique_slug_on_updating_if_taken(): void
    {
        $category1 = PortfolioCategory::factory()->create([
            'slug' => ['tr' => 'web-gelistirme']
        ]);

        $category2 = PortfolioCategory::factory()->create([
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
        $category = PortfolioCategory::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio Category updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio Category updated successfully', \Mockery::any());

        $category->title = ['tr' => 'Güncellenmiş Başlık'];
        $category->save();
    }

    /** @test */
    public function it_clears_cache_on_updated(): void
    {
        $category = PortfolioCategory::factory()->create();

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

        $category = PortfolioCategory::factory()->make([
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
            ->with('Portfolio Category title auto-trimmed', \Mockery::any());

        $longTitle = str_repeat('A', 200); // 200 karakter

        $category = PortfolioCategory::factory()->make([
            'title' => ['tr' => $longTitle]
        ]);

        $category->save();

        $this->assertEquals(191, strlen($category->getTranslated('title', 'tr')));
    }

    /** @test */
    public function it_prevents_deleting_category_with_portfolios(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bu kategoriye ait portfoliolar var');

        $category = PortfolioCategory::factory()->create();
        
        Portfolio::factory()->create([
            'portfolio_category_id' => $category->category_id
        ]);

        $category->delete();
    }

    /** @test */
    public function it_allows_deleting_category_without_portfolios(): void
    {
        $category = PortfolioCategory::factory()->create();

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
        $category = PortfolioCategory::factory()->create();
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
        $category = PortfolioCategory::factory()->create();

        Cache::shouldReceive('tags')
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->atLeast()->once();

        Cache::shouldReceive('forget')
            ->atLeast()->once();

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio Category deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio Category deleted successfully', \Mockery::any());

        $category->delete();
    }

    /** @test */
    public function it_clears_cache_on_deleted(): void
    {
        $category = PortfolioCategory::factory()->create();

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
        $category = PortfolioCategory::factory()->create();

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
        $category = PortfolioCategory::factory()->create();

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
            ->with('Portfolio Category restoring', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio Category restored successfully', \Mockery::any());

        $category->restore();
    }

    /** @test */
    public function it_force_deletes_category(): void
    {
        $category = PortfolioCategory::factory()->create();
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

        $this->assertDatabaseMissing('portfolio_categories', [
            'category_id' => $categoryId
        ]);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
