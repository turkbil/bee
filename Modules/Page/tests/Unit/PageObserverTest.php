<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Unit;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * PageObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class PageObserverTest extends TestCase
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
        $page = Page::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($page->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($page->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $page->getTranslated('slug', 'tr'));
        $this->assertEquals('test-page', $page->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $page = Page::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Page'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $page->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $page->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_ensures_only_one_homepage_on_create(): void
    {
        $homepage1 = Page::factory()->homepage()->create();
        $this->assertTrue($homepage1->is_homepage);

        $homepage2 = Page::create([
            'title' => ['tr' => 'Yeni Anasayfa', 'en' => 'New Homepage'],
            'slug' => ['tr' => 'yeni-anasayfa', 'en' => 'new-homepage'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_homepage' => true,
            'is_active' => true,
        ]);

        $this->assertTrue($homepage2->is_homepage);
        $this->assertFalse($homepage1->fresh()->is_homepage);
    }

    /** @test */
    public function it_ensures_only_one_homepage_on_update(): void
    {
        $homepage = Page::factory()->homepage()->create();
        $regularPage = Page::factory()->create();

        $regularPage->update(['is_homepage' => true]);

        $this->assertTrue($regularPage->is_homepage);
        $this->assertFalse($homepage->fresh()->is_homepage);
    }

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Page::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Page'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-page'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $page2 = Page::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Page'],
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

        Page::create([
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

        Page::create([
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

        Page::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'css' => $largeCss,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_js_size(): void
    {
        $largeJs = str_repeat('a', 60000); // 60KB (max 50KB)

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('JavaScript içeriği maksimum boyutu');

        Page::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'js' => $largeJs,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_prevents_homepage_deletion(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ana sayfa silinemez');

        $homepage->delete();
    }

    /** @test */
    public function it_allows_regular_page_deletion(): void
    {
        $page = Page::factory()->create();

        $page->delete();

        $this->assertDatabaseMissing('pages', ['page_id' => $page->page_id]);
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
            ->with('homepage_data')
            ->once();

        Page::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $page = Page::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $page->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $page = Page::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $page->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $page = Page::factory()->create();
        $page->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($page),
            'seo_settingable_id' => $page->page_id
        ]);

        $page->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($page),
            'seo_settingable_id' => $page->page_id
        ]);
    }

    /** @test */
    public function it_logs_page_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Page creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Page created successfully', \Mockery::any());

        Page::factory()->create();
    }

    /** @test */
    public function it_logs_page_update(): void
    {
        $page = Page::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Page updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Page updated successfully', \Mockery::any());

        $page->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_page_deletion(): void
    {
        $page = Page::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Page deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Page deleted successfully', \Mockery::any());

        $page->delete();
    }

    /** @test */
    public function it_prevents_force_deleting_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ana sayfa kalıcı olarak silinemez');

        $homepage->forceDelete();
    }

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $page = Page::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Page force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Page force deleted', \Mockery::any());

        $page->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $page = Page::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_page_{$page->page_id}")
            ->once();

        $page->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Page updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $page->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['page.defaults.is_active' => false]);

        $page = Page::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($page->is_active);
    }
}