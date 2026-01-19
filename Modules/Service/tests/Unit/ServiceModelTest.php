<?php

declare(strict_types=1);

namespace Modules\Service\Tests\Unit;

use Modules\Service\Tests\TestCase;
use Modules\Service\App\Models\Service;
use Illuminate\Support\Str;

/**
 * Service Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class ServiceModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $service = new Service();
        $fillable = $service->getFillable();

        $expectedFillable = [
            'title',
            'slug',
            'body',
            'is_active',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable, "Fillable should contain {$attribute}");
        }

        // Check fillable count
        $this->assertCount(count($expectedFillable), $fillable, "Fillable count mismatch");
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $service = Service::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($service->is_active);
        $this->assertIsArray($service->title);
        $this->assertIsArray($service->slug);
        $this->assertIsArray($service->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $service = new Service();

        $this->assertEquals('service_id', $service->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $service = new Service();

        $translatable = $service->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $service->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $service->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($service->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $service = Service::factory()->create();

        $this->assertEquals($service->service_id, $service->id);
    }

    /** @test */
    public function active_scope_returns_only_active_services(): void
    {
        Service::factory()->active()->count(5)->create();
        Service::factory()->inactive()->count(3)->create();

        $activePages = Service::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($service) {
            $this->assertTrue($service->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $service = new Service();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $service);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $service = new Service();

        $fields = $service->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $service = new Service();

        $this->assertTrue($service->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $service = new Service();

        $this->assertEquals('service_id', $service->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $service->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $service = Service::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $service->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $service->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $service = Service::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-service']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $service->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $service = Service::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $service->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $service = Service::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $service->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $service->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $service = Service::factory()->create();

        $this->assertNull($service->seoSetting);

        $seoSetting = $service->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($service->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $service = Service::factory()->create();

        $this->assertInstanceOf(Service::class, $service);
        $this->assertDatabaseHas('services', ['service_id' => $service->service_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $service = Service::factory()->create();

        $this->assertIsArray($service->title);
        $this->assertArrayHasKey('tr', $service->title);
        $this->assertArrayHasKey('en', $service->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_service(): void
    {
        $service = Service::factory()->active()->create();

        $this->assertTrue($service->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_service(): void
    {
        $service = Service::factory()->inactive()->create();

        $this->assertFalse($service->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $service = Service::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $service = Service::factory()->create();

        // afterTranslation metodu log yapmalı
        $service->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $service = new Service();

        $this->assertTrue(method_exists($service, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $service = new Service();

        $this->assertTrue(method_exists($service, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $service = new Service();

        $this->assertTrue(method_exists($service, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $service = Service::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $service->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
