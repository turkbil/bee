<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Unit;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * PortfolioObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class PortfolioObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    /** @test */
    public function it_generates_slug_automatically_on_create(): void
    {
        $portfolio = Portfolio::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Portfolio'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($portfolio->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($portfolio->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $portfolio->getTranslated('slug', 'tr'));
        $this->assertEquals('test-portfolio', $portfolio->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $portfolio = Portfolio::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Portfolio'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $portfolio->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $portfolio->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Portfolio::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Portfolio'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-portfolio'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $portfolio2 = Portfolio::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Portfolio'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $portfolio2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Portfolio::create([
            'title' => ['tr' => 'ab', 'en' => 'ab'], // 2 karakter (min 3)
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_title_max_length(): void
    {
        $longTitle = Str::random(200);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık maksimum');

        Portfolio::create([
            'title' => ['tr' => $longTitle, 'en' => $longTitle],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_css_size(): void
    {
        $largeCss = str_repeat('a', 60000); // 60KB (max 50KB)

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CSS içeriği maksimum boyutu');

        Portfolio::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_js_size(): void
    {
        $largeJs = str_repeat('a', 60000); // 60KB (max 50KB)

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('JavaScript içeriği maksimum boyutu');

        Portfolio::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_portfolio_deletion(): void
    {
        $portfolio = Portfolio::factory()->create();

        $portfolio->delete();

        $this->assertDatabaseMissing('portfolios', ['portfolio_id' => $portfolio->portfolio_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('portfolios_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('portfolios_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('portfolios_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Portfolio::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $portfolio = Portfolio::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $portfolio->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $portfolio = Portfolio::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $portfolio->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $portfolio = Portfolio::factory()->create();
        $portfolio->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($portfolio),
            'seo_settingable_id' => $portfolio->portfolio_id
        ]);

        $portfolio->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($portfolio),
            'seo_settingable_id' => $portfolio->portfolio_id
        ]);
    }

    /** @test */
    public function it_logs_portfolio_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio created successfully', \Mockery::any());

        Portfolio::factory()->create();
    }

    /** @test */
    public function it_logs_portfolio_update(): void
    {
        $portfolio = Portfolio::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio updated successfully', \Mockery::any());

        $portfolio->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_portfolio_deletion(): void
    {
        $portfolio = Portfolio::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Portfolio deleted successfully', \Mockery::any());

        $portfolio->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $portfolio = Portfolio::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Portfolio force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Portfolio force deleted', \Mockery::any());

        $portfolio->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $portfolio = Portfolio::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_portfolio_{$portfolio->portfolio_id}")
            ->once();

        $portfolio->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Portfolio updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $portfolio->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['portfolio.defaults.is_active' => false]);

        $portfolio = Portfolio::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($portfolio->is_active);
    }
}
