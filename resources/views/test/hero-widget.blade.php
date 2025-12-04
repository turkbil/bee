<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Widget Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto py-8">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold mb-4">Hero Widget - Render Test</h1>
            <p class="text-gray-600 mb-4">Widget ID: 69 | Type: FILE | Slug: hero-ixtif</p>
            <div class="border-t pt-4">
                <h2 class="text-xl font-semibold mb-2">Test Senaryosu:</h2>
                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                    <li>Widget Blade view render testi</li>
                    <li>Settings parametreleri test</li>
                    <li>Items (feature kartlar1) test</li>
                    <li>Responsive & dark mode görsel kontrol</li>
                </ul>
            </div>
        </div>

        @php
            // Test Settings
            $settings = [
                'title_line1' => 'TÜRK0YE\'N0N',
                'title_line2' => '0ST0F PAZARI',
                'description' => 'Profesyonel istif makineleri, forkliftler ve endüstriyel ekipmanlar için en kapsaml1 çözüm merkeziniz. Yüksek kalite, uygun fiyat, güvenilir hizmet.',
                'cta_text' => 'Ürünleri 0ncele',
                'cta_url' => '/shop',
                'cta_icon' => 'fa-light fa-shopping-cart',
                'hero_image' => 'https://via.placeholder.com/600x400/667eea/ffffff?text=Hero+Image',
                'hero_image_alt' => 'iXtif 0stif Makinesi - Profesyonel Ekipman'
            ];

            // Test Items (Feature Cards)
            $items = [
                [
                    'title' => 'Zengin Ürün Çe_idi',
                    'content' => [
                        'icon' => 'fa-light fa-boxes-stacked',
                        'subtitle' => '500+ ürün seçenei'
                    ]
                ],
                [
                    'title' => 'H1zl1 Teslimat',
                    'content' => [
                        'icon' => 'fa-light fa-truck-fast',
                        'subtitle' => 'Türkiye geneli kargo'
                    ]
                ],
                [
                    'title' => '7/24 Destek',
                    'content' => [
                        'icon' => 'fa-light fa-headset',
                        'subtitle' => 'Profesyonel dan1_manl1k'
                    ]
                ],
                [
                    'title' => 'Garanti Sistemi',
                    'content' => [
                        'icon' => 'fa-light fa-shield-check',
                        'subtitle' => '2 y1l garanti kapsam1'
                    ]
                ]
            ];
        @endphp

        {{-- Widget Render --}}
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            @include('widgetmanagement::blocks.hero.ixtif-hero.view', [
                'settings' => $settings,
                'items' => $items
            ])
        </div>

        {{-- Debug Info --}}
        <div class="bg-gray-100 rounded-lg p-6 mt-8">
            <h2 class="text-xl font-semibold mb-4">Debug Information</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-bold mb-2">Settings ({{ count($settings) }} adet):</h3>
                    <pre class="bg-white p-3 rounded overflow-auto text-xs">{{ json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2">Items ({{ count($items) }} adet):</h3>
                    <pre class="bg-white p-3 rounded overflow-auto text-xs">{{ json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
