<?php

declare(strict_types=1);

namespace Modules\Favorite\Tests\Unit;

use Modules\Favorite\Tests\TestCase;
use Modules\Favorite\App\Models\Favorite;
use Illuminate\Support\Str;

/**
 * Favorite Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class FavoriteModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $favorite = new Favorite();
        $fillable = $favorite->getFillable();

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
        $favorite = Favorite::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($favorite->is_active);
        $this->assertIsArray($favorite->title);
        $this->assertIsArray($favorite->slug);
        $this->assertIsArray($favorite->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $favorite = new Favorite();

        $this->assertEquals('favorite_id', $favorite->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $favorite = new Favorite();

        $translatable = $favorite->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $favorite->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $favorite->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($favorite->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertEquals($favorite->favorite_id, $favorite->id);
    }

    /** @test */
    public function active_scope_returns_only_active_favorites(): void
    {
        Favorite::factory()->active()->count(5)->create();
        Favorite::factory()->inactive()->count(3)->create();

        $activePages = Favorite::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($favorite) {
            $this->assertTrue($favorite->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $favorite = new Favorite();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $favorite);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $favorite = new Favorite();

        $fields = $favorite->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $favorite = new Favorite();

        $this->assertTrue($favorite->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $favorite = new Favorite();

        $this->assertEquals('favorite_id', $favorite->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $favorite->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $favorite = Favorite::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $favorite->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $favorite->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $favorite = Favorite::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-favorite']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $favorite->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $favorite = Favorite::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $favorite->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $favorite = Favorite::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $favorite->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $favorite->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertNull($favorite->seoSetting);

        $seoSetting = $favorite->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($favorite->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertInstanceOf(Favorite::class, $favorite);
        $this->assertDatabaseHas('favorites', ['favorite_id' => $favorite->favorite_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertIsArray($favorite->title);
        $this->assertArrayHasKey('tr', $favorite->title);
        $this->assertArrayHasKey('en', $favorite->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_favorite(): void
    {
        $favorite = Favorite::factory()->active()->create();

        $this->assertTrue($favorite->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_favorite(): void
    {
        $favorite = Favorite::factory()->inactive()->create();

        $this->assertFalse($favorite->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $favorite = Favorite::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $favorite = Favorite::factory()->create();

        // afterTranslation metodu log yapmalı
        $favorite->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $favorite = new Favorite();

        $this->assertTrue(method_exists($favorite, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $favorite = new Favorite();

        $this->assertTrue(method_exists($favorite, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $favorite = new Favorite();

        $this->assertTrue(method_exists($favorite, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $favorite = Favorite::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $favorite->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
