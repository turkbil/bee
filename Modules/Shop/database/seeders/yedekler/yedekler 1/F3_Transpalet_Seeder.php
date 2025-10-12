<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F3 - 1.5 Ton 24V Li-Ion Transpalet (Lojistik Merkezleri Master + Variants)
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F3/F3-EN-Brochure.pdf
 * Marka: İXTİF (brand_id = 1)
 * Kategori: TRANSPALETLER (category_id = 165)
 *
 * Yapı: 1 Master + 4 Child Variant (lojistik odaklı konfigürasyonlar)
 */
class F3_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F3 Li-Ion Transpalet (Master + Variants) ekleniyor...');

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
            ->where('sku', 'LIKE', 'F3-%')
            ->pluck('product_id');

        if ($existingProducts->isNotEmpty()) {
            DB::table('shop_products')->whereIn('product_id', $existingProducts)->delete();
            $this->command->info('🧹 Eski F3 kayıtları temizlendi (' . $existingProducts->count() . ' ürün)');
        }

        // ============================================================
        // MASTER PRODUCT - F3 Transpalet Serisi
        // ============================================================
        $masterId = DB::table('shop_products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => 'F3-MASTER',
            'model_number' => 'F3',
            'parent_product_id' => null,
            'is_master_product' => true,
            'title' => json_encode(['tr' => 'F3 Elektrikli Transpalet 1.5T - Lojistik Merkezleri Serisi'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f3-elektrikli-transpalet-1-5t-lojistik'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'F3 Li-Ion transpalet serisi; lojistik merkezleri ve 3PL operasyonları için güçlü çerçeve, flip cover batarya koruması ve dayanıklı yapı ile yoğun kullanım çözümüdür.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <h2>Lojistik Merkezlerinin Güç İstasyonu</h2>
    <p><strong>F3 transpalet serisini lojistik merkezinize soktuğunuz anda operatörleriniz "Bu cihaz ne kadar dayanıklı!" diye şaşıracak.</strong> Güçlü çerçeve yapısı ve flip cover batarya koruması ile 24V/20Ah Li-Ion batarya sistemi yoğun lojistik operasyonlarında durmak bilmiyor.</p>
    <p>İXTİF, F3'ü EPL153(1) modelinin kanıtlanmış dayanıklılığı üzerine kurarak lojistik sektöründe güçlü, güvenli ve sıfır bakım gerektiren bir çözüm yarattı. Platform tasarımı ile nakliye maliyetlerinde %30-40 tasarruf sağlar.</p>
</section>
<section class="marketing-body">
    <h3>Flip Cover Batarya Koruması</h3>
    <ul>
        <li><strong>Su Koruması</strong> – Flip cover tasarımı, bataryayı su sızıntısından korur.</li>
        <li><strong>Zorlu Koşullar</strong> – Lojistik merkezlerinin tozlu ve ıslak ortamlarında güvenle çalışır.</li>
        <li><strong>Hızlı Erişim</strong> – Kapaklı tasarım, batarya değişimini kolaylaştırır.</li>
    </ul>
    <h3>Güçlü Çerçeve Yapısı</h3>
    <p>Yoğun lojistik kullanımı için tasarlanmış güçlü çerçeve, ağır yüklerde bile deformasyon yapmaz. 3PL operasyonlarında uzun ömür garantisi.</p>
    <h3>İXTİF Lojistik Desteği</h3>
    <p>Lojistik zincirleri için filo kiralama, yedek batarya modülleri, ikinci el seçenekler ve 7/24 servis hattı: <strong>0216 755 3 555</strong> | <strong>info@ixtif.com</strong></p>
    <p><strong>SEO Anahtar Kelimeleri:</strong> F3 transpalet, lojistik transpalet, 3PL transpalet, flip cover transpalet, dayanıklı transpalet, İXTİF lojistik çözümleri.</p>
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
                        'configuration' => '24V/20Ah Li-Ion plug&play modül (flip cover koruması)'
                    ],
                    'battery_weight' => ['value' => 5, 'unit' => 'kg'],
                    'battery_protection' => 'Flip cover water protection',
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
                        'F3 transpalet serisi flip cover batarya koruması ile lojistik merkezlerinde güvenli operasyon sağlar.',
                        '120 kg güçlü çerçeve yapısı, yoğun 3PL kullanımında dayanıklılık sunar.',
                        'Plug&Play Li-Ion batarya sistemi, 60 saniyede hızlı değişim sağlar.',
                        'Platform tasarımı ile nakliye maliyetlerinde %30-40 tasarruf.',
                        'Ergonomik el ayası timoni, uzun vardiyalarda operatör konforunu artırır.',
                        'İXTİF stoktan hızlı teslim, lojistik zincirleri için toplu filo desteği sunar.'
                    ],
                    'branding' => [
                        'slogan' => 'Lojistikte dayanıklılık, sahada güven: F3 ile zorlu koşullara hazır olun.',
                        'motto' => 'İXTİF farkı ile lojistik merkezleri güçlenir.',
                        'technical_summary' => 'F3, 24V Li-Ion güç platformu, flip cover batarya koruması ve güçlü çerçeve yapısıyla lojistik merkezlerinde yoğun kullanımda dayanıklılık ve güvenlik sunar.'
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE),
            'highlighted_features' => json_encode([
                [
                    'icon' => 'shield-alt',
                    'priority' => 1,
                    'title' => ['tr' => 'Flip Cover Koruma'],
                    'description' => ['tr' => 'Kapaklı batarya tasarımı su sızıntısından korur, lojistik merkezlerinin zorlu koşullarında güvenle çalışır.']
                ],
                [
                    'icon' => 'industry',
                    'priority' => 2,
                    'title' => ['tr' => 'Güçlü Çerçeve'],
                    'description' => ['tr' => 'Yoğun lojistik kullanımı için tasarlanmış dayanıklı çerçeve yapısı, ağır yüklerde deformasyon yapmaz.']
                ],
                [
                    'icon' => 'plug',
                    'priority' => 3,
                    'title' => ['tr' => 'Plug&Play Batarya'],
                    'description' => ['tr' => '60 saniyede batarya değişimi, yoğun vardiyalarda kesintisiz operasyon desteği.']
                ],
                [
                    'icon' => 'truck-loading',
                    'priority' => 4,
                    'title' => ['tr' => 'Platform Tasarımı'],
                    'description' => ['tr' => 'Nakliye maliyetlerinde %30-40 tasarruf sağlayan kompakt platform yapısı.']
                ]
            ], JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode([
                'tr' => [
                    'Lojistik merkezlerinde yoğun sipariş hazırlama ve sevkiyat',
                    '3PL operasyonlarında çift vardiya palet taşıma',
                    'E-ticaret fulfilment merkezlerinde kesintisiz operasyon',
                    'Soğuk zincir lojistiğinde ıslak ve zorlu ortam çalışması',
                    'Dağıtım merkezlerinde yüksek palet trafiği yönetimi',
                    'Liman içi yük transfer operasyonları',
                    'Kargo transfer merkezlerinde gece-gündüz palet taşıma',
                    'Endüstriyel üretim tesislerinde hammadde ve mamul taşıma'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode([
                'tr' => [
                    'Flip cover batarya koruması ile segmentindeki en güvenli su sızıntı önleme',
                    'Güçlü çerçeve yapısı sayesinde yoğun lojistik kullanımında %50 daha uzun ömür',
                    'Plug&Play Li-Ion batarya sistemi ile 60 saniyede batarya değişimi',
                    'Platform tasarımı ile nakliye maliyetlerinde %30-40 tasarruf',
                    'İXTİF stoktan hızlı teslim ve lojistik zincirleri için toplu filo desteği',
                    'İXTİF ikinci el, kiralık ve yedek batarya modül programları ile esneklik',
                    'Türkiye geneli 7/24 mobil servis ağı ile lojistik operasyonlarına öncelikli destek'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode([
                'tr' => [
                    'Lojistik merkezleri ve dağıtım şirketleri',
                    '3PL lojistik hizmet sağlayıcıları',
                    'E-ticaret fulfilment merkezleri',
                    'Soğuk zincir lojistik depoları',
                    'Kargo ve kurye transfer merkezleri',
                    'Liman içi yük transfer operasyonları',
                    'Havaalanı kargo terminalleri',
                    'Endüstriyel üretim tesisleri lojistik',
                    'FMCG dağıtım merkezleri',
                    'İçecek ve gıda dağıtım şirketleri',
                    'Otomotiv yedek parça lojistiği',
                    'İlaç ve sağlık ürünleri dağıtım',
                    'Elektronik ürünler lojistik',
                    'Tekstil ve giyim dağıtım merkezleri',
                    'Mobilya ve beyaz eşya lojistik',
                    'Yerel ve bölgesel dağıtım şirketleri',
                    'Cross-dock operasyon merkezleri',
                    'Depolama ve stok yönetim şirketleri',
                    'Toptan ticaret lojistik depoları',
                    'İthalat-ihracat lojistik firmaları',
                    'Entegre lojistik çözüm sağlayıcıları',
                    'Depo outsourcing hizmet şirketleri'
                ]
            ], JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode([
                [
                    'question' => ['tr' => 'F3 transpalet bir vardiyada kaç saat çalışır?'],
                    'answer' => ['tr' => '24V/20Ah Li-Ion batarya ile tek şarjda 5-6 saat kesintisiz çalışır. Plug&Play batarya sistemi sayesinde 60 saniyede batarya değişimi yaparak çift vardiya çalışma desteklenir.'],
                    'sort_order' => 1
                ],
                [
                    'question' => ['tr' => 'Flip cover batarya koruması nasıl çalışır?'],
                    'answer' => ['tr' => 'Kapaklı batarya bölmesi tasarımı, bataryayı üstten su sızıntısına karşı korur. Lojistik merkezlerinin ıslak ve tozlu ortamlarında bataryanın zarar görmesini engeller. Flip cover açılarak batarya kolayca değiştirilebilir.'],
                    'sort_order' => 2
                ],
                [
                    'question' => ['tr' => 'Lojistik merkezleri için neden ideal?'],
                    'answer' => ['tr' => 'F3, güçlü çerçeve yapısı ve flip cover koruması ile yoğun lojistik kullanımına dayanıklıdır. Platform tasarımı nakliye maliyetlerinde %30-40 tasarruf sağlar. Plug&Play batarya sistemi ile kesintisiz çalışma sunar.'],
                    'sort_order' => 3
                ],
                [
                    'question' => ['tr' => 'Platform tasarımı nasıl nakliye tasarrufu sağlar?'],
                    'answer' => ['tr' => 'Kompakt platform yapısı sayesinde konteyner ve kamyon yükleme alanında daha fazla ünite yerleştirilebilir. Bu sayede lojistik maliyetlerinde %30-40 oranında tasarruf sağlanır.'],
                    'sort_order' => 4
                ],
                [
                    'question' => ['tr' => 'Batarya ömrü ne kadar, garanti kapsamında mı?'],
                    'answer' => ['tr' => 'Li-Ion batarya 2000+ döngü ömrü sunar, yani günde 1-2 şarj ile yaklaşık 3-5 yıl kullanılabilir. İXTİF 24 ay tam kapsamlı garanti verir; batarya, motor, elektronik ve şasi bu kapsamdadır.'],
                    'sort_order' => 5
                ],
                [
                    'question' => ['tr' => 'Farklı çatal ölçüleri mevcut mu?'],
                    'answer' => ['tr' => 'Standart 1150 x 560 mm çatal dışında 900 mm, 1000 mm, 1500 mm uzunluklar ve 560/685 mm açıklık kombinasyonları sunulur. Lojistik merkezlerinin farklı palet ihtiyaçları için uygun seçenekler mevcuttur.'],
                    'sort_order' => 6
                ],
                [
                    'question' => ['tr' => 'Kiralık veya ikinci el seçenekleri var mı?'],
                    'answer' => ['tr' => 'İXTİF, F3 için kısa/uzun dönem kiralama, operasyonel leasing ve stoktan ikinci el seçenekler sunar. Lojistik filolarına özel batarya modül ve bakım paketli teklifler hazırlanır. Detay için 0216 755 3 555 numarasını arayabilirsiniz.'],
                    'sort_order' => 7
                ],
                [
                    'question' => ['tr' => 'Servis ve yedek parça desteği nasıl?'],
                    'answer' => ['tr' => 'İXTİF Türkiye genelinde mobil servis ekipleri ile 7/24 destek sağlar. Lojistik operasyonları için öncelikli müdahale programı mevcuttur. Yedek parça ve batarya modül stoğu İstanbul merkezde tutulur, ertesi gün kargo ile gönderilir.'],
                    'sort_order' => 8
                ],
                [
                    'question' => ['tr' => 'Operatör eğitimi veriliyor mu?'],
                    'answer' => ['tr' => 'Evet, İXTİF uzman ekibi cihazı sahada devreye alır ve operatörlerin güvenli kullanımı için yerinde eğitim seti verir. Lojistik personelinin hızlı adaptasyonu için görsel kullanım kılavuzu da sunulur.'],
                    'sort_order' => 9
                ],
                [
                    'question' => ['tr' => 'Teklif nasıl alabilirim?'],
                    'answer' => ['tr' => 'F3 transpalet için İXTİF ile iletişime geçin: 0216 755 3 555 veya info@ixtif.com adresine yazın. Filo büyüklüğünüze göre özel fiyat, kiralama veya ikinci el seçenekleri ile detaylı teklif sunulur.'],
                    'sort_order' => 10
                ],
                [
                    'question' => ['tr' => 'F3 teknik slogan ve mottosu nedir?'],
                    'answer' => ['tr' => 'Slogan: "Lojistikte dayanıklılık, sahada güven." Motto: "İXTİF farkı ile lojistik merkezleri güçlenir." Bu mesajlar ürün sayfasında ayrı kartlarda vurgulanır.'],
                    'sort_order' => 11
                ]
            ], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '1.5 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 24V/20Ah'],
                ['label' => 'Batarya Koruması', 'value' => 'Flip Cover'],
                ['label' => 'Sektör', 'value' => 'Lojistik/3PL'],
                ['label' => 'Platform Tasarımı', 'value' => 'Nakliye Tasarrufu']
            ], JSON_UNESCAPED_UNICODE),
            'media_gallery' => json_encode([
                ['type' => 'image', 'url' => 'products/f3/main.jpg', 'is_primary' => true, 'sort_order' => 1],
                ['type' => 'image', 'url' => 'products/f3/flip-cover.jpg', 'is_primary' => false, 'sort_order' => 2],
                ['type' => 'image', 'url' => 'products/f3/logistics.jpg', 'is_primary' => false, 'sort_order' => 3],
            ], JSON_UNESCAPED_UNICODE),
            'warranty_info' => json_encode(['tr' => '24 Ay Tam Kapsamlı Garanti | Li-Ion Batarya Dahil'], JSON_UNESCAPED_UNICODE),
            'tags' => json_encode(['f3', 'transpalet', 'li-ion', 'lojistik', '3pl', 'flip-cover', 'dayanikli', 'ixtif'], JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ F3 MASTER eklendi (ID: {$masterId})");

        // ============================================================
        // CHILD VARIANTS - F3 Varyantları
        // ============================================================
        $variants = [
            [
                'sku' => 'F3-STD',
                'variant_type' => 'standart-lojistik',
                'title' => 'F3 - Standart Lojistik (1150x560 mm)',
                'fork_length' => 1150,
                'fork_width' => 560,
                'battery_config' => '1x 24V/20Ah',
                'use_case_focus' => 'lojistik-merkezleri',
                'features' => [
                    'tr' => [
                        'list' => [
                            'Standart 1150 x 560 mm çatal, lojistik merkezlerinde Euro ve endüstriyel palet için ideal.',
                            '24V/20Ah Li-Ion batarya ile 5 saate kadar yoğun lojistik operasyonu.',
                            'Flip cover batarya koruması ile ıslak ve tozlu ortamlarda güvenli çalışma.',
                            'Güçlü çerçeve yapısı, 3PL operasyonlarında uzun ömür garantisi.',
                            '120 kg dayanıklı gövde, yoğun kullanımda deformasyon yapmaz.',
                            'İXTİF stoktan hızlı teslim, lojistik zincirleri için toplu filo desteği.'
                        ],
                        'branding' => [
                            'slogan' => 'Lojistik merkezlerinin güvenilir yardımcısı: F3 Standart.',
                            'motto' => 'Euro palet taşımada İXTİF dayanıklılığı.',
                            'technical_summary' => 'F3 Standart, 1150 mm çatal uzunluğu, flip cover koruması ve güçlü çerçeve ile lojistik merkezlerinde yoğun palet taşımasında dayanıklılık sunar.'
                        ]
                    ]
                ],
                'highlighted_features' => [
                    [
                        'icon' => 'warehouse',
                        'priority' => 1,
                        'title' => ['tr' => 'Lojistik Optimized'],
                        'description' => ['tr' => '1150 mm çatal lojistik merkezlerinde Euro ve endüstriyel paletler için mükemmel uyum sağlar.']
                    ],
                    [
                        'icon' => 'shield-alt',
                        'priority' => 2,
                        'title' => ['tr' => 'Flip Cover Koruma'],
                        'description' => ['tr' => 'Kapaklı batarya tasarımı ıslak ve tozlu lojistik ortamlarında bataryayı korur.']
                    ],
                    [
                        'icon' => 'hard-hat',
                        'priority' => 3,
                        'title' => ['tr' => 'Dayanıklı Yapı'],
                        'description' => ['tr' => 'Güçlü çerçeve 3PL yoğun kullanımında uzun ömür sağlar.']
                    ]
                ],
                'use_cases' => [
                    'tr' => [
                        'Lojistik merkezlerinde sipariş hazırlama ve sevkiyat',
                        '3PL operasyonlarında Euro palet transfer',
                        'E-ticaret fulfilment depolarında yoğun palet trafiği',
                        'Dağıtım merkezlerinde gece vardiyası palet taşıma',
                        'Soğuk zincir lojistiğinde ıslak ortam operasyonu',
                        'Kargo transfer merkezlerinde cross-dock işlemleri'
                    ]
                ],
                'competitive_advantages' => [
                    'tr' => [
                        '1150 mm çatal uzunluğu ile lojistik merkezlerinde standart palet taşıma çözümü',
                        'Flip cover koruması ile ıslak ortamlarda %80 daha güvenli batarya kullanımı',
                        'Güçlü çerçeve yapısı sayesinde yoğun kullanımda %50 daha uzun ömür',
                        'Plug&Play batarya ile 60 saniyede değişim, rakiplerden %70 daha hızlı',
                        'İXTİF stoktan hızlı teslim ile lojistik zincirleri 7 gün içinde sahaya iner',
                        'Platform tasarımı ile nakliye maliyetlerinde %30-40 tasarruf'
                    ]
                ],
                'target_industries' => [
                    'tr' => [
                        'Lojistik merkezleri ve dağıtım şirketleri',
                        '3PL lojistik hizmet sağlayıcıları',
                        'E-ticaret fulfilment merkezleri',
                        'Kargo ve kurye transfer merkezleri',
                        'FMCG dağıtım merkezleri',
                        'Soğuk zincir lojistik depoları',
                        'Cross-dock operasyon merkezleri',
                        'Liman içi yük transfer',
                        'Havaalanı kargo terminalleri',
                        'Endüstriyel üretim lojistik',
                        'Otomotiv yedek parça lojistiği',
                        'İlaç dağıtım merkezleri',
                        'Elektronik ürünler lojistik',
                        'Tekstil dağıtım merkezleri',
                        'Mobilya lojistik depoları',
                        'Yerel dağıtım şirketleri',
                        'Depolama hizmet şirketleri',
                        'Toptan ticaret lojistik',
                        'İthalat-ihracat lojistik',
                        'Entegre lojistik çözüm sağlayıcıları'
                    ]
                ],
                'faq_data' => [
                    [
                        'question' => ['tr' => 'Standart çatal lojistik merkezlerinde hangi paletlere uygun?'],
                        'answer' => ['tr' => '1150 x 560 mm çatal, Euro palet (800x1200 mm) ve endüstriyel palet (1000x1200 mm) için idealdir. Lojistik merkezlerinde %95 oranında kullanılan standart palet ölçüleridir.'],
                        'sort_order' => 1
                    ],
                    [
                        'question' => ['tr' => 'Lojistik merkezinde bir vardiyada kaç palet taşınır?'],
                        'answer' => ['tr' => 'Tek şarjda 5 saat çalışma ile ortalama 80-100 palet transfer yapılabilir. Plug&Play batarya değişimi ile vardiya boyunca kesintisiz çalışma sağlanır.'],
                        'sort_order' => 2
                    ],
                    [
                        'question' => ['tr' => 'Flip cover koruması ıslak ortamlarda nasıl çalışır?'],
                        'answer' => ['tr' => 'Kapaklı batarya bölmesi üstten gelen su sızıntısına karşı korur. Soğuk zincir ve ıslak lojistik ortamlarında bataryanın zarar görmesini engeller.'],
                        'sort_order' => 3
                    ],
                    [
                        'question' => ['tr' => 'Batarya değişimi ne kadar sürer?'],
                        'answer' => ['tr' => 'Plug&Play sistem sayesinde batarya değişimi 60 saniyeden kısa sürer. Flip cover açılır, batarya çıkarılır, yeni batarya takılır ve kapak kapatılır.'],
                        'sort_order' => 4
                    ],
                    [
                        'question' => ['tr' => 'Lojistik zincirleri için toplu teklif mevcut mu?'],
                        'answer' => ['tr' => 'İXTİF, 10+ ünite sipariş veren lojistik zincirleri için özel fiyat, filo kiralama ve operasyonel leasing paketleri sunar. Detay için 0216 755 3 555 numarasını arayın.'],
                        'sort_order' => 5
                    ]
                ]
            ],
        ];

        foreach ($variants as $variant) {
            $variantId = DB::table('shop_products')->insertGetId([
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'sku' => $variant['sku'],
                'model_number' => 'F3',
                'parent_product_id' => $masterId,
                'is_master_product' => false,
                'variant_type' => $variant['variant_type'],
                'title' => json_encode(['tr' => $variant['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => \Illuminate\Support\Str::slug($variant['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => "{$variant['title']} - {$variant['use_case_focus']} sektörü için özel tasarım."], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => "<p><strong>{$variant['title']}</strong> - {$variant['use_case_focus']} odaklı F3 varyantı.</p>"], JSON_UNESCAPED_UNICODE),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => 1,
                'currency' => 'TRY',
                'stock_tracking' => 1,
                'current_stock' => 0,
                'lead_time_days' => 30,
                'weight' => 120,
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
                            'configuration' => $variant['battery_config'] . ' Li-Ion + Flip Cover'
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
                    ['label' => 'Özel Özellik', 'value' => 'Flip Cover Koruma'],
                ], JSON_UNESCAPED_UNICODE),
                'media_gallery' => json_encode([
                    ['type' => 'image', 'url' => "products/f3/{$variant['variant_type']}.jpg", 'is_primary' => true, 'sort_order' => 1],
                ], JSON_UNESCAPED_UNICODE),
                'warranty_info' => json_encode(['tr' => '24 Ay Tam Kapsamlı Garanti'], JSON_UNESCAPED_UNICODE),
                'tags' => json_encode(['f3', $variant['variant_type'], 'transpalet', 'li-ion', 'flip-cover', $variant['use_case_focus']], JSON_UNESCAPED_UNICODE),
                'is_active' => 1,
                'is_featured' => 0,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("  ✅ {$variant['title']} (ID: {$variantId})");
        }

        $this->command->info('🎉 F3 Li-Ion Transpalet Serisi (Master + 1 Variant) başarıyla eklendi!');
        $this->command->info('📊 İstatistik:');
        $this->command->info("  - Master ID: {$masterId}");
        $this->command->info('  - Child Variant sayısı: 1');
        $this->command->info('  - Toplam ürün sayısı: 2 (1 master + 1 variant)');
        $this->command->info('📝 Not: F3 için 1 varyant örneği oluşturuldu. Gerekirse daha fazla varyant eklenebilir.');
    }
}
