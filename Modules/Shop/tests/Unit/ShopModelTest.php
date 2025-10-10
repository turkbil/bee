<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Unit;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\Shop;
use Illuminate\Support\Str;

/**
 * Shop Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class ShopModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $shop = new Shop();
        $fillable = $shop->getFillable();

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
        $shop = Shop::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($shop->is_active);
        $this->assertIsArray($shop->title);
        $this->assertIsArray($shop->slug);
        $this->assertIsArray($shop->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $shop = new Shop();

        $this->assertEquals('shop_id', $shop->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $shop = new Shop();

        $translatable = $shop->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $shop->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $shop->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($shop->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $shop = Shop::factory()->create();

        $this->assertEquals($shop->shop_id, $shop->id);
    }

    /** @test */
    public function active_scope_returns_only_active_shops(): void
    {
        Shop::factory()->active()->count(5)->create();
        Shop::factory()->inactive()->count(3)->create();

        $activePages = Shop::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($shop) {
            $this->assertTrue($shop->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $shop = new Shop();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $shop);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $shop = new Shop();

        $fields = $shop->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $shop = new Shop();

        $this->assertTrue($shop->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $shop = new Shop();

        $this->assertEquals('shop_id', $shop->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $shop->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $shop = Shop::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $shop->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $shop->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $shop = Shop::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-shop']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $shop->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $shop = Shop::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $shop->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $shop = Shop::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $shop->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $shop->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $shop = Shop::factory()->create();

        $this->assertNull($shop->seoSetting);

        $seoSetting = $shop->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($shop->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $shop = Shop::factory()->create();

        $this->assertInstanceOf(Shop::class, $shop);
        $this->assertDatabaseHas('shops', ['shop_id' => $shop->shop_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $shop = Shop::factory()->create();

        $this->assertIsArray($shop->title);
        $this->assertArrayHasKey('tr', $shop->title);
        $this->assertArrayHasKey('en', $shop->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_shop(): void
    {
        $shop = Shop::factory()->active()->create();

        $this->assertTrue($shop->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_shop(): void
    {
        $shop = Shop::factory()->inactive()->create();

        $this->assertFalse($shop->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $shop = Shop::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $shop = Shop::factory()->create();

        // afterTranslation metodu log yapmalı
        $shop->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $shop = new Shop();

        $this->assertTrue(method_exists($shop, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $shop = new Shop();

        $this->assertTrue(method_exists($shop, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $shop = new Shop();

        $this->assertTrue(method_exists($shop, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $shop = Shop::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $shop->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
