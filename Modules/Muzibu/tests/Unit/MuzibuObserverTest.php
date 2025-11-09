<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Unit;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * MuzibuObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class MuzibuObserverTest extends TestCase
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
        $muzibu = Muzibu::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Muzibu'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($muzibu->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($muzibu->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $muzibu->getTranslated('slug', 'tr'));
        $this->assertEquals('test-muzibu', $muzibu->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $muzibu = Muzibu::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Muzibu'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $muzibu->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $muzibu->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Muzibu::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Muzibu'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-muzibu'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $muzibu2 = Muzibu::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Muzibu'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $muzibu2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Muzibu::create([
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

        Muzibu::create([
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

        Muzibu::create([
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

        Muzibu::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_muzibu_deletion(): void
    {
        $muzibu = Muzibu::factory()->create();

        $muzibu->delete();

        $this->assertDatabaseMissing('muzibus', ['muzibu_id' => $muzibu->muzibu_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('muzibus_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('muzibus_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('muzibus_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Muzibu::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $muzibu = Muzibu::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $muzibu->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $muzibu = Muzibu::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $muzibu->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $muzibu = Muzibu::factory()->create();
        $muzibu->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($muzibu),
            'seo_settingable_id' => $muzibu->muzibu_id
        ]);

        $muzibu->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($muzibu),
            'seo_settingable_id' => $muzibu->muzibu_id
        ]);
    }

    /** @test */
    public function it_logs_muzibu_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu created successfully', \Mockery::any());

        Muzibu::factory()->create();
    }

    /** @test */
    public function it_logs_muzibu_update(): void
    {
        $muzibu = Muzibu::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu updated successfully', \Mockery::any());

        $muzibu->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_muzibu_deletion(): void
    {
        $muzibu = Muzibu::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Muzibu deleted successfully', \Mockery::any());

        $muzibu->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $muzibu = Muzibu::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Muzibu force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Muzibu force deleted', \Mockery::any());

        $muzibu->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $muzibu = Muzibu::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_muzibu_{$muzibu->muzibu_id}")
            ->once();

        $muzibu->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Muzibu updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $muzibu->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['muzibu.defaults.is_active' => false]);

        $muzibu = Muzibu::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($muzibu->is_active);
    }
}
