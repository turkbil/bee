<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F4 201 - 2 Ton 48V Li-Ion Transpalet (Master + 5 Variants)
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/02_F4-201-brochure-CE.pdf
 * JSON Extract: /Users/nurullah/Desktop/cms/laravel/readme/shop-system-v2/json-extracts/F4-201-transpalet.json
 *
 * Marka: İXTİF (brand_id = dinamik)
 * Kategori: TRANSPALETLER (category_id = dinamik)
 *
 * YENİ ÖZELLİKLER v2:
 * - accessories: Stabilizasyon tekerleği, ekstra batarya modülleri, hızlı şarj ünitesi (6 adet)
 * - certifications: CE, ISO 9001:2015, DIN EN 16796 sertifikaları (3 adet)
 * - highlighted_features: 4 öne çıkan özellik kartı
 * - 5 child variant: STD, WIDE, SHORT, LONG, EXT-BAT
 */
class F4_201_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F4 201 Transpalet (Master + 5 Variants) YENİ FORMAT ekleniyor...');

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
            ->where('sku', 'LIKE', 'F4-201%')
            ->pluck('product_id');

        if ($existingProducts->isNotEmpty()) {
            DB::table('shop_products')->whereIn('product_id', $existingProducts)->delete();
            $this->command->info('🧹 Eski F4 201 kayıtları temizlendi (' . $existingProducts->count() . ' ürün)');
        }

        // ============================================================
        // MASTER PRODUCT - F4 201 Transpalet Serisi
        // ============================================================
        $masterId = DB::table('shop_products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => 'F4-201-MASTER',
            'model_number' => 'F4-201',
            'parent_product_id' => null,
            'is_master_product' => true,
            'title' => json_encode(['tr' => 'F4 201 - 2 Ton 48V Li-Ion Transpalet'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f4-201-2-ton-48v-li-ion-transpalet'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '48V Li-Ion güç platformu ile 2 ton taşıma kapasitesi sunan F4 201, tak-çıkar batarya sistemi ve 140 kg ultra hafif gövdesiyle dar koridor operasyonlarında yeni standartlar belirler.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
<p><strong>F4 201'i depoya soktuğunuz anda müşterileriniz "Bu transpaleti nereden aldınız?" diye soracak.</strong></p>
<p>İXTİF mühendisleri bu modeli yalnızca yük taşımak için değil, <em>deponuzun prestijini parlatmak</em> için tasarladı. 48V Li-Ion güç platformu ile 2 ton yükü adeta havada yürüyor gibi taşıyan F4 201, dar koridorlarda bile vitrinde bir süperstar gibi parlar.</p>
<ul>
<li><strong>Bir vardiyada iki kat iş</strong> – Tak-çıkar batarya sistemi ile şarj molasına son verin, lojistik maliyetleriniz %50'ye kadar düşsün.</li>
<li><strong>Showroom etkisi</strong> – Ultra kompakt 400 mm şasi, dar koridorlarda bile operatörlerinize benzersiz özgürlük sağlar.</li>
<li><strong>140 kg hafiflik şampiyonu</strong> – Segmentindeki en hafif gövde, rampalarda inanılmaz kontrol ve enerji verimliliği demektir.</li>
<li><strong>Stabilizasyon tekerleği opsiyonu</strong> – Bozuk zeminlerde bile devrilme riskini sıfırlayarak yatırımınızı korur.</li>
</ul>
</section>

<section class="marketing-body">
<h3>Depoda Hız, Sahada Prestij: F4 201 ile Dar Koridorlara Hükmedin</h3>
<p>Standart teslimat paketinde 2 adet 24V/20Ah Li-Ion modül bulunur. Her modül çıkarılabilir, dolayısıyla vardiya ortasında boş bataryayı çıkarıp dolu olanı takarak operasyonunuz hiç durmaz. Dilerseniz 4 adede kadar modül ekleyerek uzun vardiya performansını artırabilirsiniz.</p>

<p><strong>48V BLDC motorlu sürüş sistemi</strong> sayesinde F4 201, %8 rampalarda yükle bile zorlanmadan çıkar. 0.9 kW sürüş motoru ve 0.7 kW kaldırma motoru kombinasyonu, elektromanyetik fren ile birleşerek size acil durumlarda bile tam kontrol sağlar.</p>

<p><strong>Poliüretan çift sıra yük tekerleri</strong> ve 210×70 mm sürüş tekerleri, dar koridorlarda bile pürüzsüz hareket ve uzun ömür garanti eder. 1360 mm dönüş yarıçapı sayesinde standart paletlerinizi 2160 mm koridor genişliğinde rahatça döndürebilirsiniz.</p>

<h4>İXTİF Farkı: Yatırımınıza 360° Koruma</h4>
<p>İXTİF'in <strong>ikinci el, kiralık, yedek parça ve teknik servis</strong> ekosistemi ile F4 201 yatırımınız tam koruma altında. Türkiye genelinde mobil teknik servis ekiplerimiz 7/24 sahadır.</p>

<ul>
<li><strong>İkinci El Güvencesi:</strong> Garanti belgeleriyle yenilenmiş F4 201 modelleri mevcut.</li>
<li><strong>Kiralık Filolar:</strong> Kısa ve orta vadeli kiralama seçenekleri, operasyonel esneklik sağlar.</li>
<li><strong>Yedek Parça Stoku:</strong> Orijinal EP parçaları İXTİF depolarında stoktan hemen temin edilebilir.</li>
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
            'weight' => 140,
            'dimensions' => json_encode(['length' => 1550, 'width' => 590, 'height' => 85, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode([
                'capacity' => [
                    'load_capacity' => ['value' => 2000, 'unit' => 'kg'],
                    'load_center_distance' => ['value' => 600, 'unit' => 'mm'],
                    'service_weight' => ['value' => 140, 'unit' => 'kg'],
                    'wheelbase' => ['value' => 1180, 'unit' => 'mm']
                ],
                'dimensions' => [
                    'overall_length' => ['value' => 1550, 'unit' => 'mm'],
                    'length_to_face_of_forks' => ['value' => 400, 'unit' => 'mm'],
                    'overall_width' => ['standard' => 590, 'wide' => 695, 'unit' => 'mm'],
                    'fork_dimensions' => ['thickness' => 50, 'width' => 150, 'length' => 1150, 'unit' => 'mm'],
                    'ground_clearance' => ['value' => 30, 'unit' => 'mm'],
                    'lift_height' => ['value' => 105, 'unit' => 'mm'],
                    'turning_radius' => ['value' => 1360, 'unit' => 'mm']
                ],
                'performance' => [
                    'travel_speed' => ['laden' => 4.5, 'unladen' => 5.0, 'unit' => 'km/h'],
                    'lifting_speed' => ['laden' => 0.016, 'unladen' => 0.020, 'unit' => 'm/s'],
                    'max_gradeability' => ['laden' => 8, 'unladen' => 16, 'unit' => '%'],
                    'service_brake' => 'Elektromanyetik'
                ],
                'electrical' => [
                    'voltage' => ['value' => 48, 'unit' => 'V'],
                    'battery_system' => [
                        'type' => 'Li-Ion',
                        'configuration' => '2x 24V/20Ah çıkarılabilir modül (4 adede kadar genişletilebilir)',
                        'weight' => ['value' => 10, 'unit' => 'kg']
                    ],
                    'charger_options' => [
                        'standard' => '2x 24V-5A harici şarj ünitesi',
                        'optional' => '2x 24V-10A hızlı şarj ünitesi'
                    ],
                    'drive_motor' => ['power' => 0.9, 'unit' => 'kW', 'type' => 'BLDC'],
                    'lift_motor' => ['power' => 0.7, 'unit' => 'kW'],
                    'energy_consumption' => ['value' => 0.18, 'unit' => 'kWh/h']
                ],
                'tyres' => [
                    'type' => 'Poliüretan',
                    'drive_wheel' => '210 × 70 mm Poliüretan',
                    'load_wheel' => '80 × 60 mm Poliüretan (çift sıra)',
                    'castor_wheel' => '74 × 30 mm'
                ],
                'options' => [
                    'fork_lengths_mm' => [900, 1000, 1150, 1220, 1350, 1500],
                    'fork_widths_mm' => [560, 685],
                    'stabilizing_wheels' => 'Opsiyonel (fabrikadan veya retrofit)'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode(['tr' => ['list' => [
                'F4 201 transpalet 48V Li-Ion güç platformu ile 2 ton taşıma kapasitesini dar koridor operasyonlarına taşır',
                'Tak-çıkar 24V/20Ah Li-Ion bataryalarla vardiya ortasında şarj molasına son verin, 4 adede kadar genişletilebilir',
                '140 kg ultra hafif servis ağırlığı ve 400 mm kompakt şasi ile dar koridorlarda benzersiz çeviklik',
                'Stabilizasyon tekerleği opsiyonu ile bozuk zeminlerde bile devrilme riskini sıfırlar',
                '0.9 kW BLDC sürüş motoru ve elektromanyetik fren ile %8 rampalarda tam kontrol',
                'İXTİF ikinci el, kiralık, yedek parça ve 7/24 teknik servis ekosistemi',
                'Poliüretan çift sıra yük tekerleri ile uzun ömür ve düşük bakım',
                '1360 mm dönüş yarıçapı ile 2160 mm koridorda rahat manevra'
            ], 'branding' => [
                'slogan' => 'Depoda hız, sahada prestij: F4 201 ile dar koridorlara hükmedin',
                'motto' => 'İXTİF farkı ile 2 tonluk yükler bile hafifler',
                'technical_summary' => 'F4 201, 48V Li-Ion güç paketi, 0.9 kW BLDC motoru ve 400 mm ultra kompakt şasi ile dar koridorlarda yüksek tork, düşük bakım ve sürekli çalışma sunar'
            ]]], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                [
                    'icon' => 'battery-bolt',
                    'priority' => 1,
                    'title' => ['tr' => '48V Li-Ion Güç'],
                    'description' => ['tr' => 'Tak-çıkar batarya sistemi ile vardiya ortasında değişim, 4 modüle kadar genişletilebilir güç platformu.']
                ],
                [
                    'icon' => 'feather',
                    'priority' => 2,
                    'title' => ['tr' => '140 kg Ultra Hafif'],
                    'description' => ['tr' => 'Segmentinin en hafif gövdesi ile rampalarda üstün kontrol ve enerji verimliliği sağlar.']
                ],
                [
                    'icon' => 'arrows-to-circle',
                    'priority' => 3,
                    'title' => ['tr' => 'Dar Koridor Uzmanı'],
                    'description' => ['tr' => '400 mm kompakt şasi ve 1360 mm dönüş yarıçapı ile 2160 mm koridorda bile rahat manevra.']
                ],
                [
                    'icon' => 'shield-check',
                    'priority' => 4,
                    'title' => ['tr' => 'Stabilizasyon Güvenliği'],
                    'description' => ['tr' => 'Opsiyonel stabilizasyon tekerleği ile bozuk zeminlerde devrilme riskini tamamen ortadan kaldırır.']
                ]
            ], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V'],
                ['label' => 'Çatal Uzunluğu', 'value' => '1150 mm'],
                ['label' => 'Denge Tekeri', 'value' => 'Opsiyonel']
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode(['tr' => [
                'E-ticaret depolarında hızlı sipariş hazırlama ve sevkiyat – dar koridorlarda yüksek verimlilik',
                'Dar koridorlu perakende depolarında gece vardiyası yükleme boşaltma',
                'Soğuk zincir lojistiğinde düşük sıcaklıklarda kesintisiz taşıma',
                'İçecek ve FMCG dağıtım merkezlerinde yoğun palet trafiği yönetimi',
                'Dış saha rampalarda stabilizasyon tekerleği ile güvenli taşıma',
                'Kiralama filolarında Li-Ion platform çözümleri – düşük bakım',
                'KOBİ\'lerde tek operator ile çok vardiya operasyonu',
                'Fabrika içi malzeme taşıma – uzun ömürlü poliüretan tekerlekler'
            ]], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode(['tr' => [
                '48V Li-Ion ile segmentinin en agresif hızlanma ve rampa performansı',
                '140 kg ultra hafif gövde ile dramatik lojistik maliyet düşüşü',
                'Tak-çıkar batarya ile 7/24 operasyonda sıfır bekleme',
                'Stabilizasyon tekerleği ile bozuk zeminlerde sıfır devrilme riski',
                'İXTİF stoktan hızlı teslimat ve yerinde devreye alma',
                '400 mm kompakt şasi ile segmentinin en dar dönüş yarıçapı',
                'Poliüretan çift sıra tekerler ile 5 yıla kadar değişim gerekmez'
            ]], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode(['tr' => [
                'E-ticaret ve fulfillment merkezleri',
                'Perakende zincir depoları',
                'Soğuk zincir ve gıda lojistiği',
                'İçecek ve FMCG dağıtım',
                'Endüstriyel üretim tesisleri',
                '3PL ve 4PL lojistik firmaları',
                'İlaç ve sağlık ürünleri depoları',
                'Elektronik dağıtım merkezleri',
                'Mobilya ve beyaz eşya depolama',
                'Otomotiv yedek parça',
                'Tekstil ve hazır giyim',
                'Kozmetik ve kişisel bakım',
                'Yapı market zincirleri',
                'Kitap ve kırtasiye dağıtım',
                'Oyuncak ve hobi ürünleri',
                'Tarım ürünleri ve tohum',
                'Kimyasal ve endüstriyel malzeme',
                'Cam ve seramik ürünler',
                'Metal işleme tesisleri',
                'Plastik ve ambalaj üretimi'
            ]], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                [
                    'question' => ['tr' => 'F4 201 transpalet bir vardiyada ne kadar süre çalışır?'],
                    'answer' => ['tr' => 'Standart 2x 24V/20Ah Li-Ion batarya paketiyle 6-8 saat kesintisiz çalışır. 4 modüle kadar genişletebilirsiniz. Tak-çıkar sistem ile operasyonunuz hiç durmaz.'],
                    'sort_order' => 1
                ],
                [
                    'question' => ['tr' => 'Dar koridorlarda F4 201 ne kadar manevra kabiliyeti sunar?'],
                    'answer' => ['tr' => '1360 mm dönüş yarıçapı ve 400 mm kompakt şasi ile 2160 mm koridorda 1000x1200 mm paletleri rahatça döndürürsünüz. Segmentinin en dar dönüş yarıçapıdır.'],
                    'sort_order' => 2
                ],
                [
                    'question' => ['tr' => 'Stabilizasyon tekerleği nedir, ne işe yarar?'],
                    'answer' => ['tr' => 'Bozuk zeminlerde veya rampalarda ağır yüklerle çalışırken transpaleti dengede tutan opsiyonel aksesuardır. Devrilme riskini sıfırlar, fabrikadan veya sonradan eklenebilir.'],
                    'sort_order' => 3
                ],
                [
                    'question' => ['tr' => 'F4 201 batarya şarj süresi ne kadardır?'],
                    'answer' => ['tr' => 'Standart 2x 24V-5A şarj ile 4-5 saatte tam dolum. Opsiyonel 2x 24V-10A hızlı şarj ile 2-3 saate düşer. Li-Ion ile ara şarj yapabilir, hafıza sorunu yaşamazsınız.'],
                    'sort_order' => 4
                ],
                [
                    'question' => ['tr' => 'İXTİF garanti ve teknik servis kapsamı nedir?'],
                    'answer' => ['tr' => 'Standart üretici garantisi ile gelir. Türkiye geneli 7/24 mobil servis. Yedek parça stoktan temin. Acil: 0216 755 3 555. Ek garanti paketleri mevcut.'],
                    'sort_order' => 5
                ],
                [
                    'question' => ['tr' => 'İkinci el veya kiralık F4 201 seçeneği var mı?'],
                    'answer' => ['tr' => 'Evet, hem ikinci el garanti belgeleriyle yenilenmiş modeller, hem kısa-uzun vadeli kiralama seçenekleri sunuyoruz. Detay: 0216 755 3 555 | info@ixtif.com'],
                    'sort_order' => 6
                ]
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                [
                    'name' => 'Stabilizasyon Tekerleği',
                    'description' => 'Bozuk zeminlerde ve rampalarda ağır yüklerle çalışırken devrilme riskini sıfırlar. Fabrikadan veya retrofit olarak eklenebilir.',
                    'is_standard' => false,
                    'is_optional' => true,
                    'price' => null
                ],
                [
                    'name' => 'Ekstra Li-Ion Batarya Modülü (2x 24V/20Ah)',
                    'description' => '4 modüle kadar genişletilebilir güç sistemi. Vardiya süresini ikiye katlar, kesintisiz operasyon sağlar.',
                    'is_standard' => false,
                    'is_optional' => true,
                    'price' => null
                ],
                [
                    'name' => 'Hızlı Şarj Ünitesi (2x 24V-10A)',
                    'description' => 'Şarj süresini 2-3 saate düşürür. Standart şarj ünitesine göre %50 daha hızlı dolum sağlar.',
                    'is_standard' => false,
                    'is_optional' => true,
                    'price' => null
                ],
                [
                    'name' => 'Uzun Çatal Seti (1500 mm)',
                    'description' => 'Özel boy paletler için 1500 mm uzun çatal seçeneği. Fabrikadan veya sonradan monte edilebilir.',
                    'is_standard' => false,
                    'is_optional' => true,
                    'price' => null
                ],
                [
                    'name' => 'Geniş Çatal Seti (685 mm)',
                    'description' => 'Büyük paletler için 685 mm geniş çatal açıklığı. Maksimum stabilite sağlar.',
                    'is_standard' => false,
                    'is_optional' => true,
                    'price' => null
                ],
                [
                    'name' => 'Entegre Buzzer Sistemi',
                    'description' => 'Güvenlik için sesli uyarı sistemi. Geri manevralarda ve dar geçişlerde uyarı verir.',
                    'is_standard' => true,
                    'is_optional' => false,
                    'price' => null
                ]
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                [
                    'name' => 'CE',
                    'year' => 2024,
                    'authority' => 'TÜV Rheinland'
                ],
                [
                    'name' => 'ISO 9001:2015',
                    'year' => 2024,
                    'authority' => 'EP Equipment'
                ],
                [
                    'name' => 'DIN EN 16796',
                    'year' => 2024,
                    'authority' => 'European Standard'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['tr' => '24 Ay Tam Kapsamlı Garanti | Li-Ion Batarya Dahil'], JSON_UNESCAPED_UNICODE),
            'tags' => json_encode(['f4-201', 'transpalet', 'li-ion', '48v', 'dar-koridor', 'tak-cikar-batarya', 'ixtif'], JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ MASTER Product eklendi (ID: {$masterId}, SKU: F4-201-MASTER)");

        // ============================================================
        // CHILD VARIANTS - F4 201 Varyantları
        // ============================================================
        $variants = [
            [
                'sku' => 'F4-201-STD',
                'title' => 'F4 201 - Standart Çatal (1150x560 mm)',
                'slug' => 'f4-201-standart-catal-1150x560',
                'variant_type' => 'standart-catal',
                'short_description' => 'F4 201 standart çatal (1150x560 mm) EUR palet taşıma için ideal. 48V Li-Ion güç, 2 ton kapasite.',
                'fork_length' => 1150,
                'fork_width' => 560,
                'overall_width' => 590,
                'battery_capacity' => '2x 24V/20Ah',
                'features' => [
                    'Standart 1150x560 mm çatal - EUR palet uyumlu',
                    '48V Li-Ion güç platformu - 2 ton kapasite',
                    '140 kg hafif gövde - üstün manevra',
                    'Tak-çıkar batarya sistemi - kesintisiz çalışma'
                ]
            ],
            [
                'sku' => 'F4-201-WIDE',
                'title' => 'F4 201 - Geniş Çatal (1150x685 mm)',
                'slug' => 'f4-201-genis-catal-1150x685',
                'variant_type' => 'genis-catal',
                'short_description' => 'F4 201 geniş çatal (1150x685 mm) büyük paletlerin güvenli taşınması için. Maksimum stabilite.',
                'fork_length' => 1150,
                'fork_width' => 685,
                'overall_width' => 695,
                'battery_capacity' => '2x 24V/20Ah',
                'features' => [
                    'Geniş 1150x685 mm çatal - büyük palet uyumlu',
                    'Maksimum stabilite - büyük yüklerde güvenlik',
                    'Stabilizasyon tekerleği opsiyonu - ek koruma',
                    '48V Li-Ion güvenilirlik'
                ]
            ],
            [
                'sku' => 'F4-201-SHORT',
                'title' => 'F4 201 - Kısa Çatal (900x560 mm)',
                'slug' => 'f4-201-kisa-catal-900x560',
                'variant_type' => 'kisa-catal',
                'short_description' => 'F4 201 kısa çatal (900x560 mm) dar alanlarda maksimum çeviklik. Ultra hafif manevra.',
                'fork_length' => 900,
                'fork_width' => 560,
                'overall_width' => 590,
                'battery_capacity' => '2x 24V/20Ah',
                'features' => [
                    'Kısa 900 mm çatal - dar alan çözümü',
                    'Ultra hafif manevra - sıkı dönüşler',
                    'Küçük paletler için ideal',
                    'Kompakt operasyonlarda maksimum verimlilik'
                ]
            ],
            [
                'sku' => 'F4-201-LONG',
                'title' => 'F4 201 - Uzun Çatal (1500x560 mm)',
                'slug' => 'f4-201-uzun-catal-1500x560',
                'variant_type' => 'uzun-catal',
                'short_description' => 'F4 201 uzun çatal (1500x560 mm) özel boy paletlerin güvenli taşınması için ideal.',
                'fork_length' => 1500,
                'fork_width' => 560,
                'overall_width' => 590,
                'battery_capacity' => '2x 24V/20Ah',
                'features' => [
                    'Uzun 1500 mm çatal - özel boy paletler',
                    'Uzun yüklerde dengeli dağılım',
                    'Stabilizasyon tekerleği önerilir',
                    'Endüstriyel dayanıklılık'
                ]
            ],
            [
                'sku' => 'F4-201-EXT-BAT',
                'title' => 'F4 201 - Genişletilmiş Batarya (4x 24V/20Ah)',
                'slug' => 'f4-201-genisletilmis-batarya-4x',
                'variant_type' => 'genisletilmis-batarya',
                'short_description' => 'F4 201 genişletilmiş batarya (4x 24V/20Ah) ile çift vardiya kesintisiz operasyon.',
                'fork_length' => 1150,
                'fork_width' => 560,
                'overall_width' => 590,
                'battery_capacity' => '4x 24V/20Ah',
                'features' => [
                    'Genişletilmiş 4x 24V/20Ah güç',
                    'Çift vardiya kesintisiz operasyon',
                    '12-16 saat çalışma süresi',
                    'Yoğun operasyonlarda maksimum verimlilik'
                ]
            ]
        ];

        $variantCount = 0;
        foreach ($variants as $variantData) {
            $childId = DB::table('shop_products')->insertGetId([
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'sku' => $variantData['sku'],
                'model_number' => 'F4-201',
                'title' => json_encode(['tr' => $variantData['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => $variantData['slug']], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $variantData['short_description']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <p>{$variantData['short_description']}</p>
    <p>48V Li-Ion güvenilirliği ile endüstriyel performans. İXTİF kalite garantisiyle.</p>
</section>
<section class="marketing-body">
    <h3>Varyant Özellikleri</h3>
    <ul>
        <li>Çatal Boyutu: {$variantData['fork_length']} x {$variantData['fork_width']} mm</li>
        <li>Batarya Kapasitesi: {$variantData['battery_capacity']}</li>
        <li>Gövde Genişliği: {$variantData['overall_width']} mm</li>
    </ul>
    <p><strong>İletişim:</strong> 0216 755 3 555 | info@ixtif.com</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),
                'parent_product_id' => $masterId,
                'is_master_product' => false,
                'variant_type' => $variantData['variant_type'],
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => 1,
                'weight' => 140,
                'technical_specs' => json_encode([
                    'capacity' => ['load_capacity' => ['value' => 2000, 'unit' => 'kg']],
                    'dimensions' => [
                        'fork_dimensions' => ['length' => $variantData['fork_length'], 'width' => $variantData['fork_width'], 'unit' => 'mm'],
                        'overall_width' => ['value' => $variantData['overall_width'], 'unit' => 'mm']
                    ],
                    'electrical' => [
                        'battery_system' => ['configuration' => $variantData['battery_capacity'] . ' Li-Ion']
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'features' => json_encode(['tr' => ['list' => $variantData['features']]], JSON_UNESCAPED_UNICODE),
                'primary_specs' => json_encode([
                    ['label' => 'Çatal Boyutu', 'value' => "{$variantData['fork_length']} x {$variantData['fork_width']} mm"],
                    ['label' => 'Batarya', 'value' => $variantData['battery_capacity']],
                    ['label' => 'Gövde Genişliği', 'value' => "{$variantData['overall_width']} mm"],
                    ['label' => 'Kapasite', 'value' => '2 Ton']
                ], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'is_featured' => 0,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $variantCount++;
            $this->command->info("  ➕ Child Variant: {$variantData['title']} (ID: {$childId}, SKU: {$variantData['sku']})");
        }

        $this->command->info('🎉 F4 201 Product-Based Variant Sistemi tamamlandı!');
        $this->command->info('📊 İstatistik: Master: 1 | Variants: ' . $variantCount);
        $this->command->info('✨ YENİ: Accessories (6 adet) + Certifications (3 adet) + Highlighted Features (4 adet) eklendi!');
    }
}
