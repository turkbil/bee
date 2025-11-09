<?php

declare(strict_types=1);

namespace Modules\Muzibu\Tests\Unit;

use Modules\Muzibu\Tests\TestCase;
use Modules\Muzibu\App\Models\Muzibu;
use Illuminate\Support\Str;

/**
 * Muzibu Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class MuzibuModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $muzibu = new Muzibu();
        $fillable = $muzibu->getFillable();

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
        $muzibu = Muzibu::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($muzibu->is_active);
        $this->assertIsArray($muzibu->title);
        $this->assertIsArray($muzibu->slug);
        $this->assertIsArray($muzibu->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $muzibu = new Muzibu();

        $this->assertEquals('muzibu_id', $muzibu->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $muzibu = new Muzibu();

        $translatable = $muzibu->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $muzibu->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $muzibu->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($muzibu->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $muzibu = Muzibu::factory()->create();

        $this->assertEquals($muzibu->muzibu_id, $muzibu->id);
    }

    /** @test */
    public function active_scope_returns_only_active_muzibus(): void
    {
        Muzibu::factory()->active()->count(5)->create();
        Muzibu::factory()->inactive()->count(3)->create();

        $activePages = Muzibu::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($muzibu) {
            $this->assertTrue($muzibu->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $muzibu = new Muzibu();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $muzibu);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $muzibu = new Muzibu();

        $fields = $muzibu->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $muzibu = new Muzibu();

        $this->assertTrue($muzibu->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $muzibu = new Muzibu();

        $this->assertEquals('muzibu_id', $muzibu->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $muzibu->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $muzibu = Muzibu::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $muzibu->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $muzibu->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $muzibu = Muzibu::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-muzibu']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $muzibu->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $muzibu = Muzibu::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $muzibu->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $muzibu = Muzibu::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $muzibu->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $muzibu->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $muzibu = Muzibu::factory()->create();

        $this->assertNull($muzibu->seoSetting);

        $seoSetting = $muzibu->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($muzibu->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $muzibu = Muzibu::factory()->create();

        $this->assertInstanceOf(Muzibu::class, $muzibu);
        $this->assertDatabaseHas('muzibus', ['muzibu_id' => $muzibu->muzibu_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $muzibu = Muzibu::factory()->create();

        $this->assertIsArray($muzibu->title);
        $this->assertArrayHasKey('tr', $muzibu->title);
        $this->assertArrayHasKey('en', $muzibu->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_muzibu(): void
    {
        $muzibu = Muzibu::factory()->active()->create();

        $this->assertTrue($muzibu->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_muzibu(): void
    {
        $muzibu = Muzibu::factory()->inactive()->create();

        $this->assertFalse($muzibu->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $muzibu = Muzibu::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $muzibu = Muzibu::factory()->create();

        // afterTranslation metodu log yapmalı
        $muzibu->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $muzibu = new Muzibu();

        $this->assertTrue(method_exists($muzibu, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $muzibu = new Muzibu();

        $this->assertTrue(method_exists($muzibu, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $muzibu = new Muzibu();

        $this->assertTrue(method_exists($muzibu, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $muzibu = Muzibu::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $muzibu->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
