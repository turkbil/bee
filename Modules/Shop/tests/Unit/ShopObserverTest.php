<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Unit;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\Shop;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * ShopObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class ShopObserverTest extends TestCase
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
        $shop = Shop::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Shop'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($shop->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($shop->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $shop->getTranslated('slug', 'tr'));
        $this->assertEquals('test-shop', $shop->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $shop = Shop::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Shop'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $shop->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $shop->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Shop::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Shop'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-shop'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $shop2 = Shop::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Shop'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $shop2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Shop::create([
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

        Shop::create([
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

        Shop::create([
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

        Shop::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_shop_deletion(): void
    {
        $shop = Shop::factory()->create();

        $shop->delete();

        $this->assertDatabaseMissing('shops', ['shop_id' => $shop->shop_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('shops_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('shops_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('shops_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Shop::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $shop = Shop::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $shop->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $shop = Shop::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $shop->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $shop = Shop::factory()->create();
        $shop->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($shop),
            'seo_settingable_id' => $shop->shop_id
        ]);

        $shop->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($shop),
            'seo_settingable_id' => $shop->shop_id
        ]);
    }

    /** @test */
    public function it_logs_shop_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Shop creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Shop created successfully', \Mockery::any());

        Shop::factory()->create();
    }

    /** @test */
    public function it_logs_shop_update(): void
    {
        $shop = Shop::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Shop updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Shop updated successfully', \Mockery::any());

        $shop->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_shop_deletion(): void
    {
        $shop = Shop::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Shop deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Shop deleted successfully', \Mockery::any());

        $shop->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $shop = Shop::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Shop force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Shop force deleted', \Mockery::any());

        $shop->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $shop = Shop::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_shop_{$shop->shop_id}")
            ->once();

        $shop->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Shop updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $shop->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['shop.defaults.is_active' => false]);

        $shop = Shop::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($shop->is_active);
    }
}
