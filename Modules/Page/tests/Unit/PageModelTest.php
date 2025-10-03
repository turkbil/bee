<?php

declare(strict_types=1);

namespace Modules\Page\Tests\Unit;

use Modules\Page\Tests\TestCase;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Str;

/**
 * Page Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class PageModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $page = new Page();
        $fillable = $page->getFillable();

        $expectedFillable = [
            'title',
            'slug',
            'body',
            'css',
            'js',
            'is_active',
            'is_homepage',
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
        $page = Page::factory()->create([
            'is_homepage' => true,
            'is_active' => true,
        ]);

        $this->assertIsBool($page->is_homepage);
        $this->assertIsBool($page->is_active);
        $this->assertIsArray($page->title);
        $this->assertIsArray($page->slug);
        $this->assertIsArray($page->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $page = new Page();

        $this->assertEquals('page_id', $page->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $page = new Page();

        $translatable = $page->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $page->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $page->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($page->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $page = Page::factory()->create();

        $this->assertEquals($page->page_id, $page->id);
    }

    /** @test */
    public function active_scope_returns_only_active_pages(): void
    {
        Page::factory()->active()->count(5)->create();
        Page::factory()->inactive()->count(3)->create();

        $activePages = Page::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($page) {
            $this->assertTrue($page->is_active);
        });
    }

    /** @test */
    public function homepage_scope_returns_only_homepage(): void
    {
        $homepage = Page::factory()->homepage()->create();
        Page::factory()->count(5)->create(['is_homepage' => false]);

        $result = Page::homepage()->first();

        $this->assertNotNull($result);
        $this->assertEquals($homepage->page_id, $result->page_id);
        $this->assertTrue($result->is_homepage);
    }

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $page = new Page();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $page);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $page = new Page();

        $fields = $page->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $page = new Page();

        $this->assertTrue($page->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $page = new Page();

        $this->assertEquals('page_id', $page->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $page->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $page = Page::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $page->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $page->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $page = Page::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-page']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $page->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $page = Page::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $page->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $page = Page::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $page->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $page->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $page = Page::factory()->create();

        $this->assertNull($page->seoSetting);

        $seoSetting = $page->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($page->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $page = Page::factory()->create();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertDatabaseHas('pages', ['page_id' => $page->page_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $page = Page::factory()->create();

        $this->assertIsArray($page->title);
        $this->assertArrayHasKey('tr', $page->title);
        $this->assertArrayHasKey('en', $page->title);
    }

    /** @test */
    public function factory_homepage_state_creates_homepage(): void
    {
        $page = Page::factory()->homepage()->create();

        $this->assertTrue($page->is_homepage);
        $this->assertTrue($page->is_active);
    }

    /** @test */
    public function factory_active_state_creates_active_page(): void
    {
        $page = Page::factory()->active()->create();

        $this->assertTrue($page->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_page(): void
    {
        $page = Page::factory()->inactive()->create();

        $this->assertFalse($page->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $page = Page::factory()->withCustomStyles()->create();

        $this->assertNotEmpty($page->css);
        $this->assertNotEmpty($page->js);
        $this->assertStringContainsString('.page-container', $page->css);
        $this->assertStringContainsString('DOMContentLoaded', $page->js);
    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $page = Page::factory()->create();

        // afterTranslation metodu log yapmalı
        $page->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $page = new Page();

        $this->assertTrue(method_exists($page, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $page = new Page();

        $this->assertTrue(method_exists($page, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $page = new Page();

        $this->assertTrue(method_exists($page, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $page = Page::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $page->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}