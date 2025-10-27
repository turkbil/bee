<?php
/**
 * 🎯 SEO GROUP LAYOUT GÜNCELLE - YENİ PLATFORM ALANLARI EKLE
 *
 * Kullanım:
 * php update-seo-group-layout.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 SEO Group Layout Güncelleniyor...\n\n";

// Mevcut layout'u al
$group = DB::table('settings_groups')->where('id', 8)->first();

if (!$group) {
    echo "❌ SEO group bulunamadı!\n";
    exit(1);
}

$layout = json_decode($group->layout, true);

if (!$layout) {
    echo "❌ Layout JSON parse edilemedi!\n";
    exit(1);
}

echo "✅ Mevcut layout yüklendi\n";
echo "📊 Mevcut element sayısı: " . count($layout['elements']) . "\n\n";

// Yeni "Dijital Pazarlama Platformları" bölümü ekle
$newSection = [
    // Başlık
    [
        'type' => 'heading',
        'properties' => [
            'content' => 'Dijital Pazarlama Platformları',
            'size' => 'h3',
            'width' => 12,
            'align' => 'left'
        ]
    ],

    // Paragraf açıklama
    [
        'type' => 'paragraph',
        'properties' => [
            'content' => '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i><strong>Google Tag Manager (GTM)</strong> kullanarak tüm platformları tek merkezden yönetebilirsiniz. Önce GTM Container ID\'yi girin, sonra diğer platform ID\'lerini GTM panelinden yönetin.</div>',
            'width' => 12
        ]
    ],

    // GTM (Tek satır, önemli)
    [
        'type' => 'row',
        'properties' => [
            'columns' => [
                ['index' => 1, 'width' => 12]
            ]
        ],
        'columns' => [
            [
                'width' => 12,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => '🎯 Google Tag Manager Container ID',
                            'name' => 'seo_google_tag_manager_id',
                            'placeholder' => 'GTM-XXXXXXX',
                            'help_text' => 'Tüm tracking kodlarını GTM üzerinden yönetin. Zorunlu değil ama ÖNERİLİR!',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ]
        ]
    ],

    // Google Ads Conversion (3 alan)
    [
        'type' => 'row',
        'properties' => [
            'columns' => [
                ['index' => 1, 'width' => 6],
                ['index' => 2, 'width' => 6]
            ]
        ],
        'columns' => [
            [
                'width' => 6,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'Google Ads Conversion ID',
                            'name' => 'seo_google_ads_conversion_id',
                            'placeholder' => 'AW-XXXXXXXXXX',
                            'help_text' => 'Google Ads dönüşüm takibi için Conversion ID',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ],
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'Form Gönderme Conversion Label',
                            'name' => 'seo_google_ads_form_conversion_label',
                            'placeholder' => 'AbC-123xyz',
                            'help_text' => 'Form gönderme conversion label',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ],
            [
                'width' => 6,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'Telefon Tıklama Conversion Label',
                            'name' => 'seo_google_ads_phone_conversion_label',
                            'placeholder' => 'XyZ-456abc',
                            'help_text' => 'Telefon tıklama conversion label',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ]
        ]
    ],

    // Facebook + LinkedIn + Clarity
    [
        'type' => 'row',
        'properties' => [
            'columns' => [
                ['index' => 1, 'width' => 4],
                ['index' => 2, 'width' => 4],
                ['index' => 3, 'width' => 4]
            ]
        ],
        'columns' => [
            [
                'width' => 4,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'Facebook Pixel ID',
                            'name' => 'seo_facebook_pixel_id',
                            'placeholder' => '123456789012345',
                            'help_text' => 'Facebook/Instagram reklamları için Pixel ID (15 haneli)',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ],
            [
                'width' => 4,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'LinkedIn Partner ID',
                            'name' => 'seo_linkedin_partner_id',
                            'placeholder' => '123456',
                            'help_text' => 'B2B endüstriyel ürünler için LinkedIn Insight Tag',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ],
            [
                'width' => 4,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'Microsoft Clarity ID',
                            'name' => 'seo_microsoft_clarity_id',
                            'placeholder' => 'abcd1234',
                            'help_text' => 'ÜCRETSIZ heatmap ve session replay (ÖNERİLİR!)',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ]
        ]
    ],

    // Opsiyonel: Twitter + TikTok
    [
        'type' => 'row',
        'properties' => [
            'columns' => [
                ['index' => 1, 'width' => 6],
                ['index' => 2, 'width' => 6]
            ]
        ],
        'columns' => [
            [
                'width' => 6,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'Twitter (X) Pixel ID (Opsiyonel)',
                            'name' => 'seo_twitter_pixel_id',
                            'placeholder' => 'o1234',
                            'help_text' => 'Twitter reklamları için pixel ID',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ],
            [
                'width' => 6,
                'elements' => [
                    [
                        'type' => 'text',
                        'properties' => [
                            'label' => 'TikTok Pixel ID (Opsiyonel)',
                            'name' => 'seo_tiktok_pixel_id',
                            'placeholder' => 'C1234567890ABCDEF',
                            'help_text' => 'TikTok reklamları için pixel ID',
                            'width' => 12,
                            'required' => false,
                            'default_value' => null,
                            'is_active' => true,
                            'is_system' => false
                        ]
                    ]
                ]
            ]
        ]
    ],
];

// Yeni bölümü layout'a ekle
foreach ($newSection as $element) {
    $layout['elements'][] = $element;
}

echo "✅ Yeni platform alanları layout'a eklendi\n";
echo "📊 Yeni element sayısı: " . count($layout['elements']) . "\n\n";

// Layout'u güncelle
$updatedLayout = json_encode($layout, JSON_UNESCAPED_UNICODE);

DB::table('settings_groups')
    ->where('id', 8)
    ->update([
        'layout' => $updatedLayout,
        'updated_at' => now()
    ]);

echo "════════════════════════════════════════════════\n";
echo "✅ LAYOUT BAŞARIYLA GÜNCELLENDİ!\n";
echo "════════════════════════════════════════════════\n\n";

echo "🎯 Artık admin panelde görebilirsiniz:\n";
echo "   https://ixtif.com/admin/settingmanagement/values/8\n\n";

echo "📋 Eklenen Alanlar:\n";
echo "   1. Google Tag Manager Container ID\n";
echo "   2. Google Ads Conversion ID\n";
echo "   3. Google Ads Form Conversion Label\n";
echo "   4. Google Ads Phone Conversion Label\n";
echo "   5. Facebook Pixel ID\n";
echo "   6. LinkedIn Partner ID\n";
echo "   7. Microsoft Clarity ID\n";
echo "   8. Twitter (X) Pixel ID\n";
echo "   9. TikTok Pixel ID\n\n";

echo "✨ İşlem tamamlandı!\n";
