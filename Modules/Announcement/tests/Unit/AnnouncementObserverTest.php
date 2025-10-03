<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Unit;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * AnnouncementObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class AnnouncementObserverTest extends TestCase
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
        $announcement = Announcement::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Announcement'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($announcement->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($announcement->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $announcement->getTranslated('slug', 'tr'));
        $this->assertEquals('test-announcement', $announcement->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $announcement = Announcement::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Announcement'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $announcement->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $announcement->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Announcement::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Announcement'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-announcement'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $page2 = Announcement::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Announcement'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $page2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Announcement::create([
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

        Announcement::create([
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

        Announcement::create([
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

        Announcement::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_page_deletion(): void
    {
        $announcement = Announcement::factory()->create();

        $announcement->delete();

        $this->assertDatabaseMissing('pages', ['announcement_id' => $announcement->announcement_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('pages_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('pages_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('pages_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Announcement::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $announcement = Announcement::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $announcement->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $announcement = Announcement::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $announcement->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $announcement = Announcement::factory()->create();
        $announcement->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($announcement),
            'seo_settingable_id' => $announcement->announcement_id
        ]);

        $announcement->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($announcement),
            'seo_settingable_id' => $announcement->announcement_id
        ]);
    }

    /** @test */
    public function it_logs_page_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Announcement creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Announcement created successfully', \Mockery::any());

        Announcement::factory()->create();
    }

    /** @test */
    public function it_logs_page_update(): void
    {
        $announcement = Announcement::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Announcement updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Announcement updated successfully', \Mockery::any());

        $announcement->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_page_deletion(): void
    {
        $announcement = Announcement::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Announcement deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Announcement deleted successfully', \Mockery::any());

        $announcement->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $announcement = Announcement::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Announcement force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Announcement force deleted', \Mockery::any());

        $announcement->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $announcement = Announcement::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_page_{$announcement->announcement_id}")
            ->once();

        $announcement->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Announcement updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $announcement->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['announcement.defaults.is_active' => false]);

        $announcement = Announcement::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($announcement->is_active);
    }
}
