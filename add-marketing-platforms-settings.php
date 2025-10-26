<?php
/**
 * 🎯 DİJİTAL PAZARLAMA PLATFORMLARI - SETTINGS EKLE
 *
 * Kullanım:
 * php artisan tinker
 * require 'add-marketing-platforms-settings.php';
 *
 * Veya:
 * php add-marketing-platforms-settings.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 Dijital Pazarlama Platformları - Settings Ekleniyor...\n\n";

// Mevcut son sıra numarasını bul
$lastSortOrder = DB::table('settings')
    ->where('group_id', 8)
    ->max('sort_order') ?? 10;

$settings = [
    // ========================================
    // GOOGLE TAG MANAGER
    // ========================================
    [
        'group_id' => 8,
        'label' => 'Google Tag Manager Container ID',
        'key' => 'seo_google_tag_manager_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'GTM Container ID (örn: GTM-XXXXXXX). Tüm tracking kodlarını GTM üzerinden yönetin.',
        'created_at' => now(),
        'updated_at' => now(),
    ],

    // ========================================
    // GOOGLE ADS - CONVERSION TRACKING
    // ========================================
    [
        'group_id' => 8,
        'label' => 'Google Ads Conversion ID',
        'key' => 'seo_google_ads_conversion_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'Google Ads Conversion Tracking ID (örn: AW-XXXXXXXXXX)',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'group_id' => 8,
        'label' => 'Google Ads - Form Gönderme Conversion Label',
        'key' => 'seo_google_ads_form_conversion_label',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'Form gönderme conversion label (örn: AbC-123xyz)',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'group_id' => 8,
        'label' => 'Google Ads - Telefon Tıklama Conversion Label',
        'key' => 'seo_google_ads_phone_conversion_label',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'Telefon tıklama conversion label',
        'created_at' => now(),
        'updated_at' => now(),
    ],

    // ========================================
    // FACEBOOK PIXEL (META)
    // ========================================
    [
        'group_id' => 8,
        'label' => 'Facebook Pixel ID',
        'key' => 'seo_facebook_pixel_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'Facebook (Meta) Pixel ID (örn: 123456789012345). Facebook/Instagram reklamları için gerekli.',
        'created_at' => now(),
        'updated_at' => now(),
    ],

    // ========================================
    // LINKEDIN INSIGHT TAG
    // ========================================
    [
        'group_id' => 8,
        'label' => 'LinkedIn Partner ID',
        'key' => 'seo_linkedin_partner_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'LinkedIn Insight Tag Partner ID (örn: 123456). B2B endüstriyel ürünler için önemli!',
        'created_at' => now(),
        'updated_at' => now(),
    ],

    // ========================================
    // MICROSOFT CLARITY
    // ========================================
    [
        'group_id' => 8,
        'label' => 'Microsoft Clarity Project ID',
        'key' => 'seo_microsoft_clarity_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'Microsoft Clarity Project ID (örn: abcd1234). ÜCRETSIZ heatmap ve session replay!',
        'created_at' => now(),
        'updated_at' => now(),
    ],

    // ========================================
    // TWITTER (X) PIXEL
    // ========================================
    [
        'group_id' => 8,
        'label' => 'Twitter (X) Pixel ID',
        'key' => 'seo_twitter_pixel_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'Twitter (X) Pixel ID. Twitter reklamları için (opsiyonel).',
        'created_at' => now(),
        'updated_at' => now(),
    ],

    // ========================================
    // TIKTOK PIXEL
    // ========================================
    [
        'group_id' => 8,
        'label' => 'TikTok Pixel ID',
        'key' => 'seo_tiktok_pixel_id',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => ++$lastSortOrder,
        'is_active' => 1,
        'is_system' => 0,
        'is_required' => 0,
        'help_text' => 'TikTok Pixel ID. TikTok reklamları için (opsiyonel).',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

// Mevcut kayıtları kontrol et, sadece yoksa ekle
$inserted = 0;
$skipped = 0;

foreach ($settings as $setting) {
    $exists = DB::table('settings')
        ->where('key', $setting['key'])
        ->exists();

    if (!$exists) {
        DB::table('settings')->insert($setting);
        echo "✅ Eklendi: {$setting['label']} ({$setting['key']})\n";
        $inserted++;
    } else {
        echo "⏭️  Zaten var: {$setting['label']} ({$setting['key']})\n";
        $skipped++;
    }
}

echo "\n";
echo "════════════════════════════════════════════════\n";
echo "📊 ÖZET\n";
echo "════════════════════════════════════════════════\n";
echo "✅ Eklenen: $inserted\n";
echo "⏭️  Atlanan (Zaten var): $skipped\n";
echo "📦 Toplam: " . ($inserted + $skipped) . "\n";
echo "════════════════════════════════════════════════\n\n";

echo "🎯 Artık admin panelinden ayarlayabilirsiniz:\n";
echo "   https://ixtif.com/admin/settingmanagement/values/8\n\n";

echo "✨ İşlem tamamlandı!\n";
