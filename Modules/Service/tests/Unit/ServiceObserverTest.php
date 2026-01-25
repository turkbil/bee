<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Unit;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * ServiceObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class ServiceObserverTest extends TestCase
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
        $service = Service::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Service'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($service->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($service->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $service->getTranslated('slug', 'tr'));
        $this->assertEquals('test-service', $service->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $service = Service::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Service'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $service->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $service->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Service::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Service'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-service'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $service2 = Service::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Service'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $service2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Service::create([
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

        Service::create([
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

        Service::create([
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

        Service::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_service_deletion(): void
    {
        $service = Service::factory()->create();

        $service->delete();

        $this->assertDatabaseMissing('services', ['service_id' => $service->service_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('services_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('services_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('services_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Service::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $service = Service::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $service->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $service = Service::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $service->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $service = Service::factory()->create();
        $service->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($service),
            'seo_settingable_id' => $service->service_id
        ]);

        $service->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($service),
            'seo_settingable_id' => $service->service_id
        ]);
    }

    /** @test */
    public function it_logs_service_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Service creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service created successfully', \Mockery::any());

        Service::factory()->create();
    }

    /** @test */
    public function it_logs_service_update(): void
    {
        $service = Service::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Service updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service updated successfully', \Mockery::any());

        $service->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_service_deletion(): void
    {
        $service = Service::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Service deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Service deleted successfully', \Mockery::any());

        $service->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $service = Service::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Service force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Service force deleted', \Mockery::any());

        $service->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $service = Service::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_service_{$service->service_id}")
            ->once();

        $service->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Service updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $service->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['service.defaults.is_active' => false]);

        $service = Service::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($service->is_active);
    }
}
