<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\Tests\Unit;

use Modules\ReviewSystem\Tests\TestCase;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Illuminate\Support\Str;

/**
 * ReviewSystem Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class ReviewSystemModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $reviewsystem = new ReviewSystem();
        $fillable = $reviewsystem->getFillable();

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
        $reviewsystem = ReviewSystem::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($reviewsystem->is_active);
        $this->assertIsArray($reviewsystem->title);
        $this->assertIsArray($reviewsystem->slug);
        $this->assertIsArray($reviewsystem->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertEquals('reviewsystem_id', $reviewsystem->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $reviewsystem = new ReviewSystem();

        $translatable = $reviewsystem->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $reviewsystem->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $reviewsystem->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($reviewsystem->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $this->assertEquals($reviewsystem->reviewsystem_id, $reviewsystem->id);
    }

    /** @test */
    public function active_scope_returns_only_active_reviewsystems(): void
    {
        ReviewSystem::factory()->active()->count(5)->create();
        ReviewSystem::factory()->inactive()->count(3)->create();

        $activePages = ReviewSystem::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($reviewsystem) {
            $this->assertTrue($reviewsystem->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $reviewsystem);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $reviewsystem = new ReviewSystem();

        $fields = $reviewsystem->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertTrue($reviewsystem->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertEquals('reviewsystem_id', $reviewsystem->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $reviewsystem->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $reviewsystem->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $reviewsystem->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-reviewsystem']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $reviewsystem->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $reviewsystem->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $reviewsystem->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $reviewsystem->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $this->assertNull($reviewsystem->seoSetting);

        $seoSetting = $reviewsystem->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($reviewsystem->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $this->assertInstanceOf(ReviewSystem::class, $reviewsystem);
        $this->assertDatabaseHas('reviewsystems', ['reviewsystem_id' => $reviewsystem->reviewsystem_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        $this->assertIsArray($reviewsystem->title);
        $this->assertArrayHasKey('tr', $reviewsystem->title);
        $this->assertArrayHasKey('en', $reviewsystem->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_reviewsystem(): void
    {
        $reviewsystem = ReviewSystem::factory()->active()->create();

        $this->assertTrue($reviewsystem->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_reviewsystem(): void
    {
        $reviewsystem = ReviewSystem::factory()->inactive()->create();

        $this->assertFalse($reviewsystem->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $reviewsystem = ReviewSystem::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $reviewsystem = ReviewSystem::factory()->create();

        // afterTranslation metodu log yapmalı
        $reviewsystem->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertTrue(method_exists($reviewsystem, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertTrue(method_exists($reviewsystem, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $reviewsystem = new ReviewSystem();

        $this->assertTrue(method_exists($reviewsystem, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $reviewsystem = ReviewSystem::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $reviewsystem->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
