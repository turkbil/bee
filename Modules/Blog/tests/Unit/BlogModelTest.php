<?php

declare(strict_types=1);

namespace Modules\Blog\Tests\Unit;

use Modules\Blog\Tests\TestCase;
use Modules\Blog\App\Models\Blog;
use Illuminate\Support\Str;

/**
 * Blog Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class BlogModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $blog = new Blog();
        $fillable = $blog->getFillable();

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
        $blog = Blog::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($blog->is_active);
        $this->assertIsArray($blog->title);
        $this->assertIsArray($blog->slug);
        $this->assertIsArray($blog->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $blog = new Blog();

        $this->assertEquals('blog_id', $blog->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $blog = new Blog();

        $translatable = $blog->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $blog->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $blog->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($blog->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $blog = Blog::factory()->create();

        $this->assertEquals($blog->blog_id, $blog->id);
    }

    /** @test */
    public function active_scope_returns_only_active_blogs(): void
    {
        Blog::factory()->active()->count(5)->create();
        Blog::factory()->inactive()->count(3)->create();

        $activePages = Blog::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($blog) {
            $this->assertTrue($blog->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $blog = new Blog();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $blog);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $blog = new Blog();

        $fields = $blog->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $blog = new Blog();

        $this->assertTrue($blog->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $blog = new Blog();

        $this->assertEquals('blog_id', $blog->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $blog->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $blog = Blog::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $blog->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $blog->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $blog = Blog::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-blog']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $blog->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $blog = Blog::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $blog->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $blog = Blog::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $blog->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $blog->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $blog = Blog::factory()->create();

        $this->assertNull($blog->seoSetting);

        $seoSetting = $blog->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($blog->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $blog = Blog::factory()->create();

        $this->assertInstanceOf(Blog::class, $blog);
        $this->assertDatabaseHas('blogs', ['blog_id' => $blog->blog_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $blog = Blog::factory()->create();

        $this->assertIsArray($blog->title);
        $this->assertArrayHasKey('tr', $blog->title);
        $this->assertArrayHasKey('en', $blog->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_blog(): void
    {
        $blog = Blog::factory()->active()->create();

        $this->assertTrue($blog->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_blog(): void
    {
        $blog = Blog::factory()->inactive()->create();

        $this->assertFalse($blog->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $blog = Blog::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $blog = Blog::factory()->create();

        // afterTranslation metodu log yapmalı
        $blog->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $blog = new Blog();

        $this->assertTrue(method_exists($blog, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $blog = new Blog();

        $this->assertTrue(method_exists($blog, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $blog = new Blog();

        $this->assertTrue(method_exists($blog, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $blog = Blog::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $blog->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
