<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\Tests\Unit;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * ReviewSystemObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class ReviewSystemObserverTest extends TestCase
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
        $reviewsystem = ReviewSystem::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test ReviewSystem'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($reviewsystem->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($reviewsystem->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $reviewsystem->getTranslated('slug', 'tr'));
        $this->assertEquals('test-reviewsystem', $reviewsystem->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $reviewsystem = ReviewSystem::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test ReviewSystem'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $reviewsystem->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $reviewsystem->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        ReviewSystem::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test ReviewSystem'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-reviewsystem'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $reviewsystem2 = ReviewSystem::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test ReviewSystem'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $reviewsystem2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        ReviewSystem::create([
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

        ReviewSystem::create([
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

        ReviewSystem::create([
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

        ReviewSystem::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_reviewsystem_deletion(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $reviewsystem->delete();

        $this->assertDatabaseMissing('reviewsystems', ['reviewsystem_id' => $reviewsystem->reviewsystem_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('reviewsystems_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('reviewsystems_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('reviewsystems_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        ReviewSystem::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $reviewsystem->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $reviewsystem->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();
        $reviewsystem->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($reviewsystem),
            'seo_settingable_id' => $reviewsystem->reviewsystem_id
        ]);

        $reviewsystem->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($reviewsystem),
            'seo_settingable_id' => $reviewsystem->reviewsystem_id
        ]);
    }

    /** @test */
    public function it_logs_reviewsystem_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem created successfully', \Mockery::any());

        ReviewSystem::factory()->create();
    }

    /** @test */
    public function it_logs_reviewsystem_update(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem updated successfully', \Mockery::any());

        $reviewsystem->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_reviewsystem_deletion(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('ReviewSystem deleted successfully', \Mockery::any());

        $reviewsystem->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('ReviewSystem force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('ReviewSystem force deleted', \Mockery::any());

        $reviewsystem->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_reviewsystem_{$reviewsystem->reviewsystem_id}")
            ->once();

        $reviewsystem->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('ReviewSystem updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $reviewsystem->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['reviewsystem.defaults.is_active' => false]);

        $reviewsystem = ReviewSystem::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($reviewsystem->is_active);
    }
}
