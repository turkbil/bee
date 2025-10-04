<?php

declare(strict_types=1);

namespace Modules\Portfolio\Tests\Unit;

use Modules\Portfolio\Tests\TestCase;
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Str;

/**
 * Portfolio Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class PortfolioModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $portfolio = new Portfolio();
        $fillable = $portfolio->getFillable();

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
        $portfolio = Portfolio::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($portfolio->is_active);
        $this->assertIsArray($portfolio->title);
        $this->assertIsArray($portfolio->slug);
        $this->assertIsArray($portfolio->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $portfolio = new Portfolio();

        $this->assertEquals('portfolio_id', $portfolio->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $portfolio = new Portfolio();

        $translatable = $portfolio->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $portfolio->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $portfolio->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($portfolio->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $portfolio = Portfolio::factory()->create();

        $this->assertEquals($portfolio->portfolio_id, $portfolio->id);
    }

    /** @test */
    public function active_scope_returns_only_active_portfolios(): void
    {
        Portfolio::factory()->active()->count(5)->create();
        Portfolio::factory()->inactive()->count(3)->create();

        $activePages = Portfolio::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($portfolio) {
            $this->assertTrue($portfolio->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $portfolio = new Portfolio();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $portfolio);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $portfolio = new Portfolio();

        $fields = $portfolio->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $portfolio = new Portfolio();

        $this->assertTrue($portfolio->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $portfolio = new Portfolio();

        $this->assertEquals('portfolio_id', $portfolio->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $portfolio->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $portfolio = Portfolio::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $portfolio->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $portfolio->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $portfolio = Portfolio::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-portfolio']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $portfolio->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $portfolio = Portfolio::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $portfolio->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $portfolio = Portfolio::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $portfolio->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $portfolio->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $portfolio = Portfolio::factory()->create();

        $this->assertNull($portfolio->seoSetting);

        $seoSetting = $portfolio->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($portfolio->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $portfolio = Portfolio::factory()->create();

        $this->assertInstanceOf(Portfolio::class, $portfolio);
        $this->assertDatabaseHas('portfolios', ['portfolio_id' => $portfolio->portfolio_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $portfolio = Portfolio::factory()->create();

        $this->assertIsArray($portfolio->title);
        $this->assertArrayHasKey('tr', $portfolio->title);
        $this->assertArrayHasKey('en', $portfolio->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_portfolio(): void
    {
        $portfolio = Portfolio::factory()->active()->create();

        $this->assertTrue($portfolio->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_portfolio(): void
    {
        $portfolio = Portfolio::factory()->inactive()->create();

        $this->assertFalse($portfolio->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $portfolio = Portfolio::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $portfolio = Portfolio::factory()->create();

        // afterTranslation metodu log yapmalı
        $portfolio->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $portfolio = new Portfolio();

        $this->assertTrue(method_exists($portfolio, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $portfolio = new Portfolio();

        $this->assertTrue(method_exists($portfolio, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $portfolio = new Portfolio();

        $this->assertTrue(method_exists($portfolio, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $portfolio = Portfolio::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $portfolio->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
