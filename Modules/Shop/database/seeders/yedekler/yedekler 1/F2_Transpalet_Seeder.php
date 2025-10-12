<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F2 - 1.5 Ton 24V Li-Ion Transpalet (Retail/Perakende Master + Variants)
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F2/F2-EN-Brochure.pdf
 * Marka: İXTİF (brand_id = 1)
 * Kategori: TRANSPALETLER (category_id = 165)
 *
 * Yapı: 1 Master + 4 Child Variant (çatal/batarya kombinasyonları)
 */
class F2_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F2 Li-Ion Transpalet (Master + Variants) ekleniyor...');

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
            ->where('sku', 'LIKE', 'F2-%')
            ->pluck('product_id');

        if ($existingProducts->isNotEmpty()) {
            DB::table('shop_products')->whereIn('product_id', $existingProducts)->delete();
            $this->command->info('🧹 Eski F2 kayıtları temizlendi');
        }

        // ============================================================
        // MASTER PRODUCT - F2 Transpalet Serisi
        // ============================================================
        $masterId = DB::table('shop_products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => 'F2-MASTER',
            'model_number' => 'F2',
            'parent_product_id' => null,
            'is_master_product' => true,
            'title' => json_encode(['tr' => 'F2 Elektrikli Transpalet 1.5T - Li-Ion Perakende Serisi'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f2-elektrikli-transpalet-1-5t-li-ion-perakende'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'F2 Li-Ion transpalet serisi; süpermarket, AVM ve perakende sektörü için 120 kg ultra hafif, fırsat şarjı özellikli, şık tasarımlı 1.5 ton transpalet çözümüdür.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <h2>Perakende Sektörünün Yeni Gözdesi</h2>
    <p><strong>F2 transpalet serisini mağazanıza soktuğunuz anda müşterileriniz "Bu cihaz ne kadar hafif!" diye şaşıracak.</strong> 120 kg ultra hafif gövdesi ile operatörleriniz yorulmadan çalışırken, 24V/20Ah Li-Ion batarya sistemi fırsat şarjı sayesinde mola aralarında anında dolacak.</p>
    <p>İXTİF, F2'yi EPT12-EZ'nin kanıtlanmış platformu üzerine kurarak retail sektöründe sessiz, şık ve sıfır bakım gerektiren bir çözüm yarattı. Li-Ion teknolojisi ile su ekleme, akü odası veya asit bakımı derdine son verin.</p>
</section>
<section class="marketing-body">
    <h3>Li-Ion Perakende Avantajları</h3>
    <ul>
        <li><strong>Fırsat Şarjı</strong> – Kahve molasında bile şarj olur, gün boyu kesintisiz çalışır.</li>
        <li><strong>Sıfır Bakım</strong> – Su ekleme, asit kontrolü gerekmez. Tak-çalıştır mantığı.</li>
        <li><strong>Ultra Hafif</strong> – Her bir Li-Ion batarya modülü sadece 5 kg, toplam servis ağırlığı 120 kg.</li>
        <li><strong>2000+ Döngü Ömrü</strong> – Yıllarca yatırım getirisi sağlar.</li>
    </ul>
    <h3>İXTİF Perakende Desteği</h3>
    <p>Perakende zincirleri için filo kiralama, ikinci el seçenekler, yedek parça stoku ve 7/24 servis hattı: <strong>0216 755 3 555</strong> | <strong>info@ixtif.com</strong></p>
    <p><strong>SEO Anahtar Kelimeleri:</strong> F2 transpalet, Li-Ion transpalet, perakende transpalet, süpermarket transpalet, AVM transpalet, fırsat şarjı transpalet, İXTİF retail çözümleri.</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'currency' => 'TRY',
            'stock_tracking' => 1,
            'current_stock' => 0,
            'lead_time_days' => 30,
            'weight' => 120,
            'dimensions' => json_encode(['length' => 1550, 'width' => 695, 'height' => 105, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode([
                'capacity' => [
                    'load_capacity' => ['value' => 1500, 'unit' => 'kg'],
                    'load_center_distance' => ['value' => 600, 'unit' => 'mm'],
                    'service_weight' => ['value' => 120, 'unit' => 'kg'],
                ],
                'dimensions' => [
                    'overall_length' => ['value' => 1550, 'unit' => 'mm'],
                    'overall_width' => ['standard' => 590, 'wide' => 695, 'unit' => 'mm'],
                    'fork_dimensions' => ['thickness' => 50, 'width' => 150, 'length' => 1150, 'unit' => 'mm'],
                    'ground_clearance' => ['value' => 30, 'unit' => 'mm'],
                    'lift_height' => ['value' => 105, 'unit' => 'mm'],
                ],
                'performance' => [
                    'travel_speed' => ['laden' => 4.0, 'unladen' => 4.5, 'unit' => 'km/h'],
                    'max_gradeability' => ['laden' => 6, 'unladen' => 12, 'unit' => '%'],
                ],
                'electrical' => [
                    'drive_motor_rating' => ['value' => 0.65, 'unit' => 'kW', 'duty' => 'S2 60 min'],
                    'lift_motor_rating' => ['value' => 0.5, 'unit' => 'kW', 'duty' => 'S3 15%'],
                    'battery_system' => [
                        'voltage' => 24,
                        'capacity' => 20,
                        'unit' => 'V/Ah',
                        'configuration' => '24V/20Ah Li-Ion değiştirilebilir modül (fırsat şarjı özellikli)'
                    ],
                    'battery_weight' => ['value' => 5, 'unit' => 'kg'],
                    'charger_options' => [
                        'standard' => '24V/5A harici şarj ünitesi',
                        'optional' => ['24V/10A hızlı şarj ünitesi']
                    ],
                ],
                'tyres' => [
                    'type' => 'Poliüretan',
                    'drive_wheel' => '180 × 50 mm Poliüretan',
                    'load_wheel' => '74 × 60 mm Poliüretan (çift)',
                ],
                'options' => [
                    'fork_lengths_mm' => [900, 1000, 1150, 1500],
                    'fork_spreads_mm' => [560, 685],
                ]
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                'tr' => [
                    'list' => [
                        'F2 transpalet serisi 24V/20Ah Li-Ion ile perakende sektörüne fırsat şarjı getirir.',
                        '120 kg ultra hafif gövde, operatör yorgunluğunu minimize eder.',
                        '5 kg batarya ağırlığı, 60 saniyede değişim kolaylığı sağlar.',
                        '2000+ döngü ömürlü Li-Ion teknolojisi, yıllarca yatırım getirisi sağlar.',
                        'Ergonomik el ayası kontrolü, uzun vardiyalarda konfor sunar.',
                        'İXTİF stoktan hızlı teslim, yerinde montaj ve retail sektörü desteği sunar.'
                    ],
                    'branding' => [
                        'slogan' => 'Perakendede hız, sahada şıklık: F2 ile mağazanıza prestij katın.',
                        'motto' => 'İXTİF farkı ile 1.5 tonluk yükler bile hafifler.',
                        'technical_summary' => 'F2, 24V Li-Ion güç platformu, 0.65 kW BLDC sürüş motoru ve ultra hafif 120 kg servis ağırlığıyla perakende sektöründe sessiz, hızlı ve sıfır bakım sunar.'
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                [
                    'icon' => 'battery-charging',
                    'priority' => 1,
                    'title' => ['tr' => 'Fırsat Şarjı'],
                    'description' => ['tr' => '24V/20Ah Li-Ion batarya, kahve molalarında bile şarj olur. Gün boyu kesintisiz çalışma.']
                ],
                [
                    'icon' => 'feather',
                    'priority' => 2,
                    'title' => ['tr' => 'Ultra Hafif'],
                    'description' => ['tr' => '120 kg toplam ağırlık ve 5 kg batarya modülü ile operatör yorgunluğu minimumda.']
                ],
                [
                    'icon' => 'paint-brush',
                    'priority' => 3,
                    'title' => ['tr' => 'Şık Retail Tasarım'],
                    'description' => ['tr' => 'EPT12-EZ platformu üzerine kurulu estetik tasarım, AVM ve mağazalarda vitrin kalitesi.']
                ],
                [
                    'icon' => 'tools',
                    'priority' => 4,
                    'title' => ['tr' => 'Sıfır Bakım'],
                    'description' => ['tr' => 'Li-Ion teknolojisi ile su ekleme, asit kontrolü veya akü odası gerektirmez.']
                ]
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                'tr' => [
                    'Süpermarketlerde gece vardiyası raf ikmal operasyonları',
                    'AVM arka sahalarında sessiz ve şık malzeme taşıma',
                    'Perakende zincir depolarında sipariş hazırlama ve sevkiyat',
                    'Küçük market ve bakkal depolarında kompakt alan yönetimi',
                    'E-ticaret perakende depolarında hızlı sipariş toplama',
                    'Lüks mağaza arka sahalarında prestijli operasyon görünümü',
                    'Gıda perakende zincirlerde hijyenik ve kolay temizlenebilir taşıma',
                    'Tekstil mağazalarında hafif ve kompakt stok transfer'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                'tr' => [
                    '24V Li-Ion fırsat şarjı sistemi ile segmentindeki en hızlı şarj çözümü',
                    '120 kg ultra hafif tasarım sayesinde operatör yorgunluğunda %40 azalma',
                    'EPT12-EZ kanıtlanmış platformu üzerine kurulu güvenilir mühendislik',
                    'Sıfır bakım Li-Ion teknolojisi ile yıllık bakım maliyetlerinde %70 tasarruf',
                    'İXTİF stoktan hızlı teslim ve perakende sektörü odaklı yerinde devreye alma',
                    'İXTİF ikinci el, kiralık ve operasyonel leasing seçenekleriyle esneklik',
                    'Türkiye geneli 7/24 mobil servis ağı ile perakende filolarına öncelikli destek'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                'tr' => [
                    'Süpermarket zincirleri',
                    'AVM arka saha operasyonları',
                    'Perakende gıda depoları',
                    'E-ticaret perakende fulfilment',
                    'Küçük market ve bakkal depoları',
                    'Tekstil mağaza zincirleri',
                    'Ayakkabı ve aksesuar mağazaları',
                    'Elektronik perakende depoları',
                    'Kozmetik ve kişisel bakım zincirleri',
                    'Kitap ve kırtasiye mağazaları',
                    'Ev tekstil mağazaları',
                    'Spor malzemeleri perakende',
                    'Oyuncak mağaza zincirleri',
                    'Hırdavat ve yapı market perakende',
                    'Petshop zincirleri',
                    'Sağlık ürünleri eczane depoları',
                    'Optik mağaza zincirleri',
                    'Lüks perakende mağazalar',
                    'Mobilya showroom arka sahaları',
                    'Gıda franchise zincirleri',
                    'Yerel toptancı depoları',
                    'Kargo şubeleri perakende transfer noktaları'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                [
                    'question' => ['tr' => 'F2 transpalet bir vardiyada kaç saat çalışır?'],
                    'answer' => ['tr' => '24V/20Ah Li-Ion batarya ile tek şarjda 5-6 saat kesintisiz çalışır. Fırsat şarjı özelliği sayesinde kahve ve öğle molalarında kısa süreli şarj ile gün boyu operasyon devam eder.'],
                    'sort_order' => 1
                ],
                [
                    'question' => ['tr' => 'Fırsat şarjı nasıl çalışır?'],
                    'answer' => ['tr' => 'Li-Ion teknolojisi sayesinde batarya %20-30 seviyesindeyken bile 15-20 dakika kısa şarj ile %50-60 seviyesine ulaşır. Bu sayede vardiya ortasında uzun şarj beklemenize gerek kalmaz. Normal 220V prize harici şarj ünitesi ile bağlanır.'],
                    'sort_order' => 2
                ],
                [
                    'question' => ['tr' => 'Süpermarket ve AVM için neden ideal?'],
                    'answer' => ['tr' => 'F2, 120 kg ultra hafif yapısı ve sessiz Li-Ion sistemi ile müşteri saatlerinde bile kullanılabilir. Şık tasarımı sayesinde arka sahadan vitrin alanına çıksa bile görsel kirlilik yaratmaz. Akü odası, su ekleme gibi bakım gerektirmediği için sıkışık retail alanlarda pratiktir.'],
                    'sort_order' => 3
                ],
                [
                    'question' => ['tr' => 'Batarya ömrü ne kadar, garanti kapsamında mı?'],
                    'answer' => ['tr' => 'Li-Ion batarya 2000+ döngü ömrü sunar, yani günde 1 şarj ile yaklaşık 5-6 yıl kullanılabilir. İXTİF 24 ay tam kapsamlı garanti verir; batarya, motor, elektronik ve şasi bu kapsamdadır.'],
                    'sort_order' => 4
                ],
                [
                    'question' => ['tr' => 'Farklı çatal ölçüleri mevcut mu?'],
                    'answer' => ['tr' => 'Standart 1150 x 560 mm çatal dışında 900 mm, 1000 mm, 1500 mm uzunluklar ve 560/685 mm açıklık kombinasyonları sunulur. Euro palet, endüstriyel palet veya özel ölçü paletler için uygun seçenekler mevcuttur.'],
                    'sort_order' => 5
                ],
                [
                    'question' => ['tr' => 'Kiralık veya ikinci el seçenekleri var mı?'],
                    'answer' => ['tr' => 'İXTİF, F2 için kısa/uzun dönem kiralama, operasyonel leasing ve stoktan ikinci el seçenekler sunar. Perakende filolarına özel bakım ve yedek parça paketli teklifler hazırlanır. Detay için 0216 755 3 555 numarasını arayabilirsiniz.'],
                    'sort_order' => 6
                ],
                [
                    'question' => ['tr' => 'Servis ve yedek parça desteği nasıl?'],
                    'answer' => ['tr' => 'İXTİF Türkiye genelinde mobil servis ekipleri ile 7/24 destek sağlar. Perakende sektörü için öncelikli müdahale programı mevcuttur. Yedek parça stoğu İstanbul merkezde tutulur, ertesi gün kargo ile gönderilir.'],
                    'sort_order' => 7
                ],
                [
                    'question' => ['tr' => 'Operatör eğitimi veriliyor mu?'],
                    'answer' => ['tr' => 'Evet, İXTİF uzman ekibi cihazı sahada devreye alır ve operatörlerin güvenli kullanımı için yerinde eğitim seti verir. Perakende personelinin hızlı adaptasyonu için görsel kullanım kılavuzu da sunulur.'],
                    'sort_order' => 8
                ],
                [
                    'question' => ['tr' => 'Maksimum rampa eğimi ne kadar?'],
                    'answer' => ['tr' => 'F2, yüklü halde %6, yüksüz halde %12 eğime kadar güvenle çıkabilir. Perakende depo rampaları için yeterli performans sunar. Elektromanyetik fren sistemi rampalarda güvenli duruş sağlar.'],
                    'sort_order' => 9
                ],
                [
                    'question' => ['tr' => 'Teklif nasıl alabilirim?'],
                    'answer' => ['tr' => 'F2 transpalet için İXTİF ile iletişime geçin: 0216 755 3 555 veya info@ixtif.com adresine yazın. Filo büyüklüğünüze göre özel fiyat, kiralama veya ikinci el seçenekleri ile detaylı teklif sunulur.'],
                    'sort_order' => 10
                ],
                [
                    'question' => ['tr' => 'F2 teknik slogan ve mottosu nedir?'],
                    'answer' => ['tr' => 'Slogan: "Perakendede hız, sahada şıklık." Motto: "İXTİF farkı ile 1.5 tonluk yükler bile hafifler." Bu mesajlar ürün sayfasında ayrı kartlarda vurgulanır.'],
                    'sort_order' => 11
                ]
            ], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '1.5 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 24V/20Ah'],
                ['label' => 'Ağırlık', 'value' => '120 kg'],
                ['label' => 'Fırsat Şarjı', 'value' => 'Mevcut'],
                ['label' => 'Sektör', 'value' => 'Perakende/Retail']
            ], JSON_UNESCAPED_UNICODE),
            'media_gallery' => json_encode([
                ['type' => 'image', 'url' => 'products/f2/main.jpg', 'is_primary' => true, 'sort_order' => 1],
                ['type' => 'image', 'url' => 'products/f2/battery.jpg', 'is_primary' => false, 'sort_order' => 2],
                ['type' => 'image', 'url' => 'products/f2/retail.jpg', 'is_primary' => false, 'sort_order' => 3],
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['tr' => '24 Ay Tam Kapsamlı Garanti | Li-Ion Batarya Dahil'], JSON_UNESCAPED_UNICODE),
            'tags' => json_encode(['f2', 'transpalet', 'li-ion', 'perakende', 'retail', 'supermarket', 'avm', 'firsat-sarji', 'ixtif'], JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ F2 MASTER eklendi (ID: {$masterId})");

        // ============================================================
        // CHILD VARIANTS - F2 Varyantları
        // ============================================================
        $variants = [
            [
                'sku' => 'F2-STD',
                'variant_type' => 'standart-catal',
                'title' => 'F2 - Standart Çatal (1150x560 mm)',
                'fork_length' => 1150,
                'fork_width' => 560,
                'battery_config' => '1x 24V/20Ah',
                'use_case_focus' => 'süpermarket',
                'features' => [
                    'tr' => [
                        'list' => [
                            'Standart 1150 x 560 mm çatal, Euro palet (800x1200) ve endüstriyel palet (1000x1200) için ideal.',
                            '24V/20Ah Li-Ion batarya ile 5 saate kadar süpermarket ikmal operasyonu.',
                            'Fırsat şarjı özelliği ile kahve molasında 15 dakika şarj, %30 kapasite artışı.',
                            'Sessiz Li-Ion sistemi ile müşteri saatlerinde bile rahatlıkla kullanılır.',
                            '120 kg hafif gövde, operatör yorgunluğunu minimize eder.',
                            'İXTİF stoktan hızlı teslim, süpermarket zincirleri için toplu filo desteği.'
                        ],
                        'branding' => [
                            'slogan' => 'Süpermarketin en sessiz yardımcısı: F2 Standart.',
                            'motto' => 'Euro palet taşımada İXTİF güvencesi.',
                            'technical_summary' => 'F2 Standart, 1150 mm çatal uzunluğu ve 24V Li-Ion sistemi ile süpermarket raf ikmalinde en hızlı ve sessiz çözümü sunar.'
                        ]
                    ]
                ],
                'highlighted_features' => [
                    [
                        'icon' => 'shopping-cart',
                        'priority' => 1,
                        'title' => ['tr' => 'Süpermarket Optimized'],
                        'description' => ['tr' => '1150 mm çatal Euro palet ve standart endüstriyel paletler için mükemmel uyum sağlar.']
                    ],
                    [
                        'icon' => 'volume-mute',
                        'priority' => 2,
                        'title' => ['tr' => 'Sessiz Çalışma'],
                        'description' => ['tr' => 'Li-Ion motor sistemi ile müşteri saatlerinde bile gürültü yapmadan raf ikmal.']
                    ],
                    [
                        'icon' => 'clock',
                        'priority' => 3,
                        'title' => ['tr' => '15 Dakika Fırsat Şarjı'],
                        'description' => ['tr' => 'Kahve molasında 15 dakika şarj ile %30 kapasite artışı, gün boyu kesintisiz çalışma.']
                    ]
                ],
                'use_cases' => [
                    'tr' => [
                        'Süpermarket gece vardiyası raf ikmal operasyonları',
                        'Perakende zincir depolarında Euro palet taşıma',
                        'Küçük market stok sahalarında kompakt alan yönetimi',
                        'Gıda perakende depo sevkiyat hazırlık',
                        'E-ticaret perakende depolarında sipariş toplama',
                        'AVM arka saha standart palet transfer'
                    ]
                ],
                'competitive_advantages' => [
                    'tr' => [
                        '1150 mm çatal uzunluğu ile Euro palet ve endüstriyel palet taşımada standart çözüm',
                        'Fırsat şarjı ile 15 dakikada %30 kapasite artışı, rakiplerden %50 daha hızlı',
                        'Sessiz Li-Ion sistemi ile müşteri saatlerinde bile kullanılabilir',
                        '120 kg hafif gövde sayesinde operatör yorgunluğunda %40 azalma',
                        'İXTİF stoktan hızlı teslim ile süpermarket zincirleri 7 gün içinde sahaya iner',
                        'Sıfır bakım Li-Ion teknolojisi ile yıllık bakım maliyetlerinde %70 tasarruf'
                    ]
                ],
                'target_industries' => [
                    'tr' => [
                        'Süpermarket zincirleri',
                        'Küçük market ve bakkallar',
                        'Perakende gıda depoları',
                        'E-ticaret perakende fulfilment',
                        'AVM arka saha operasyonları',
                        'Gıda franchise zincirleri',
                        'Yerel toptancı depoları',
                        'Kargo şubeleri perakende transfer',
                        'Tekstil mağaza zincirleri',
                        'Elektronik perakende depoları',
                        'Kitap ve kırtasiye mağazaları',
                        'Petshop zincirleri',
                        'Hırdavat ve yapı market perakende',
                        'Mobilya showroom arka sahaları',
                        'Spor malzemeleri perakende',
                        'Oyuncak mağaza zincirleri',
                        'Kozmetik zincirleri',
                        'Ayakkabı mağazaları',
                        'Optik mağaza zincirleri',
                        'Sağlık ürünleri eczane depoları'
                    ]
                ],
                'faq_data' => [
                    [
                        'question' => ['tr' => 'Standart çatal hangi palet türlerine uygun?'],
                        'answer' => ['tr' => '1150 x 560 mm çatal, Euro palet (800x1200 mm) ve endüstriyel palet (1000x1200 mm) için idealdir. Süpermarket ve perakende sektöründe %90 oranında kullanılan standart palet ölçüleridir.'],
                        'sort_order' => 1
                    ],
                    [
                        'question' => ['tr' => 'Süpermarkette gece vardiyasında kaç palet taşınır?'],
                        'answer' => ['tr' => 'Tek şarjda 5 saat çalışma ile ortalama 60-80 palet ikmal yapılabilir. Fırsat şarjı ile bu sayı 100+ palete çıkar.'],
                        'sort_order' => 2
                    ],
                    [
                        'question' => ['tr' => 'Müşteri saatlerinde kullanılabilir mi?'],
                        'answer' => ['tr' => 'Evet, Li-Ion sessiz motor sistemi sayesinde müşteri saatlerinde bile rahatlıkla kullanılabilir. Gürültü seviyesi konuşma sesinin altındadır.'],
                        'sort_order' => 3
                    ],
                    [
                        'question' => ['tr' => 'Fırsat şarjı süpermarkette nasıl yapılır?'],
                        'answer' => ['tr' => 'Arka sahada normal 220V prize harici şarj ünitesi takılır. Kahve ve öğle molalarında 15-20 dakika kısa şarj yapılır, %20-30 kapasite artışı sağlanır.'],
                        'sort_order' => 4
                    ],
                    [
                        'question' => ['tr' => 'Süpermarket zincirleri için toplu teklif mevcut mu?'],
                        'answer' => ['tr' => 'İXTİF, 5+ ünite sipariş veren süpermarket zincirleri için özel fiyat, filo kiralama ve operasyonel leasing paketleri sunar. Detay için 0216 755 3 555 numarasını arayın.'],
                        'sort_order' => 5
                    ]
                ]
            ],
            [
                'sku' => 'F2-EXT',
                'variant_type' => 'uzun-catal',
                'title' => 'F2 - Uzun Çatal (1500x560 mm)',
                'fork_length' => 1500,
                'fork_width' => 560,
                'battery_config' => '1x 24V/20Ah',
                'use_case_focus' => 'tekstil-mobilya',
                'features' => [
                    'tr' => [
                        'list' => [
                            'Uzun 1500 x 560 mm çatal, büyük boy paletler ve özel ölçü yükler için tasarlandı.',
                            'Tekstil, mobilya ve beyaz eşya sektörlerinde uzun paletlerin güvenli taşıması.',
                            '24V/20Ah Li-Ion batarya ile 5 saate kadar uzun palet transfer operasyonu.',
                            'Fırsat şarjı özelliği ile mobilya showroom arka sahalarında kesintisiz çalışma.',
                            '120 kg hafif gövde, uzun çatalın ağırlığını dengeleyerek manevra kolaylığı sağlar.',
                            'İXTİF mobilya ve tekstil sektörü için özel devreye alma ve operatör eğitimi sunar.'
                        ],
                        'branding' => [
                            'slogan' => 'Uzun yükler için uzun ömürlü çözüm: F2 Uzun Çatal.',
                            'motto' => 'Mobilya ve tekstilde İXTİF farkı.',
                            'technical_summary' => 'F2 Uzun Çatal, 1500 mm çatal uzunluğu ve 24V Li-Ion sistemi ile tekstil, mobilya ve beyaz eşya sektörlerinde büyük boy paletlerin güvenli taşımasını sağlar.'
                        ]
                    ]
                ],
                'highlighted_features' => [
                    [
                        'icon' => 'arrows-alt-h',
                        'priority' => 1,
                        'title' => ['tr' => '1500 mm Uzun Çatal'],
                        'description' => ['tr' => 'Büyük boy paletler, mobilya ve tekstil ürünleri için özel tasarlanmış uzun çatal.']
                    ],
                    [
                        'icon' => 'couch',
                        'priority' => 2,
                        'title' => ['tr' => 'Mobilya & Tekstil Optimized'],
                        'description' => ['tr' => 'Showroom arka sahaları ve büyük ölçekli perakende depoları için ideal çözüm.']
                    ],
                    [
                        'icon' => 'balance-scale',
                        'priority' => 3,
                        'title' => ['tr' => 'Dengeli Tasarım'],
                        'description' => ['tr' => '120 kg hafif gövde, uzun çatalın ağırlığını dengeleyerek kolay manevra sağlar.']
                    ]
                ],
                'use_cases' => [
                    'tr' => [
                        'Mobilya showroom arka sahalarında büyük boy palet taşıma',
                        'Tekstil mağaza zincirlerinde uzun rulo ve büyük paket transfer',
                        'Beyaz eşya perakende depolarında özel ölçü palet operasyonu',
                        'Halı ve ev tekstil mağazalarında rulo taşıma',
                        'Büyük elektronik ürünler (TV, buzdolabı) perakende depo sevkiyatı',
                        'İnşaat market arka sahalarında uzun ahşap ve profil malzeme taşıma'
                    ]
                ],
                'competitive_advantages' => [
                    'tr' => [
                        '1500 mm uzun çatal ile büyük boy paletlerde segmentindeki en geniş kapsama',
                        'Li-Ion fırsat şarjı ile mobilya showroom arka sahalarında kesintisiz operasyon',
                        '120 kg hafif gövde, uzun çatalın ağırlığını dengeler ve kolay manevra sağlar',
                        'Tekstil ve mobilya sektörü için İXTİF özel devreye alma ve eğitim programı',
                        'Sıfır bakım Li-Ion teknolojisi ile mobilya ve tekstil depolarında temiz çalışma ortamı',
                        'İXTİF ikinci el ve kiralık seçenekleri ile mobilya zincirleri için esneklik'
                    ]
                ],
                'target_industries' => [
                    'tr' => [
                        'Mobilya showroom arka sahaları',
                        'Tekstil mağaza zincirleri',
                        'Beyaz eşya perakende depoları',
                        'Halı ve ev tekstil mağazaları',
                        'İnşaat market arka sahaları',
                        'Büyük elektronik ürünler perakende',
                        'Kapı ve pencere showroom depoları',
                        'Ahşap ve dekorasyon mağazaları',
                        'Bahçe mobilyası perakende',
                        'Ofis mobilyası showroom',
                        'Yatak ve baza perakende',
                        'Perde ve tül mağazaları',
                        'Laminat parke perakende',
                        'Mutfak dolabı showroom',
                        'Aydınlatma perakende depoları',
                        'Banyo mobilyası showroom',
                        'Avize ve dekorasyon mağazaları',
                        'Plastik mobilya perakende',
                        'Bahçe ve dış mekan ürünleri',
                        'Masa ve sandalye perakende'
                    ]
                ],
                'faq_data' => [
                    [
                        'question' => ['tr' => 'Uzun çatal hangi ölçü paletlere uygun?'],
                        'answer' => ['tr' => '1500 x 560 mm çatal, 1200-1400 mm uzunluğundaki büyük boy paletler, mobilya ve tekstil ürünlerinin özel ölçü paletleri için idealdir. Standart Euro paletten %30 daha uzun yükleri güvenle taşır.'],
                        'sort_order' => 1
                    ],
                    [
                        'question' => ['tr' => 'Mobilya showroom arka sahalarında nasıl kullanılır?'],
                        'answer' => ['tr' => 'Mobilya paketleri genellikle uzun ve hacimlidir. 1500 mm çatal, koltuk, yatak, dolap gibi büyük ürünlerin paletlenmiş halini tek seferde taşır. Fırsat şarjı ile gün boyu showroom arka sahasında kesintisiz çalışır.'],
                        'sort_order' => 2
                    ],
                    [
                        'question' => ['tr' => 'Tekstil rulolarını taşıyabilir mi?'],
                        'answer' => ['tr' => 'Evet, 1500 mm çatal uzunluğu tekstil rulolarının paletlenmeden de taşınmasına olanak tanır. 1.5 ton kapasiteyle kumaş rulolarını güvenle transfer edebilirsiniz.'],
                        'sort_order' => 3
                    ],
                    [
                        'question' => ['tr' => 'Uzun çatal manevra zorluğu yaratır mı?'],
                        'answer' => ['tr' => 'Hayır, F2 ultra hafif 120 kg gövdesi sayesinde 1500 mm çatalın ağırlığını dengeler. Ergonomik timoni ile kolay manevra sağlanır.'],
                        'sort_order' => 4
                    ],
                    [
                        'question' => ['tr' => 'Mobilya zincirleri için toplu teklif mevcut mu?'],
                        'answer' => ['tr' => 'İXTİF, mobilya ve tekstil zincirleri için filo kiralama, ikinci el seçenekler ve operasyonel leasing paketleri sunar. Detay için 0216 755 3 555 veya info@ixtif.com adresine başvurun.'],
                        'sort_order' => 5
                    ]
                ]
            ],
            [
                'sku' => 'F2-WIDE',
                'variant_type' => 'genis-catal',
                'title' => 'F2 - Geniş Çatal (1150x685 mm)',
                'fork_length' => 1150,
                'fork_width' => 685,
                'battery_config' => '1x 24V/20Ah',
                'use_case_focus' => 'agir-palet',
                'features' => [
                    'tr' => [
                        'list' => [
                            'Geniş 1150 x 685 mm çatal, ağır ve geniş paletler için ekstra stabilite sağlar.',
                            'İçecek, gıda ve FMCG sektörlerinde yüklü paletlerin güvenli taşıması.',
                            '24V/20Ah Li-Ion batarya ile 5 saate kadar geniş palet transfer operasyonu.',
                            'Fırsat şarjı özelliği ile içecek dağıtım merkezlerinde yoğun palet trafiği yönetimi.',
                            '685 mm çatal açıklığı, standart 560 mm göre %22 daha geniş taban desteği sunar.',
                            'İXTİF içecek ve FMCG sektörü için özel filo desteği ve hızlı servis ağı sağlar.'
                        ],
                        'branding' => [
                            'slogan' => 'Ağır paletlerde güvenin adresi: F2 Geniş Çatal.',
                            'motto' => 'Geniş tabanla güvenli taşıma, İXTİF garantisi.',
                            'technical_summary' => 'F2 Geniş Çatal, 685 mm çatal açıklığı ve 24V Li-Ion sistemi ile içecek, gıda ve FMCG sektörlerinde ağır ve geniş paletlerin güvenli taşımasını sağlar.'
                        ]
                    ]
                ],
                'highlighted_features' => [
                    [
                        'icon' => 'expand-arrows-alt',
                        'priority' => 1,
                        'title' => ['tr' => '685 mm Geniş Çatal'],
                        'description' => ['tr' => 'Standart 560 mm çataldan %22 daha geniş, ağır paletlerde ekstra stabilite sağlar.']
                    ],
                    [
                        'icon' => 'beer',
                        'priority' => 2,
                        'title' => ['tr' => 'İçecek & FMCG Optimized'],
                        'description' => ['tr' => 'Yüklü içecek paletlerinin (su, kola, bira) güvenli ve dengeli taşıması için ideal.']
                    ],
                    [
                        'icon' => 'shield-alt',
                        'priority' => 3,
                        'title' => ['tr' => 'Ekstra Stabilite'],
                        'description' => ['tr' => 'Geniş çatal açıklığı sayesinde ağır paletlerde salınım riski minimum seviyede.']
                    ]
                ],
                'use_cases' => [
                    'tr' => [
                        'İçecek dağıtım merkezlerinde ağır palet transfer',
                        'FMCG depolarında yüklü gıda paletlerinin taşıması',
                        'Süt ürünleri perakende depolarında soğuk zincir operasyonu',
                        'Konserve ve ambalajlı gıda dağıtım merkezleri',
                        'Deterjan ve temizlik ürünleri perakende depoları',
                        'Kimyasal ürünler perakende transfer (hafif kimyasallar)'
                    ]
                ],
                'competitive_advantages' => [
                    'tr' => [
                        '685 mm geniş çatal açıklığı ile ağır paletlerde segmentindeki en yüksek stabilite',
                        'Li-Ion fırsat şarjı ile içecek dağıtım merkezlerinde yoğun vardiya desteği',
                        '%22 daha geniş taban desteği sayesinde salınım riskinde %40 azalma',
                        'İçecek ve FMCG sektörü için İXTİF özel filo desteği ve öncelikli servis',
                        'Sıfır bakım Li-Ion teknolojisi ile gıda depolarında hijyenik çalışma ortamı',
                        'İXTİF stoktan hızlı teslim ile içecek zincirleri 7 gün içinde sahaya iner'
                    ]
                ],
                'target_industries' => [
                    'tr' => [
                        'İçecek dağıtım merkezleri',
                        'FMCG perakende depoları',
                        'Süt ürünleri perakende depoları',
                        'Konserve ve ambalajlı gıda dağıtım',
                        'Deterjan ve temizlik ürünleri perakende',
                        'Kimyasal ürünler perakende (hafif)',
                        'Kağıt ve kağıt ürünleri perakende',
                        'Pet şişe geri dönüşüm merkezleri',
                        'Cam şişe içecek dağıtım',
                        'Alkollü içecek perakende depoları',
                        'Enerji içeceği dağıtım merkezleri',
                        'Meyve suyu ve nektarlar perakende',
                        'Gazlı içecek franchise depoları',
                        'Toplu tüketim gıda depoları',
                        'Otel ve restoran tedarik depoları',
                        'Kafe ve kafeterya tedarik merkezleri',
                        'Spor salonları tedarik depoları',
                        'Hastane ve okul yemekhanesi depoları',
                        'Catering şirketleri merkez depoları',
                        'Toplu yemek hizmeti depoları'
                    ]
                ],
                'faq_data' => [
                    [
                        'question' => ['tr' => 'Geniş çatal hangi palet türleri için ideal?'],
                        'answer' => ['tr' => '685 mm çatal açıklığı, ağır içecek paletleri (su, kola, bira), yüklü gıda paletleri ve geniş tabanlı endüstriyel paletler için idealdir. Standart 560 mm çataldan %22 daha geniş taban desteği sağlar.'],
                        'sort_order' => 1
                    ],
                    [
                        'question' => ['tr' => 'İçecek dağıtım merkezlerinde nasıl kullanılır?'],
                        'answer' => ['tr' => 'İçecek paletleri genellikle ağır ve yüklüdür (su, kola, bira). Geniş çatal açıklığı bu paletlerin salınım riski olmadan güvenle taşınmasını sağlar. Fırsat şarjı ile yoğun vardiyalarda kesintisiz çalışır.'],
                        'sort_order' => 2
                    ],
                    [
                        'question' => ['tr' => 'Geniş çatal dar koridorlarda manevra sorunu yaratır mı?'],
                        'answer' => ['tr' => 'F2 Geniş Çatal, 695 mm toplam genişliğe sahiptir. Standart 590 mm genişlikten sadece 105 mm daha geniştir. Çoğu perakende depoda rahatlıkla manevra yapılabilir.'],
                        'sort_order' => 3
                    ],
                    [
                        'question' => ['tr' => 'FMCG sektörü için uygun mu?'],
                        'answer' => ['tr' => 'Evet, FMCG (hızlı tüketim ürünleri) depolarında yüklü gıda paletlerinin güvenli taşıması için idealdir. Geniş taban desteği salınım riskini minimize eder.'],
                        'sort_order' => 4
                    ],
                    [
                        'question' => ['tr' => 'İçecek zincirleri için toplu teklif mevcut mu?'],
                        'answer' => ['tr' => 'İXTİF, içecek ve FMCG zincirleri için filo kiralama, ikinci el seçenekler ve operasyonel leasing paketleri sunar. Detay için 0216 755 3 555 veya info@ixtif.com adresine başvurun.'],
                        'sort_order' => 5
                    ]
                ]
            ],
            [
                'sku' => 'F2-PRO',
                'variant_type' => 'extended-battery',
                'title' => 'F2 PRO - Extended Battery (2x 24V/20Ah)',
                'fork_length' => 1150,
                'fork_width' => 560,
                'battery_config' => '2x 24V/20Ah',
                'use_case_focus' => 'yogun-vardiya',
                'features' => [
                    'tr' => [
                        'list' => [
                            'Çift batarya paketi (2x 24V/20Ah) ile 10+ saat kesintisiz çalışma.',
                            'Yoğun vardiya gerektiren perakende depolarda 7/24 operasyon desteği.',
                            'Standart 1150 x 560 mm çatal, Euro ve endüstriyel paletler için evrensel uyum.',
                            'Hızlı batarya değişim sistemi ile 60 saniyede yedek batarya takılır.',
                            'Fırsat şarjı özelliği ile batarya rotasyonu yapılarak kesintisiz çalışma.',
                            'İXTİF yoğun vardiya operasyonları için özel filo desteği ve öncelikli servis sağlar.'
                        ],
                        'branding' => [
                            'slogan' => 'Yoğun vardiyalarda durmak yok: F2 PRO ile 7/24 operasyon.',
                            'motto' => 'Çift batarya gücü, çift verimlilik - İXTİF PRO farkı.',
                            'technical_summary' => 'F2 PRO, çift 24V/20Ah Li-Ion batarya paketi ile 10+ saat kesintisiz çalışma sunar. Yoğun vardiya gerektiren perakende depolarda 7/24 operasyon desteği sağlar.'
                        ]
                    ]
                ],
                'highlighted_features' => [
                    [
                        'icon' => 'battery-full',
                        'priority' => 1,
                        'title' => ['tr' => 'Çift Batarya Paketi'],
                        'description' => ['tr' => '2x 24V/20Ah Li-Ion batarya ile 10+ saat kesintisiz çalışma, yoğun vardiyalarda kesinti yok.']
                    ],
                    [
                        'icon' => 'sync-alt',
                        'priority' => 2,
                        'title' => ['tr' => 'Hızlı Batarya Rotasyonu'],
                        'description' => ['tr' => '60 saniyede batarya değişimi, yedek bataryalarla 7/24 operasyon desteği.']
                    ],
                    [
                        'icon' => 'chart-line',
                        'priority' => 3,
                        'title' => ['tr' => 'Yoğun Vardiya Optimized'],
                        'description' => ['tr' => 'E-ticaret fulfilment ve yoğun perakende depoları için tasarlanmış extended battery çözümü.']
                    ]
                ],
                'use_cases' => [
                    'tr' => [
                        'E-ticaret perakende fulfilment merkezlerinde 7/24 sipariş hazırlama',
                        'Yoğun perakende depolarda çift vardiya operasyonu',
                        '24 saat çalışan süpermarket dağıtım merkezleri',
                        'Kargo transfer merkezlerinde gece-gündüz palet taşıma',
                        'Büyük AVM arka sahalarında yoğun malzeme transfer',
                        'FMCG dağıtım merkezlerinde kesintisiz vardiya desteği'
                    ]
                ],
                'competitive_advantages' => [
                    'tr' => [
                        'Çift 24V/20Ah batarya paketi ile segmentindeki en uzun çalışma süresi (10+ saat)',
                        'Hızlı batarya rotasyonu ile 7/24 operasyonda sıfır bekleme süresi',
                        'Fırsat şarjı + batarya rotasyonu kombinasyonu ile rakiplerden %100 daha uzun uptime',
                        'E-ticaret fulfilment ve yoğun perakende için İXTİF özel filo desteği',
                        'Sıfır bakım Li-Ion teknolojisi ile yoğun vardiya maliyetlerinde %60 tasarruf',
                        'İXTİF 7/24 mobil servis ağı ile yoğun vardiya operasyonlarında öncelikli müdahale'
                    ]
                ],
                'target_industries' => [
                    'tr' => [
                        'E-ticaret fulfilment merkezleri',
                        'Yoğun perakende depoları',
                        '24 saat süpermarket dağıtım',
                        'Kargo transfer merkezleri',
                        'Büyük AVM arka sahaları',
                        'FMCG dağıtım merkezleri',
                        '3PL lojistik perakende hizmetleri',
                        'Soğuk zincir perakende depoları',
                        'İlaç perakende dağıtım merkezleri',
                        'Elektronik perakende fulfilment',
                        'Giyim ve aksesuar e-ticaret depoları',
                        'Kozmetik perakende dağıtım',
                        'Kitap ve medya fulfilment merkezleri',
                        'Spor malzemeleri e-ticaret depoları',
                        'Oyuncak perakende dağıtım',
                        'Ev tekstil e-ticaret fulfilment',
                        'Petshop perakende dağıtım',
                        'Hırdavat e-ticaret depoları',
                        'Mobilya e-ticaret fulfilment',
                        'Gıda e-ticaret dağıtım merkezleri'
                    ]
                ],
                'faq_data' => [
                    [
                        'question' => ['tr' => 'Çift batarya paketi kaç saat çalışma süresi sağlar?'],
                        'answer' => ['tr' => '2x 24V/20Ah Li-Ion batarya paketi ile tek şarjda 10-12 saat kesintisiz çalışma süresi elde edilir. Yedek batarya rotasyonu ile 7/24 operasyon desteği sağlanır.'],
                        'sort_order' => 1
                    ],
                    [
                        'question' => ['tr' => 'Batarya rotasyonu nasıl yapılır?'],
                        'answer' => ['tr' => 'Her bir Li-Ion batarya modülü 60 saniyede çıkarılıp takılabilir. 4-6 adet yedek batarya ile rotasyon yapılarak transpalet hiç durmadan çalışır. Boş bataryalar harici şarj ünitelerinde şarj edilir.'],
                        'sort_order' => 2
                    ],
                    [
                        'question' => ['tr' => 'E-ticaret fulfilment için neden ideal?'],
                        'answer' => ['tr' => 'E-ticaret fulfilment merkezleri genellikle 7/24 çalışır. F2 PRO çift batarya paketi ile gece-gündüz kesintisiz sipariş hazırlama operasyonu desteklenir. Fırsat şarjı özelliği ile batarya rotasyonu daha esnek hale gelir.'],
                        'sort_order' => 3
                    ],
                    [
                        'question' => ['tr' => 'Çift batarya ağırlık artışı yaratır mı?'],
                        'answer' => ['tr' => 'Her bir Li-Ion batarya modülü sadece 5 kg olduğu için çift batarya paketi toplam 10 kg ekstra ağırlık getirir. 120 kg temel gövde ağırlığı ile toplam 130 kg olur, hala rakiplerden %30 daha hafiftir.'],
                        'sort_order' => 4
                    ],
                    [
                        'question' => ['tr' => 'Yoğun vardiya operasyonları için toplu teklif mevcut mu?'],
                        'answer' => ['tr' => 'İXTİF, e-ticaret fulfilment ve yoğun perakende depoları için filo kiralama, batarya rotasyon programları ve operasyonel leasing paketleri sunar. Detay için 0216 755 3 555 veya info@ixtif.com adresine başvurun.'],
                        'sort_order' => 5
                    ]
                ]
            ]
        ];

        foreach ($variants as $variant) {
            $variantId = DB::table('shop_products')->insertGetId([
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'sku' => $variant['sku'],
                'model_number' => 'F2',
                'parent_product_id' => $masterId,
                'is_master_product' => false,
                'variant_type' => $variant['variant_type'],
                'title' => json_encode(['tr' => $variant['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => \Illuminate\Support\Str::slug($variant['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => "{$variant['title']} - {$variant['use_case_focus']} sektörü için özel tasarım."], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => "<p><strong>{$variant['title']}</strong> - {$variant['use_case_focus']} odaklı F2 varyantı.</p>"], JSON_UNESCAPED_UNICODE),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => 1,
                'currency' => 'TRY',
                'stock_tracking' => 1,
                'current_stock' => 0,
                'lead_time_days' => 30,
                'weight' => ($variant['battery_config'] === '2x 24V/20Ah') ? 130 : 120,
                'dimensions' => json_encode(['length' => 1550, 'width' => ($variant['fork_width'] === 685 ? 695 : 590), 'height' => 105, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
                'technical_specs' => json_encode([
                    'capacity' => [
                        'load_capacity' => ['value' => 1500, 'unit' => 'kg'],
                    ],
                    'dimensions' => [
                        'fork_dimensions' => ['length' => $variant['fork_length'], 'width' => $variant['fork_width'], 'unit' => 'mm'],
                    ],
                    'electrical' => [
                        'battery_system' => [
                            'configuration' => $variant['battery_config'] . ' Li-Ion'
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'features' => json_encode($variant['features'], JSON_UNESCAPED_UNICODE),
                'highlighted_features' => json_encode($variant['highlighted_features'], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($variant['use_cases'], JSON_UNESCAPED_UNICODE),
                'competitive_advantages' => json_encode($variant['competitive_advantages'], JSON_UNESCAPED_UNICODE),
                'target_industries' => json_encode($variant['target_industries'], JSON_UNESCAPED_UNICODE),
                'faq_data' => json_encode($variant['faq_data'], JSON_UNESCAPED_UNICODE),
                'primary_specs' => json_encode([
                    ['label' => 'Çatal Boyutu', 'value' => "{$variant['fork_length']} x {$variant['fork_width']} mm"],
                    ['label' => 'Batarya Konfigürasyonu', 'value' => $variant['battery_config']],
                    ['label' => 'Odak Sektör', 'value' => $variant['use_case_focus']],
                ], JSON_UNESCAPED_UNICODE),
                'media_gallery' => json_encode([
                    ['type' => 'image', 'url' => "products/f2/{$variant['variant_type']}.jpg", 'is_primary' => true, 'sort_order' => 1],
                ], JSON_UNESCAPED_UNICODE),
                'warranty_info' => json_encode(['tr' => '24 Ay Tam Kapsamlı Garanti'], JSON_UNESCAPED_UNICODE),
                'tags' => json_encode(['f2', $variant['variant_type'], 'transpalet', 'li-ion', $variant['use_case_focus']], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'is_featured' => 0,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("  ✅ {$variant['title']} (ID: {$variantId})");
        }

        $this->command->info('🎉 F2 Li-Ion Transpalet Serisi (Master + 4 Variant) başarıyla eklendi!');
        $this->command->info('📊 İstatistik:');
        $this->command->info("  - Master ID: {$masterId}");
        $this->command->info('  - Child Variant sayısı: 4');
        $this->command->info('  - Toplam ürün sayısı: 5 (1 master + 4 variant)');
    }
}
