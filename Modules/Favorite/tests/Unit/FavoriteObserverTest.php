<?php

declare(strict_types=1);

namespace Modules\Favorite\Tests\Unit;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * FavoriteObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class FavoriteObserverTest extends TestCase
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
        $favorite = Favorite::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Favorite'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($favorite->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($favorite->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $favorite->getTranslated('slug', 'tr'));
        $this->assertEquals('test-favorite', $favorite->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $favorite = Favorite::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Favorite'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $favorite->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $favorite->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Favorite::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Favorite'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-favorite'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $favorite2 = Favorite::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Favorite'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $favorite2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Favorite::create([
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

        Favorite::create([
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

        Favorite::create([
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

        Favorite::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_favorite_deletion(): void
    {
        $favorite = Favorite::factory()->create();

        $favorite->delete();

        $this->assertDatabaseMissing('favorites', ['favorite_id' => $favorite->favorite_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('favorites_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('favorites_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('favorites_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Favorite::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $favorite = Favorite::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $favorite->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $favorite = Favorite::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $favorite->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $favorite = Favorite::factory()->create();
        $favorite->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($favorite),
            'seo_settingable_id' => $favorite->favorite_id
        ]);

        $favorite->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($favorite),
            'seo_settingable_id' => $favorite->favorite_id
        ]);
    }

    /** @test */
    public function it_logs_favorite_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Favorite creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Favorite created successfully', \Mockery::any());

        Favorite::factory()->create();
    }

    /** @test */
    public function it_logs_favorite_update(): void
    {
        $favorite = Favorite::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Favorite updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Favorite updated successfully', \Mockery::any());

        $favorite->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_favorite_deletion(): void
    {
        $favorite = Favorite::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Favorite deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Favorite deleted successfully', \Mockery::any());

        $favorite->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $favorite = Favorite::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Favorite force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Favorite force deleted', \Mockery::any());

        $favorite->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $favorite = Favorite::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_favorite_{$favorite->favorite_id}")
            ->once();

        $favorite->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Favorite updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $favorite->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['favorite.defaults.is_active' => false]);

        $favorite = Favorite::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($favorite->is_active);
    }
}
