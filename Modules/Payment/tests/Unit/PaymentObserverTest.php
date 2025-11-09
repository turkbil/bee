<?php

declare(strict_types=1);

namespace Modules\Payment\Tests\Unit;

use Modules\Payment\Tests\TestCase;
use Modules\Payment\App\Models\Payment;
use Illuminate\Support\Facades\{Cache, Log};
use Illuminate\Support\Str;

/**
 * PaymentObserver Unit Tests
 *
 * Model lifecycle event'lerini ve observer'ın
 * otomatik işlemlerini test eder.
 */
class PaymentObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    /** @test */
    public function it_generates_slug_automatically_on_create(): void
    {
        $payment = Payment::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Payment'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertNotEmpty($payment->getTranslated('slug', 'tr'));
        $this->assertNotEmpty($payment->getTranslated('slug', 'en'));
        $this->assertEquals('test-sayfasi', $payment->getTranslated('slug', 'tr'));
        $this->assertEquals('test-payment', $payment->getTranslated('slug', 'en'));
    }

    /** @test */
    public function it_does_not_override_provided_slug(): void
    {
        $payment = Payment::create([
            'title' => ['tr' => 'Test Sayfası', 'en' => 'Test Payment'],
            'slug' => ['tr' => 'custom-slug', 'en' => 'custom-slug'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $this->assertEquals('custom-slug', $payment->getTranslated('slug', 'tr'));
        $this->assertEquals('custom-slug', $payment->getTranslated('slug', 'en'));
    }

    /** @test */

    /** @test */

    /** @test */
    public function it_generates_unique_slug_on_conflict(): void
    {
        Payment::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Payment'],
            'slug' => ['tr' => 'test-sayfa', 'en' => 'test-payment'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);

        $payment2 = Payment::create([
            'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Payment'],
            'body' => ['tr' => '<p>Test 2</p>', 'en' => '<p>Test 2</p>'],
            'is_active' => true,
        ]);

        // İkinci sayfa unique slug almalı
        $slug = $payment2->getTranslated('slug', 'tr');
        $this->assertNotEquals('test-sayfa', $slug);
        $this->assertStringStartsWith('test-sayfa', $slug);
    }

    /** @test */
    public function it_validates_title_min_length(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık minimum');

        Payment::create([
            'title' => ['tr' => 'ab', 'en' => 'ab'], // 2 karakter (min 3)
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_title_max_length(): void
    {
        $longTitle = Str::random(200);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Başlık maksimum');

        Payment::create([
            'title' => ['tr' => $longTitle, 'en' => $longTitle],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_css_size(): void
    {
        $largeCss = str_repeat('a', 60000); // 60KB (max 50KB)

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CSS içeriği maksimum boyutu');

        Payment::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_js_size(): void
    {
        $largeJs = str_repeat('a', 60000); // 60KB (max 50KB)

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('JavaScript içeriği maksimum boyutu');

        Payment::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
            'is_active' => true,
        ]);
    }

    /** @test */

    /** @test */
    public function it_allows_regular_payment_deletion(): void
    {
        $payment = Payment::factory()->create();

        $payment->delete();

        $this->assertDatabaseMissing('payments', ['payment_id' => $payment->payment_id]);
    }

    /** @test */
    public function it_clears_cache_after_create(): void
    {
        Cache::shouldReceive('forget')
            ->with('payments_list')
            ->once();

        Cache::shouldReceive('forget')
            ->with('payments_menu_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->with('payments_sitemap_cache')
            ->once();

        Cache::shouldReceive('forget')
            ->once();

        Payment::factory()->create();
    }

    /** @test */
    public function it_clears_cache_after_update(): void
    {
        $payment = Payment::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $payment->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_clears_cache_after_delete(): void
    {
        $payment = Payment::factory()->create();

        Cache::shouldReceive('forget')->atLeast()->once();

        $payment->delete();
    }

    /** @test */
    public function it_deletes_seo_setting_on_delete(): void
    {
        $payment = Payment::factory()->create();
        $payment->getOrCreateSeoSetting(); // SEO setting oluştur

        $this->assertDatabaseHas('seo_settings', [
            'seo_settingable_type' => get_class($payment),
            'seo_settingable_id' => $payment->payment_id
        ]);

        $payment->delete();

        $this->assertDatabaseMissing('seo_settings', [
            'seo_settingable_type' => get_class($payment),
            'seo_settingable_id' => $payment->payment_id
        ]);
    }

    /** @test */
    public function it_logs_payment_creation(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Payment creating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Payment created successfully', \Mockery::any());

        Payment::factory()->create();
    }

    /** @test */
    public function it_logs_payment_update(): void
    {
        $payment = Payment::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Payment updating', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Payment updated successfully', \Mockery::any());

        $payment->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_logs_payment_deletion(): void
    {
        $payment = Payment::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Payment deleting', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Payment deleted successfully', \Mockery::any());

        $payment->delete();
    }

    /** @test */

    /** @test */
    public function it_logs_force_delete_attempt(): void
    {
        $payment = Payment::factory()->create();

        Log::shouldReceive('warning')
            ->once()
            ->with('Payment force deleting', \Mockery::any());

        Log::shouldReceive('warning')
            ->once()
            ->with('Payment force deleted', \Mockery::any());

        $payment->forceDelete();
    }

    /** @test */
    public function it_clears_universal_seo_cache_on_save(): void
    {
        $payment = Payment::factory()->create();

        Cache::shouldReceive('forget')
            ->with("universal_seo_payment_{$payment->payment_id}")
            ->once();

        $payment->update(['title' => ['tr' => 'Updated', 'en' => 'Updated']]);
    }

    /** @test */
    public function it_tracks_changed_fields_on_update(): void
    {
        $payment = Payment::factory()->create([
            'title' => ['tr' => 'Eski Başlık', 'en' => 'Old Title'],
            'is_active' => true
        ]);

        Log::shouldReceive('info')
            ->with('Payment updating', \Mockery::on(function ($data) {
                return isset($data['changed_fields']) && in_array('title', $data['changed_fields']);
            }));

        $payment->update(['title' => ['tr' => 'Yeni Başlık', 'en' => 'New Title']]);
    }

    /** @test */
    public function it_applies_default_values_from_config(): void
    {
        config(['payment.defaults.is_active' => false]);

        $payment = Payment::create([
            'title' => ['tr' => 'Test', 'en' => 'Test'],
            'body' => ['tr' => '<p>Test</p>', 'en' => '<p>Test</p>'],
        ]);

        // Default'dan false gelmeli (is_active belirtilmedi)
        $this->assertFalse($payment->is_active);
    }
}
