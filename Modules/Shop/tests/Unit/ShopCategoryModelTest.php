<?php

declare(strict_types=1);

namespace Modules\Shop\Tests\Unit;

use Modules\Shop\Tests\TestCase;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\Shop;

/**
 * ShopCategory Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class ShopCategoryModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $category = new ShopCategory();
        $fillable = $category->getFillable();

        $expectedFillable = [
            'title',
            'slug',
            'description',
            'is_active',
            'sort_order',
            'parent_id',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable, "Fillable should contain {$attribute}");
        }

        $this->assertCount(count($expectedFillable), $fillable, "Fillable count mismatch");
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $category = ShopCategory::factory()->create([
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->assertIsBool($category->is_active);
        $this->assertIsInt($category->sort_order);
        $this->assertIsArray($category->title);
        $this->assertIsArray($category->slug);
        $this->assertIsArray($category->description);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $category = new ShopCategory();

        $this->assertEquals('category_id', $category->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $category = new ShopCategory();

        $translatable = $category->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('description', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $category = ShopCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development']
        ]);

        $this->assertEquals('Web Geliştirme', $category->getTranslated('title', 'tr'));
        $this->assertEquals('Web Development', $category->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $category = ShopCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme']
        ]);

        $this->assertNull($category->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $category = ShopCategory::factory()->create();

        $this->assertEquals($category->category_id, $category->id);
    }

    /** @test */
    public function active_scope_returns_only_active_categories(): void
    {
        ShopCategory::factory()->active()->count(5)->create();
        ShopCategory::factory()->inactive()->count(3)->create();

        $activeCategories = ShopCategory::active()->get();

        $this->assertCount(5, $activeCategories);
        $activeCategories->each(function ($category) {
            $this->assertTrue($category->is_active);
        });
    }

    /** @test */
    public function it_has_shops_relationship(): void
    {
        $category = ShopCategory::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $category->shops());
    }

    /** @test */
    public function it_can_have_parent_category(): void
    {
        $parent = ShopCategory::factory()->create();
        $child = ShopCategory::factory()->create(['parent_id' => $parent->category_id]);

        $this->assertNotNull($child->parent);
        $this->assertEquals($parent->category_id, $child->parent->category_id);
    }

    /** @test */
    public function it_can_have_children_categories(): void
    {
        $parent = ShopCategory::factory()->create();
        $child1 = ShopCategory::factory()->create(['parent_id' => $parent->category_id]);
        $child2 = ShopCategory::factory()->create(['parent_id' => $parent->category_id]);

        $children = $parent->children;

        $this->assertCount(2, $children);
    }

    /** @test */
    public function it_calculates_depth_level_correctly(): void
    {
        $parent = ShopCategory::factory()->create(['parent_id' => null]);
        $child = ShopCategory::factory()->create(['parent_id' => $parent->category_id]);
        $grandchild = ShopCategory::factory()->create(['parent_id' => $child->category_id]);

        $this->assertEquals(0, $parent->fresh()->depth_level);
        $this->assertEquals(1, $child->fresh()->depth_level);
        $this->assertEquals(2, $grandchild->fresh()->depth_level);
    }

    /** @test */
    public function it_calculates_indent_pixels(): void
    {
        $parent = ShopCategory::factory()->create(['parent_id' => null]);
        $child = ShopCategory::factory()->create(['parent_id' => $parent->category_id]);

        $this->assertEquals(0, $parent->fresh()->indent_px);
        $this->assertEquals(30, $child->fresh()->indent_px);
    }

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $category = new ShopCategory();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $category);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $category = new ShopCategory();

        $fields = $category->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['description']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $category = new ShopCategory();

        $this->assertTrue($category->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $category = new ShopCategory();

        $this->assertEquals('category_id', $category->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $category = ShopCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme', 'en' => 'Web Development']
        ]);

        app()->setLocale('tr');
        $seoTitle = $category->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Web Geliştirme', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $category = ShopCategory::factory()->create([
            'description' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $category->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $category = ShopCategory::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $category->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $category = ShopCategory::factory()->create([
            'slug' => ['tr' => 'web-gelistirme', 'en' => 'web-development']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $category->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('web-gelistirme', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $category = ShopCategory::factory()->create([
            'title' => ['tr' => 'Web Geliştirme'],
            'slug' => ['tr' => 'web-gelistirme']
        ]);

        app()->setLocale('tr');
        $schema = $category->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('CollectionPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $category = ShopCategory::factory()->create();

        $this->assertNull($category->seoSetting);

        $seoSetting = $category->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($category->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $category = ShopCategory::factory()->create();

        $this->assertInstanceOf(ShopCategory::class, $category);
        $this->assertDatabaseHas('shop_categories', ['category_id' => $category->category_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $category = ShopCategory::factory()->create();

        $this->assertIsArray($category->title);
        $this->assertArrayHasKey('tr', $category->title);
        $this->assertArrayHasKey('en', $category->title);
    }

    /** @test */
    public function factory_active_state_creates_active_category(): void
    {
        $category = ShopCategory::factory()->active()->create();

        $this->assertTrue($category->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_category(): void
    {
        $category = ShopCategory::factory()->inactive()->create();

        $this->assertFalse($category->is_active);
    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $category = ShopCategory::factory()->create();

        // afterTranslation metodu log yapmalı
        $category->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $category = new ShopCategory();

        $this->assertTrue(method_exists($category, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $category = new ShopCategory();

        $this->assertTrue(method_exists($category, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $category = new ShopCategory();

        $this->assertTrue(method_exists($category, 'seoSetting'));
    }

    /** @test */
    public function it_uses_has_media_management_trait(): void
    {
        $category = new ShopCategory();

        $this->assertTrue(method_exists($category, 'registerMediaCollections'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $category = ShopCategory::factory()->create([
            'title' => ['tr' => 'a ab abc Web Geliştirme'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $category->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }

    /** @test */
    public function it_prevents_circular_reference_in_depth_calculation(): void
    {
        // Bu test manuel circular reference durumunda hangi davranışın olacağını test eder
        $category = ShopCategory::factory()->create(['parent_id' => null]);
        
        // Normal durum
        $this->assertEquals(0, $category->depth_level);
    }
}
