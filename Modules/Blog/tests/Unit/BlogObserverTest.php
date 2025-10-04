<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Unit;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * BlogObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class BlogObserverTest extends TestCase
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
        $blog = Blog::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Blog'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($blog->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($blog->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $blog->getTranslated('slug', 'tr'));
        $this->assertEquals('test-blog', $blog->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $blog = Blog::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Blog'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $blog->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $blog->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Blog::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Blog'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-blog'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $blog2 = Blog::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Blog'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $blog2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Blog::create([
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

        Blog::create([
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

        Blog::create([
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

        Blog::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_blog_deletion(): void
    {
        $blog = Blog::factory()->create();

        $blog->delete();

        $this->assertDatabaseMissing('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('blogs_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('blogs_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('blogs_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Blog::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $blog = Blog::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $blog->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $blog = Blog::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $blog->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $blog = Blog::factory()->create();
        $blog->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($blog),
            'seo_settingable_id' => $blog->blog_id
        ]);

        $blog->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($blog),
            'seo_settingable_id' => $blog->blog_id
        ]);
    }

    /** @test */
    public function it_logs_blog_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Blog creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Blog created successfully', \Mockery::any());

        Blog::factory()->create();
    }

    /** @test */
    public function it_logs_blog_update(): void
    {
        $blog = Blog::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Blog updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Blog updated successfully', \Mockery::any());

        $blog->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_blog_deletion(): void
    {
        $blog = Blog::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Blog deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Blog deleted successfully', \Mockery::any());

        $blog->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $blog = Blog::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Blog force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Blog force deleted', \Mockery::any());

        $blog->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $blog = Blog::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_blog_{$blog->blog_id}")
            ->once();

        $blog->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Blog updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $blog->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['blog.defaults.is_active' => false]);

        $blog = Blog::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($blog->is_active);
    }
}
