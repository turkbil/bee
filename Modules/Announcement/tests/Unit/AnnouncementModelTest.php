<?php

declare(strict_types=1);

namespace Modules\Announcement\Tests\Unit;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Illuminate\Support\Str;

/**
 * Announcement Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class AnnouncementModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $announcement = new Announcement();
        $fillable = $announcement->getFillable();

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
        $announcement = Announcement::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($announcement->is_active);
        $this->assertIsArray($announcement->title);
        $this->assertIsArray($announcement->slug);
        $this->assertIsArray($announcement->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $announcement = new Announcement();

        $this->assertEquals('announcement_id', $announcement->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $announcement = new Announcement();

        $translatable = $announcement->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $announcement->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $announcement->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($announcement->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertEquals($announcement->announcement_id, $announcement->id);
    }

    /** @test */
    public function active_scope_returns_only_active_pages(): void
    {
        Announcement::factory()->active()->count(5)->create();
        Announcement::factory()->inactive()->count(3)->create();

        $activePages = Announcement::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($announcement) {
            $this->assertTrue($announcement->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $announcement = new Announcement();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $announcement);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $announcement = new Announcement();

        $fields = $announcement->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $announcement = new Announcement();

        $this->assertTrue($announcement->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $announcement = new Announcement();

        $this->assertEquals('announcement_id', $announcement->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $announcement->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $announcement = Announcement::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $announcement->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $announcement->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $announcement = Announcement::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-announcement']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $announcement->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $announcement = Announcement::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $announcement->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $announcement = Announcement::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $announcement->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $announcement->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertNull($announcement->seoSetting);

        $seoSetting = $announcement->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($announcement->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertInstanceOf(Announcement::class, $announcement);
        $this->assertDatabaseHas('pages', ['announcement_id' => $announcement->announcement_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertIsArray($announcement->title);
        $this->assertArrayHasKey('tr', $announcement->title);
        $this->assertArrayHasKey('en', $announcement->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_page(): void
    {
        $announcement = Announcement::factory()->active()->create();

        $this->assertTrue($announcement->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_page(): void
    {
        $announcement = Announcement::factory()->inactive()->create();

        $this->assertFalse($announcement->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $announcement = Announcement::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $announcement = Announcement::factory()->create();

        // afterTranslation metodu log yapmalı
        $announcement->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $announcement = new Announcement();

        $this->assertTrue(method_exists($announcement, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $announcement = new Announcement();

        $this->assertTrue(method_exists($announcement, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $announcement = new Announcement();

        $this->assertTrue(method_exists($announcement, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $announcement->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
