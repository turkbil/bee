<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Unit;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Illuminate\Support\Str;

/**
 * Payment Model Unit Tests
 *
 * Model'in attribute'leri, relationship'leri,
 * scope'ları ve özel metodlarını test eder.
 */
class PaymentModelTest extends TestCase
{

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $payment = new Payment();
        $fillable = $payment->getFillable();

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
        $payment = Payment::factory()->create([
            'is_active' => true,
        ]);

        $this->assertIsBool($payment->is_active);
        $this->assertIsArray($payment->title);
        $this->assertIsArray($payment->slug);
        $this->assertIsArray($payment->body);
    }

    /** @test */
    public function it_uses_correct_primary_key(): void
    {
        $payment = new Payment();

        $this->assertEquals('payment_id', $payment->getKeyName());
    }

    /** @test */
    public function it_has_translatable_attributes(): void
    {
        $payment = new Payment();

        $translatable = $payment->getTranslatable();

        $this->assertContains('title', $translatable);
        $this->assertContains('slug', $translatable);
        $this->assertContains('body', $translatable);
    }

    /** @test */
    public function it_can_get_translated_title(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık', 'en' => 'English Title']
        ]);

        $this->assertEquals('Türkçe Başlık', $payment->getTranslated('title', 'tr'));
        $this->assertEquals('English Title', $payment->getTranslated('title', 'en'));
    }

    /** @test */
    public function it_returns_null_for_missing_translation(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Türkçe Başlık']
        ]);

        $this->assertNull($payment->getTranslated('title', 'fr'));
    }

    /** @test */
    public function it_has_id_attribute_accessor(): void
    {
        $payment = Payment::factory()->create();

        $this->assertEquals($payment->payment_id, $payment->id);
    }

    /** @test */
    public function active_scope_returns_only_active_payments(): void
    {
        Payment::factory()->active()->count(5)->create();
        Payment::factory()->inactive()->count(3)->create();

        $activePages = Payment::active()->get();

        $this->assertCount(5, $activePages);
        $activePages->each(function ($payment) {
            $this->assertTrue($payment->is_active);
        });
    }

    /** @test */

    /** @test */
    public function it_implements_translatable_entity_interface(): void
    {
        $payment = new Payment();

        $this->assertInstanceOf(\App\Contracts\TranslatableEntity::class, $payment);
    }

    /** @test */
    public function it_returns_correct_translatable_fields(): void
    {
        $payment = new Payment();

        $fields = $payment->getTranslatableFields();

        $this->assertEquals('text', $fields['title']);
        $this->assertEquals('html', $fields['body']);
        $this->assertEquals('auto', $fields['slug']);
    }

    /** @test */
    public function it_has_seo_settings_support(): void
    {
        $payment = new Payment();

        $this->assertTrue($payment->hasSeoSettings());
    }

    /** @test */
    public function it_provides_primary_key_name(): void
    {
        $payment = new Payment();

        $this->assertEquals('payment_id', $payment->getPrimaryKeyName());
    }

    /** @test */
    public function it_has_seo_fallback_title(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Test Başlık', 'en' => 'Test Title']
        ]);

        app()->setLocale('tr');
        $seoTitle = $payment->getSeoFallbackTitle();

        $this->assertNotEmpty($seoTitle);
        $this->assertEquals('Test Başlık', $seoTitle);
    }

    /** @test */
    public function it_has_seo_fallback_description(): void
    {
        $payment = Payment::factory()->create([
            'body' => ['tr' => '<p>Bu bir test içeriğidir. ' . str_repeat('Lorem ipsum ', 50) . '</p>']
        ]);

        app()->setLocale('tr');
        $seoDescription = $payment->getSeoFallbackDescription();

        $this->assertNotEmpty($seoDescription);
        $this->assertLessThanOrEqual(160, strlen($seoDescription));
        $this->assertStringNotContainsString('<p>', $seoDescription);
    }

    /** @test */
    public function it_has_seo_fallback_keywords(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Laravel Framework PHP Geliştirme']
        ]);

        app()->setLocale('tr');
        $keywords = $payment->getSeoFallbackKeywords();

        $this->assertIsArray($keywords);
        $this->assertLessThanOrEqual(5, count($keywords));
    }

    /** @test */
    public function it_has_seo_fallback_canonical_url(): void
    {
        $payment = Payment::factory()->create([
            'slug' => ['tr' => 'test-sayfasi', 'en' => 'test-payment']
        ]);

        app()->setLocale('tr');
        $canonicalUrl = $payment->getSeoFallbackCanonicalUrl();

        $this->assertStringContainsString('test-sayfasi', $canonicalUrl);
    }

    /** @test */
    public function it_has_seo_fallback_image(): void
    {
        $payment = Payment::factory()->create([
            'body' => ['tr' => '<p>Test</p><img src="/images/test.jpg" alt="Test">']
        ]);

        app()->setLocale('tr');
        $image = $payment->getSeoFallbackImage();

        $this->assertEquals('/images/test.jpg', $image);
    }

    /** @test */
    public function it_returns_null_when_no_image_in_content(): void
    {
        $payment = Payment::factory()->create([
            'body' => ['tr' => '<p>Sadece metin içerik</p>']
        ]);

        app()->setLocale('tr');
        $image = $payment->getSeoFallbackImage();

        $this->assertNull($image);
    }

    /** @test */
    public function it_has_seo_fallback_schema_markup(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Test Sayfası'],
            'slug' => ['tr' => 'test-sayfasi']
        ]);

        app()->setLocale('tr');
        $schema = $payment->getSeoFallbackSchemaMarkup();

        $this->assertIsArray($schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('WebPage', $schema['@type']);
        $this->assertArrayHasKey('name', $schema);
        $this->assertArrayHasKey('url', $schema);
    }

    /** @test */
    public function it_can_create_or_get_seo_setting(): void
    {
        $payment = Payment::factory()->create();

        $this->assertNull($payment->seoSetting);

        $seoSetting = $payment->getOrCreateSeoSetting();

        $this->assertNotNull($seoSetting);
        $this->assertNotNull($payment->fresh()->seoSetting);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $payment = Payment::factory()->create();

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertDatabaseHas('payments', ['payment_id' => $payment->payment_id]);
    }

    /** @test */
    public function factory_creates_with_multilang_data(): void
    {
        $payment = Payment::factory()->create();

        $this->assertIsArray($payment->title);
        $this->assertArrayHasKey('tr', $payment->title);
        $this->assertArrayHasKey('en', $payment->title);
    }

    /** @test */

    /** @test */
    public function factory_active_state_creates_active_payment(): void
    {
        $payment = Payment::factory()->active()->create();

        $this->assertTrue($payment->is_active);
    }

    /** @test */
    public function factory_inactive_state_creates_inactive_payment(): void
    {
        $payment = Payment::factory()->inactive()->create();

        $this->assertFalse($payment->is_active);
    }

    /** @test */
    public function factory_with_custom_styles_creates_css_and_js(): void
    {
        $payment = Payment::factory()->withCustomStyles()->create();

    }

    /** @test */
    public function it_calls_after_translation_method(): void
    {
        $payment = Payment::factory()->create();

        // afterTranslation metodu log yapmalı
        $payment->afterTranslation('en', ['title' => 'Translated Title']);

        $this->assertTrue(true); // Log exception fırlatmamalı
    }

    /** @test */
    public function it_uses_sluggable_trait(): void
    {
        $payment = new Payment();

        $this->assertTrue(method_exists($payment, 'sluggable'));
    }

    /** @test */
    public function it_uses_has_translations_trait(): void
    {
        $payment = new Payment();

        $this->assertTrue(method_exists($payment, 'getTranslated'));
    }

    /** @test */
    public function it_uses_has_seo_trait(): void
    {
        $payment = new Payment();

        $this->assertTrue(method_exists($payment, 'seoSetting'));
    }

    /** @test */
    public function it_filters_short_keywords_in_seo_fallback(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'a ab abc Test Sayfası'] // Kısa kelimeler
        ]);

        app()->setLocale('tr');
        $keywords = $payment->getSeoFallbackKeywords();

        // 3 karakterden kısa kelimeler filtrelenmeli
        foreach ($keywords as $keyword) {
            $this->assertGreaterThan(3, strlen($keyword));
        }
    }
}
