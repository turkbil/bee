<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F1 - 1.5 Ton 24V AGM Transpalet (Endüstriyel Kullanım)
 * MASTER + CHILD VARIANTS
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F1/F1-EN-Brochure-2.pdf
 * Marka: İXTİF (brand_id = 1)
 * Kategori: TRANSPALETLER (category_id = 165)
 */
class F1_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F1 Elektrikli Transpalet (Master + Variants) ekleniyor...');

        // Dinamik olarak Transpalet kategorisi ID'sini bul
        $categoryId = DB::table('shop_categories')->where('title->tr', 'Transpalet')->value('category_id');
        if (!$categoryId) {
            $this->command->error('❌ Transpalet kategorisi bulunamadı!');
            return;
        }

        // Brand ID'sini bul veya oluştur
        $brandId = DB::table('shop_brands')->where('title->tr', 'İXTİF')->value('brand_id');
        if (!$brandId) {
            $brandId = DB::table('shop_brands')->insertGetId([
                'title' => json_encode(['tr' => 'İXTİF', 'en' => 'İXTİF']),
                'slug' => json_encode(['tr' => 'ixtif', 'en' => 'ixtif']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Mevcut kayıtları temizle
        $existingProducts = DB::table('shop_products')
            ->where('sku', 'LIKE', 'F1-%')
            ->pluck('product_id');

        if ($existingProducts->isNotEmpty()) {
            DB::table('shop_products')->whereIn('product_id', $existingProducts)->delete();
            $this->command->info('🧹 Eski F1 kayıtları temizlendi (' . $existingProducts->count() . ' ürün)');
        }

        // MASTER PRODUCT
        $productId = DB::table('shop_products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => 'F1-MASTER',
            'model_number' => 'F1',
            'parent_product_id' => null,
            'is_master_product' => true,
            'title' => json_encode(['tr' => 'F1 Elektrikli Transpalet 1.5T - Endüstriyel AGM Bataryalı'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f1-elektrikli-transpalet-1-5t'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '1.5 ton kapasiteli F1 transpalet, 24V/65Ah AGM batarya ve entegre şarj cihazı ile endüstriyel kullanım için optimize edilmiştir. 5-6 saat kesintisiz çalışma, platform tasarımı ile maliyet avantajı.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <h2>Endüstriyel Kullanım İçin Dayanıklı Çözüm</h2>
    <p>F1 Elektrikli Transpalet, endüstriyel kullanım için tasarlanmış 1.5 ton kapasiteli güçlü transpalet. 24V/65Ah AGM batarya ve entegre 24V/10A şarj cihazı ile 5-6 saat kesintisiz çalışma sağlar.</p>
    <p>EPT20-15ET'nin kanıtlanmış pratik özellikleriyle F Serisi platform tasarımının maliyet avantajını birleştiren F1, depo operasyonlarında güvenilir performans sunar.</p>
</section>
<section class="marketing-body">
    <h3>AGM Batarya Teknolojisi</h3>
    <p>24V/65Ah AGM batarya sistemi, uzun ömür ve minimum bakım gereksinimiyle operasyonel verimliliği artırır. Entegre şarj cihazı ile pratik kullanım.</p>
    <h3>Platform Tabanlı Tasarım</h3>
    <p>F Serisi platform, basitleştirilmiş konfigürasyon ve %50'ye varan nakliye tasarrufu (132 ünite/40' konteyner) sağlar.</p>
    <h3>İXTİF Hizmet Garantisi</h3>
    <ul>
        <li><strong>Yeni ve İkinci El Satış:</strong> İhtiyacınıza uygun esnek seçenekler</li>
        <li><strong>Kiralama Hizmeti:</strong> Kısa ve uzun dönem imkanı</li>
        <li><strong>Yedek Parça Desteği:</strong> Orijinal parça stok garantisi</li>
        <li><strong>Teknik Servis:</strong> 0216 755 3 555 | info@ixtif.com</li>
    </ul>
</section>
HTML], JSON_UNESCAPED_UNICODE),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'currency' => 'TRY',
            'stock_tracking' => 1,
            'current_stock' => 0,
            'lead_time_days' => 30,
            'weight' => 145,
            'dimensions' => json_encode(['length' => 1604, 'width' => 695, 'height' => 105, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode([
                'capacity' => ['load_capacity' => ['value' => 1500, 'unit' => 'kg'], 'service_weight' => ['value' => 145, 'unit' => 'kg']],
                'dimensions' => [
                    'overall_length' => ['value' => 1604, 'unit' => 'mm'],
                    'fork_dimensions' => ['thickness' => 55, 'width' => 150, 'length' => 1150, 'unit' => 'mm'],
                    'turning_radius' => ['value' => 1426, 'unit' => 'mm']
                ],
                'electrical' => [
                    'battery' => ['type' => 'AGM', 'voltage' => 24, 'capacity' => 65, 'unit' => 'V/Ah', 'configuration' => '2×12V/65Ah'],
                    'charger' => ['type' => 'Integrated', 'power' => '24V/10A'],
                    'working_time' => '5-6 hours'
                ],
                'performance' => ['travel_speed_laden' => ['value' => 4, 'unit' => 'km/h'], 'travel_speed_unladen' => ['value' => 4.5, 'unit' => 'km/h']]
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode(['tr' => ['list' => [
                '24V/65Ah AGM batarya - uzun ömür ve düşük bakım',
                'Entegre 24V/10A şarj cihazı - pratik kullanım',
                '5-6 saat kesintisiz çalışma süresi',
                '145 kg hafif gövde - kolay manevra kabiliyeti',
                'EPT20-15ET kanıtlanmış sürüş ünitesi',
                'Platform tasarımı - %50 nakliye tasarrufu',
                'Polyurethane tekerler - düşük gürültü',
                'CE sertifikalı - Avrupa standartları'
            ]]], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '1.5 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'AGM 24V/65Ah'],
                ['label' => 'Çatal Uzunluğu', 'value' => '1150 mm'],
                ['label' => 'Denge Tekeri', 'value' => 'Opsiyonel']
            ], JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'is_featured' => 0,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ MASTER Product eklendi (ID: {$productId}, SKU: F1-MASTER)");

        // CHILD VARIANTS
        $variants = [
            [
                'sku' => 'F1-STD',
                'title' => 'F1 - Standart Çatal (1150x560 mm)',
                'slug' => 'f1-standart-catal-1150x560',
                'variant_type' => 'standart-catal',
                'short_description' => 'F1 standart çatal boyutu (1150x560 mm) ile genel amaçlı EUR palet taşıma işlemleri için ideal.',
                'weight' => 145,
                'fork_length' => 1150,
                'fork_width' => 560,
                'features' => [
                    'Standart 1150x560 mm çatal - EUR palet uyumlu',
                    'AGM batarya ile 5-6 saat çalışma',
                    '145 kg hafif gövde - kolay manevra',
                    'Entegre şarj cihazı - pratik kullanım'
                ]
            ],
            [
                'sku' => 'F1-WIDE',
                'title' => 'F1 - Geniş Çatal (1150x685 mm)',
                'slug' => 'f1-genis-catal-1150x685',
                'variant_type' => 'genis-catal',
                'short_description' => 'F1 geniş çatal (1150x685 mm) büyük paletlerin güvenli taşınması için tasarlandı.',
                'weight' => 148,
                'fork_length' => 1150,
                'fork_width' => 685,
                'features' => [
                    'Geniş 1150x685 mm çatal - büyük palet uyumlu',
                    'Büyük paletlerde maksimum stabilite',
                    'Denge tekeri opsiyonu - ek güvenlik',
                    'AGM güvenilirliği'
                ]
            ],
            [
                'sku' => 'F1-SHORT',
                'title' => 'F1 - Kısa Çatal (900x560 mm)',
                'slug' => 'f1-kisa-catal-900x560',
                'variant_type' => 'kisa-catal',
                'short_description' => 'F1 kısa çatal (900x560 mm) dar alanlarda ve kısa paletlerde maksimum çeviklik.',
                'weight' => 142,
                'fork_length' => 900,
                'fork_width' => 560,
                'features' => [
                    'Kısa 900 mm çatal - dar alan çözümü',
                    '142 kg ultra hafif - üstün manevra',
                    'Küçük paletler için ideal',
                    'Dar koridorlarda rahat dönüş'
                ]
            ],
            [
                'sku' => 'F1-LONG',
                'title' => 'F1 - Uzun Çatal (1500x560 mm)',
                'slug' => 'f1-uzun-catal-1500x560',
                'variant_type' => 'uzun-catal',
                'short_description' => 'F1 uzun çatal (1500x560 mm) özel boy paletlerin güvenli taşınması için.',
                'weight' => 150,
                'fork_length' => 1500,
                'fork_width' => 560,
                'features' => [
                    'Uzun 1500 mm çatal - özel boy paletler',
                    'Uzun yüklerde dengeli dağılım',
                    'Stabilizasyon tekerleği önerilir',
                    'Endüstriyel dayanıklılık'
                ]
            ]
        ];

        $variantCount = 0;
        foreach ($variants as $variantData) {
            $variantTechnicalSpecs = json_decode(json_encode([
                'capacity' => ['load_capacity' => ['value' => 1500, 'unit' => 'kg'], 'service_weight' => ['value' => $variantData['weight'], 'unit' => 'kg']],
                'dimensions' => [
                    'fork_dimensions' => ['thickness' => 55, 'width' => 150, 'length' => $variantData['fork_length'], 'unit' => 'mm'],
                    'fork_spread' => ['standard' => $variantData['fork_width'], 'unit' => 'mm']
                ],
                'electrical' => [
                    'battery' => ['type' => 'AGM', 'voltage' => 24, 'capacity' => 65, 'unit' => 'V/Ah'],
                    'charger' => ['type' => 'Integrated', 'power' => '24V/10A']
                ]
            ]), true);

            $childId = DB::table('shop_products')->insertGetId([
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'sku' => $variantData['sku'],
                'model_number' => 'F1',
                'title' => json_encode(['tr' => $variantData['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => $variantData['slug']], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $variantData['short_description']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <p>{$variantData['short_description']}</p>
    <p>AGM batarya güvenilirliği ile endüstriyel performans. İXTİF kalite garantisiyle.</p>
</section>
<section class="marketing-body">
    <h3>Varyant Özellikleri</h3>
    <ul>
        <li>Çatal Boyutu: {$variantData['fork_length']} x {$variantData['fork_width']} mm</li>
        <li>Servis Ağırlığı: {$variantData['weight']} kg</li>
        <li>AGM Batarya: 24V/65Ah</li>
    </ul>
    <p><strong>İletişim:</strong> 0216 755 3 555 | info@ixtif.com</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),
                'parent_product_id' => $productId,
                'is_master_product' => false,
                'variant_type' => $variantData['variant_type'],
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => 1,
                'weight' => $variantData['weight'],
                'technical_specs' => json_encode($variantTechnicalSpecs, JSON_UNESCAPED_UNICODE),
                'features' => json_encode(['tr' => ['list' => $variantData['features']]], JSON_UNESCAPED_UNICODE),
                'primary_specs' => json_encode([
                    ['label' => 'Çatal Boyutu', 'value' => "{$variantData['fork_length']} x {$variantData['fork_width']} mm"],
                    ['label' => 'Akü Sistemi', 'value' => 'AGM 24V/65Ah'],
                    ['label' => 'Servis Ağırlığı', 'value' => "{$variantData['weight']} kg"],
                    ['label' => 'Çalışma Süresi', 'value' => '5-6 saat']
                ], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'is_featured' => 0,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $variantCount++;
            $this->command->info("  ➕ Child Product: {$variantData['title']} (ID: {$childId}, SKU: {$variantData['sku']})");
        }

        $this->command->info('🎉 F1 Product-Based Variant Sistemi tamamlandı!');
        $this->command->info('📊 İstatistik: Master: 1 | Variants: ' . $variantCount);
    }
}
