<?php

namespace Modules\Shop\Database\Seeders\V4;

use Illuminate\Database\Seeder;
use Modules\Shop\app\Models\ShopProduct;
use Modules\Shop\app\Models\ShopBrand;
use Modules\SeoManagement\app\Models\SeoSetting;

/**
 * V4 TRANSPALET SEEDER ÖRNEĞİ
 *
 * Bu dosya ChatGPT'nin üretmesi gereken DETAYLI ÖRNEK seeder'dır.
 *
 * ÖNEMLİ:
 * - TR + EN (2 dil)
 * - 8 Content Variation
 * - Primary Specs (5 alan)
 * - One-line description
 * - SEO Settings (9 robots_meta)
 * - FAQ (12-15 soru)
 * - Keywords (3 kategori)
 */
class TranspaletF4Seeder extends Seeder
{
    public function run(): void
    {
        // ShopProduct oluştur
        $product = ShopProduct::updateOrCreate(
            ['sku' => 'IXTIF-F4-1500'],
            [
                'sku' => 'IXTIF-F4-1500',

                // Başlık (TR + EN)
                'title' => json_encode([
                    'tr' => 'iXtif F4 - 1.5 Ton Li-Ion Elektrikli Transpalet',
                    'en' => 'iXtif F4 - 1.5 Ton Li-Ion Electric Pallet Truck'
                ], JSON_UNESCAPED_UNICODE),

                // Slug (auto-generate)
                'slug' => json_encode([
                    'tr' => 'ixtif-f4-15-ton-li-ion-elektrikli-transpalet',
                    'en' => 'ixtif-f4-15-ton-li-ion-electric-pallet-truck'
                ], JSON_UNESCAPED_UNICODE),

                // One-line description (Ürün kartları için - 120-150 karakter)
                'one_line_description' => json_encode([
                    'tr' => 'Hafif, güçlü ve dayanıklı lityum iyon bataryalı elektrikli transpalet. Dar koridorlar için ideal, uzun çalışma süresi.',
                    'en' => 'Lightweight, powerful and durable lithium-ion battery electric pallet truck. Ideal for narrow aisles, long operating time.'
                ], JSON_UNESCAPED_UNICODE),

                // Short description (30-50 kelime)
                'short_description' => json_encode([
                    'tr' => 'iXtif F4, 1500 kg taşıma kapasiteli, lityum iyon bataryalı elektrikli transpalet. Kompakt tasarımı sayesinde dar koridorlarda yüksek manevra kabiliyeti sunar. Entegre şarj cihazı ve uzun batarya ömrü ile kesintisiz çalışma sağlar.',
                    'en' => 'iXtif F4 is a 1500 kg capacity lithium-ion battery electric pallet truck. Its compact design provides high maneuverability in narrow aisles. Integrated charger and long battery life ensure continuous operation.'
                ], JSON_UNESCAPED_UNICODE),

                // Body (800-1200 kelime, 5 bölüm, HTML)
                'body' => json_encode([
                    'tr' => '
                        <section>
                            <h2>Depo Verimliliğinizi Artırın</h2>
                            <p>Modern depoculuk operasyonlarında zaman ve verimlilik her şeyden önemlidir. Geleneksel manuel transpaletler operatör yorgunluğuna neden olurken, elektrikli transpaletler ise ağır kurşun-asit bataryalar nedeniyle sürekli bakım gerektirmektedir. İşte tam bu noktada iXtif F4, depo operasyonlarınızda devrim yaratmak için tasarlandı.</p>
                        </section>
                        <section>
                            <h3>iXtif F4 - Lityum İyon Teknolojisi</h3>
                            <p>iXtif F4, 1500 kg taşıma kapasitesi ile orta ölçekli depo operasyonları için ideal bir çözümdür. Lityum iyon batarya teknolojisi sayesinde geleneksel kurşun-asit bataryalara göre %40 daha hafif, %30 daha uzun ömürlü ve sıfır bakım gerektirmektedir.</p>
                            <p>Kompakt gövde tasarımı ve sadece 1360 mm dönüş yarıçapı ile dar koridorlarda bile yüksek manevra kabiliyeti sunar. Opsiyonel çift denge tekerlek sistemi, yüksek yüklerle çalışırken maksimum stabilite sağlar.</p>
                        </section>
                        <section>
                            <h3>Teknik Üstünlükler</h3>
                            <p><strong>Batarya Sistemi:</strong> 24V 20Ah lityum iyon batarya, 2000+ şarj döngüsü ömrü ve 6-8 saat kesintisiz çalışma süresi sunar. Entegre şarj cihazı ile herhangi bir prize takmanız yeterli - harici şarj cihazına gerek yoktur.</p>
                            <p><strong>Performans:</strong> 6 km/h maksimum hız, 4% tırmanma kapasitesi ve hidrolik yükseltme sistemi ile yüklerinizi hızlı ve güvenli şekilde taşıyın.</p>
                            <p><strong>Ergonomi:</strong> Ayarlanabilir sürüş kolu, acil stop butonu ve otomatik fren sistemi operatör güvenliğini maksimize eder.</p>
                        </section>
                        <section>
                            <h3>Kullanım Senaryoları</h3>
                            <p>iXtif F4, e-ticaret depolarında sipariş toplama, 3PL operasyonlarında palet transferi, üretim tesislerinde hammadde taşıma ve perakende mağazalarda stok yönetimi gibi çok çeşitli uygulamalarda kullanılmaktadır.</p>
                            <p>Özellikle dar koridor depoları ve çift yönlü racking sistemleri için ideal bir çözümdür. Sessiz çalışması sayesinde gıda ve ilaç depolarında da tercih edilmektedir.</p>
                        </section>
                        <section>
                            <h3>İletişim ve Destek</h3>
                            <p>iXtif F4 hakkında daha fazla bilgi, fiyat teklifi ve canlı demo için 0216 755 3 555 numaralı telefondan bize ulaşabilirsiniz. Uzman ekibimiz, depo operasyonlarınız için en uygun çözümü sunmaktan memnuniyet duyacaktır.</p>
                        </section>
                    ',
                    'en' => '
                        <section>
                            <h2>Boost Your Warehouse Efficiency</h2>
                            <p>In modern warehousing operations, time and efficiency are paramount. Traditional manual pallet trucks cause operator fatigue, while electric pallet trucks require constant maintenance due to heavy lead-acid batteries. This is where iXtif F4 is designed to revolutionize your warehouse operations.</p>
                        </section>
                        <section>
                            <h3>iXtif F4 - Lithium-Ion Technology</h3>
                            <p>iXtif F4 is an ideal solution for medium-scale warehouse operations with a 1500 kg load capacity. Thanks to lithium-ion battery technology, it is 40% lighter, 30% longer-lasting, and requires zero maintenance compared to traditional lead-acid batteries.</p>
                            <p>Compact body design and only 1360 mm turning radius provides high maneuverability even in narrow aisles. Optional dual stabilizing wheel system ensures maximum stability when working with high loads.</p>
                        </section>
                        <section>
                            <h3>Technical Superiority</h3>
                            <p><strong>Battery System:</strong> 24V 20Ah lithium-ion battery offers 2000+ charge cycle life and 6-8 hours continuous operation. With integrated charger, just plug it into any outlet - no external charger needed.</p>
                            <p><strong>Performance:</strong> Transport your loads quickly and safely with 6 km/h maximum speed, 4% climbing capacity and hydraulic lifting system.</p>
                            <p><strong>Ergonomics:</strong> Adjustable control handle, emergency stop button and automatic brake system maximize operator safety.</p>
                        </section>
                        <section>
                            <h3>Usage Scenarios</h3>
                            <p>iXtif F4 is used in a wide variety of applications such as order picking in e-commerce warehouses, pallet transfer in 3PL operations, raw material handling in production facilities and inventory management in retail stores.</p>
                            <p>It is an ideal solution especially for narrow aisle warehouses and double-deep racking systems. Its quiet operation makes it preferred in food and pharmaceutical warehouses.</p>
                        </section>
                        <section>
                            <h3>Contact and Support</h3>
                            <p>For more information about iXtif F4, price quotation and live demo, you can contact us at +90 216 755 3 555. Our expert team will be happy to provide the most suitable solution for your warehouse operations.</p>
                        </section>
                    '
                ], JSON_UNESCAPED_UNICODE),

                'category_id' => 2, // Transpalet
                'brand_id' => ShopBrand::where('slug', 'ixtif')->first()->brand_id ?? 1,

                'is_active' => true,
                'is_featured' => false,
                'base_price' => 0.00,
                'price_on_request' => true,

                // PRIMARY SPECS (5 ZORUNLU ALAN - Transpalet için)
                'primary_specs' => json_encode([
                    'capacity' => '1500 kg',
                    'stabilizing_wheel' => 'Opsiyonel (Çift denge tekerlek)',
                    'battery' => '24V 20Ah Li-Ion',
                    'charger' => 'Entegre şarj cihazı',
                    'turning_radius' => '1360 mm'
                ], JSON_UNESCAPED_UNICODE),

                // 8 CONTENT VARIATIONS (Her özellik için 8 farklı anlatım)
                'content_variations' => json_encode([
                    'li-ion-battery' => [
                        'technical' => [
                            'tr' => '24V 20Ah lityum iyon batarya, 2000+ şarj döngüsü ömrü, BMS koruma sistemi, 6-8 saat çalışma süresi.',
                            'en' => '24V 20Ah lithium-ion battery, 2000+ charge cycle life, BMS protection system, 6-8 hours operating time.'
                        ],
                        'benefit' => [
                            'tr' => 'Tam gün kesintisiz çalış, şarj bekleme yok. Kurşun-aside göre %40 daha hafif, sıfır bakım.',
                            'en' => 'Work all day without interruption, no waiting for charging. 40% lighter than lead-acid, zero maintenance.'
                        ],
                        'slogan' => [
                            'tr' => 'Bir Şarj, Tam Gün İş!',
                            'en' => 'One Charge, Full Day Work!'
                        ],
                        'motto' => [
                            'tr' => 'Güç Hiç Bitmesin',
                            'en' => 'Power Never Ends'
                        ],
                        'short' => [
                            'tr' => '24V Li-Ion, 2000+ döngü, 6-8 saat çalışma',
                            'en' => '24V Li-Ion, 2000+ cycles, 6-8 hours operation'
                        ],
                        'long' => [
                            'tr' => '24V 20Ah kapasiteli lityum iyon batarya sistemi, geleneksel kurşun-asit bataryalara göre birçok üstünlük sunmaktadır. 2000\'den fazla şarj döngüsü ömrü ile 5 yıldan uzun kullanım sağlar. Gelişmiş BMS (Battery Management System) koruma sistemi, aşırı şarj, derin deşarj ve aşırı ısınmaya karşı bataryanızı korur. Tek şarjla 6-8 saat kesintisiz çalışma süresi, vardiyalı çalışmalarda bile şarj bekleme süresini ortadan kaldırır. Kurşun-asit bataryalara göre %40 daha hafif olması, daha az enerji tüketimi ve daha yüksek manevra kabiliyeti anlamına gelir.',
                            'en' => 'The 24V 20Ah lithium-ion battery system offers many advantages over traditional lead-acid batteries. With more than 2000 charge cycles, it provides over 5 years of use. Advanced BMS (Battery Management System) protection protects your battery against overcharge, deep discharge and overheating. Single charge provides 6-8 hours of continuous operation, eliminating charging waiting time even in shift work. Being 40% lighter than lead-acid batteries means lower energy consumption and higher maneuverability.'
                        ],
                        'comparison' => [
                            'tr' => 'Li-Ion vs Kurşun-Asit: %40 daha hafif, 3x daha uzun ömür, sıfır bakım, hızlı şarj',
                            'en' => 'Li-Ion vs Lead-Acid: 40% lighter, 3x longer life, zero maintenance, fast charging'
                        ],
                        'keywords' => [
                            'tr' => 'li-ion batarya, lityum iyon, şarj süresi, batarya ömrü, hafif batarya, BMS, enerji verimliliği',
                            'en' => 'li-ion battery, lithium ion, charge time, battery life, lightweight battery, BMS, energy efficiency'
                        ]
                    ],
                    'compact-design' => [
                        'technical' => [
                            'tr' => '1360 mm dönüş yarıçapı, 1150 mm gövde genişliği, 800 mm çatal boyu, 85 mm kaldırma yüksekliği.',
                            'en' => '1360 mm turning radius, 1150 mm body width, 800 mm fork length, 85 mm lift height.'
                        ],
                        'benefit' => [
                            'tr' => 'Dar koridorlarda bile rahatça manevra yap. Çift yönlü racking sistemlerinde ideal.',
                            'en' => 'Maneuver easily even in narrow aisles. Ideal for double-deep racking systems.'
                        ],
                        'slogan' => [
                            'tr' => 'Dar Koridorda Geniş Özgürlük',
                            'en' => 'Wide Freedom in Narrow Aisles'
                        ],
                        'motto' => [
                            'tr' => 'Her Yere Sığar',
                            'en' => 'Fits Everywhere'
                        ],
                        'short' => [
                            'tr' => 'Kompakt tasarım, dar koridor performansı',
                            'en' => 'Compact design, narrow aisle performance'
                        ],
                        'long' => [
                            'tr' => 'iXtif F4\'ün kompakt gövde tasarımı, modern depo operasyonlarının gerektirdiği dar koridor performansını mükemmel şekilde karşılar. 1360 mm dönüş yarıçapı ile standart 2400 mm genişliğindeki koridorlarda rahatça çalışabilirsiniz. 1150 mm gövde genişliği, çift yönlü racking sistemlerinde palet çekme işlemlerini kolaylaştırır. 800 mm çatal boyu, Avrupa standardı EUR paletler (800x1200 mm) için optimize edilmiştir. Hidrolik kaldırma sistemi ile 85 mm yüksekliğe kadar hassas yükseltme yapabilir, rampa ve eşik geçişlerinde zorluk yaşamazsınız.',
                            'en' => 'The compact body design of iXtif F4 perfectly meets the narrow aisle performance required by modern warehouse operations. With a 1360 mm turning radius, you can work comfortably in standard 2400 mm wide aisles. 1150 mm body width facilitates pallet picking operations in double-deep racking systems. 800 mm fork length is optimized for European standard EUR pallets (800x1200 mm). With hydraulic lifting system, you can lift precisely up to 85 mm height, you will not have difficulty in passing ramps and thresholds.'
                        ],
                        'comparison' => [
                            'tr' => 'Standart transpalet: 1800 mm dönüş. F4: 1360 mm. %25 daha kompakt!',
                            'en' => 'Standard pallet truck: 1800 mm turn. F4: 1360 mm. 25% more compact!'
                        ],
                        'keywords' => [
                            'tr' => 'dar koridor, kompakt transpalet, dönüş yarıçapı, manevra, çift yönlü racking',
                            'en' => 'narrow aisle, compact pallet truck, turning radius, maneuverability, double-deep racking'
                        ]
                    ],
                    'stabilizing-wheel' => [
                        'technical' => [
                            'tr' => 'Çift denge tekerlek sistemi (opsiyonel), endüstriyel PU malzeme, yük sensörlü otomatik devreye alma.',
                            'en' => 'Dual stabilizing wheel system (optional), industrial PU material, load sensor automatic activation.'
                        ],
                        'benefit' => [
                            'tr' => 'Yüksek yüklerde bile sallantı yok. Bozuk zeminlerde maksimum stabilite.',
                            'en' => 'No wobbling even with high loads. Maximum stability on uneven surfaces.'
                        ],
                        'slogan' => [
                            'tr' => 'Her Yük Dengede',
                            'en' => 'Every Load Balanced'
                        ],
                        'motto' => [
                            'tr' => 'Sağlam Adım',
                            'en' => 'Solid Step'
                        ],
                        'short' => [
                            'tr' => 'Opsiyonel denge tekerlek, yüksek stabilite',
                            'en' => 'Optional stabilizing wheel, high stability'
                        ],
                        'long' => [
                            'tr' => 'Opsiyonel çift denge tekerlek sistemi, yüksek tonajlı yükler taşırken veya bozuk zeminlerde çalışırken maksimum stabilite sağlar. Endüstriyel PU (poliüretan) malzeme, aşınmaya dayanıklı ve uzun ömürlüdür. Yük sensörlü sistem, yük 1000 kg\'ı aştığında denge tekerleklerini otomatik olarak devreye alır. Bu sayede operatör müdahalesi gerektirmeden her koşulda güvenli çalışma sağlanır. Özellikle pürüzlü beton, asfalt çatlakları ve eşit olmayan yüzeylerde yük dengesi ve operatör konforu için kritik öneme sahiptir.',
                            'en' => 'Optional dual stabilizing wheel system provides maximum stability when carrying high tonnage loads or working on uneven surfaces. Industrial PU (polyurethane) material is wear-resistant and long-lasting. Load sensor system automatically activates stabilizing wheels when load exceeds 1000 kg. This ensures safe operation in all conditions without operator intervention. It is critical for load balance and operator comfort, especially on rough concrete, asphalt cracks and uneven surfaces.'
                        ],
                        'comparison' => [
                            'tr' => 'Tekli vs Çiftli Denge: %60 daha stabil, sıfır sallantı, güvenli taşıma',
                            'en' => 'Single vs Dual Stabilizer: 60% more stable, zero wobbling, safe transport'
                        ],
                        'keywords' => [
                            'tr' => 'denge tekerlek, stabilizatör, yük dengesi, bozuk zemin, stabilite sistemi',
                            'en' => 'stabilizing wheel, stabilizer, load balance, uneven surface, stability system'
                        ]
                    ]
                    // ... Diğer 5-6 özellik için aynı format devam eder
                ], JSON_UNESCAPED_UNICODE),

                // Technical Specs (TR + EN)
                'technical_specs' => json_encode([
                    'tr' => [
                        'Kapasite' => '1500 kg',
                        'Batarya' => '24V 20Ah Li-Ion',
                        'Şarj Cihazı' => 'Entegre',
                        'Şarj Süresi' => '3-4 saat',
                        'Çalışma Süresi' => '6-8 saat',
                        'Maksimum Hız (Yüklü)' => '6 km/h',
                        'Maksimum Hız (Boş)' => '6.5 km/h',
                        'Tırmanma Kapasitesi' => '%4',
                        'Dönüş Yarıçapı' => '1360 mm',
                        'Gövde Genişliği' => '1150 mm',
                        'Çatal Boyu' => '800 mm',
                        'Çatal Genişliği' => '150 mm',
                        'Kaldırma Yüksekliği' => '85 mm',
                        'Ağırlık' => '285 kg',
                        'Denge Tekerlek' => 'Opsiyonel',
                        'Tekerlek Tipi' => 'PU (Poliüretan)',
                        'Fren Sistemi' => 'Otomatik elektromanyetik'
                    ],
                    'en' => [
                        'Capacity' => '1500 kg',
                        'Battery' => '24V 20Ah Li-Ion',
                        'Charger' => 'Integrated',
                        'Charging Time' => '3-4 hours',
                        'Operating Time' => '6-8 hours',
                        'Max Speed (Loaded)' => '6 km/h',
                        'Max Speed (Empty)' => '6.5 km/h',
                        'Climbing Capacity' => '4%',
                        'Turning Radius' => '1360 mm',
                        'Body Width' => '1150 mm',
                        'Fork Length' => '800 mm',
                        'Fork Width' => '150 mm',
                        'Lift Height' => '85 mm',
                        'Weight' => '285 kg',
                        'Stabilizing Wheel' => 'Optional',
                        'Wheel Type' => 'PU (Polyurethane)',
                        'Brake System' => 'Automatic electromagnetic'
                    ]
                ], JSON_UNESCAPED_UNICODE),

                // Features (TR + EN)
                'features' => json_encode([
                    'tr' => [
                        'Lityum iyon batarya teknolojisi',
                        'Entegre şarj cihazı (harici cihaz gerektirmez)',
                        'Kompakt gövde tasarımı (dar koridor performansı)',
                        'Opsiyonel çift denge tekerlek sistemi',
                        'Ayarlanabilir ergonomik sürüş kolu',
                        'Dijital batarya göstergesi',
                        'Acil stop butonu',
                        'Otomatik fren sistemi',
                        'Sessiz çalışma (60 dB)',
                        'CE sertifikalı'
                    ],
                    'en' => [
                        'Lithium-ion battery technology',
                        'Integrated charger (no external device required)',
                        'Compact body design (narrow aisle performance)',
                        'Optional dual stabilizing wheel system',
                        'Adjustable ergonomic control handle',
                        'Digital battery indicator',
                        'Emergency stop button',
                        'Automatic brake system',
                        'Quiet operation (60 dB)',
                        'CE certified'
                    ]
                ], JSON_UNESCAPED_UNICODE),

                // Use Cases (TR + EN)
                'use_cases' => json_encode([
                    'tr' => [
                        'E-ticaret depolarında sipariş toplama',
                        '3PL operasyonlarında palet transferi',
                        'Üretim tesislerinde hammadde taşıma',
                        'Perakende mağazalarda stok yönetimi',
                        'Dar koridor depolarında yükleme/boşaltma',
                        'Çift yönlü racking sistemlerinde palet çekme',
                        'Gıda depolarında soğuk hava taşıma',
                        'İlaç depolarında hassas yük taşıma'
                    ],
                    'en' => [
                        'Order picking in e-commerce warehouses',
                        'Pallet transfer in 3PL operations',
                        'Raw material handling in production facilities',
                        'Inventory management in retail stores',
                        'Loading/unloading in narrow aisle warehouses',
                        'Pallet picking in double-deep racking systems',
                        'Cold storage transport in food warehouses',
                        'Sensitive load transport in pharmaceutical warehouses'
                    ]
                ], JSON_UNESCAPED_UNICODE),

                // FAQ (12-15 soru, 4 kategori, TR + EN)
                'faq_data' => json_encode([
                    'tr' => [
                        ['category' => 'Technical', 'question' => 'Batarya ömrü ne kadar?', 'answer' => 'Lityum iyon batarya 2000+ şarj döngüsü ömrüne sahiptir. Normal kullanımda 5 yıldan uzun süre kullanılabilir.'],
                        ['category' => 'Technical', 'question' => 'Şarj süresi ne kadar?', 'answer' => '24V 20Ah batarya tam şarj süresi 3-4 saattir. %80 şarj 2 saat içinde tamamlanır.'],
                        ['category' => 'Technical', 'question' => 'Denge tekerlek ne işe yarar?', 'answer' => 'Opsiyonel denge tekerlek sistemi, 1000 kg üzeri yüklerde veya bozuk zeminlerde ekstra stabilite sağlar.'],
                        ['category' => 'Usage', 'question' => 'Hangi palet tipleriyle uyumlu?', 'answer' => 'EUR (800x1200 mm) ve endüstriyel (1000x1200 mm) paletlerle uyumludur. 800 mm çatal boyu standarttır.'],
                        ['category' => 'Usage', 'question' => 'Dar koridorlarda kullanılabilir mi?', 'answer' => 'Evet, 1360 mm dönüş yarıçapı ile 2400 mm genişliğindeki koridorlarda rahatça çalışır.'],
                        ['category' => 'Usage', 'question' => 'Soğuk hava depolarında kullanılabilir mi?', 'answer' => 'Li-Ion batarya -10°C ila +45°C sıcaklıklarda çalışır. Kısa süreli soğuk hava giriş/çıkışları için uygundur.'],
                        ['category' => 'Maintenance', 'question' => 'Bakım gerektiriyor mu?', 'answer' => 'Lityum iyon batarya sıfır bakım gerektirir. 6 ayda bir genel kontrol önerilir.'],
                        ['category' => 'Maintenance', 'question' => 'Batarya değişimi gerekir mi?', 'answer' => 'Normal kullanımda 5 yıl boyunca batarya değişimi gerekmez. Batarya performansı düştüğünde modüler değişim yapılır.'],
                        ['category' => 'Maintenance', 'question' => 'Hangi yedek parçalar gerekir?', 'answer' => 'Tekerlek, fren balatası ve çatal rulmanları kullanıma bağlı aşınır. 2-3 yılda değişim gerekebilir.'],
                        ['category' => 'Performance', 'question' => 'Maksimum hız nedir?', 'answer' => 'Yüklü halde 6 km/h, boş halde 6.5 km/h hıza ulaşır.'],
                        ['category' => 'Performance', 'question' => 'Rampalarda çalışabilir mi?', 'answer' => 'Evet, %4 tırmanma kapasitesi ile hafif eğimli rampalarda çalışabilir.'],
                        ['category' => 'Performance', 'question' => 'Tek şarjla ne kadar çalışır?', 'answer' => 'Orta yoğunluklu kullanımda 6-8 saat kesintisiz çalışır. Yoğun kullanımda 4-6 saat.'],
                        ['category' => 'Performance', 'question' => 'Gürültü seviyesi ne kadar?', 'answer' => 'Sadece 60 dB gürültü seviyesi ile sessiz çalışır. Gıda ve ilaç depoları için idealdir.']
                    ],
                    'en' => [
                        ['category' => 'Technical', 'question' => 'What is the battery life?', 'answer' => 'Lithium-ion battery has 2000+ charge cycle life. It can be used for over 5 years in normal use.'],
                        ['category' => 'Technical', 'question' => 'How long does charging take?', 'answer' => '24V 20Ah battery full charge time is 3-4 hours. 80% charge is completed within 2 hours.'],
                        ['category' => 'Technical', 'question' => 'What does the stabilizing wheel do?', 'answer' => 'Optional stabilizing wheel system provides extra stability with loads over 1000 kg or on uneven surfaces.'],
                        ['category' => 'Usage', 'question' => 'Which pallet types is it compatible with?', 'answer' => 'Compatible with EUR (800x1200 mm) and industrial (1000x1200 mm) pallets. 800 mm fork length is standard.'],
                        ['category' => 'Usage', 'question' => 'Can it be used in narrow aisles?', 'answer' => 'Yes, with a 1360 mm turning radius, it works comfortably in 2400 mm wide aisles.'],
                        ['category' => 'Usage', 'question' => 'Can it be used in cold storage?', 'answer' => 'Li-Ion battery operates at -10°C to +45°C. Suitable for short-term cold storage entry/exit.'],
                        ['category' => 'Maintenance', 'question' => 'Does it require maintenance?', 'answer' => 'Lithium-ion battery requires zero maintenance. General inspection is recommended every 6 months.'],
                        ['category' => 'Maintenance', 'question' => 'Is battery replacement required?', 'answer' => 'No battery replacement is required for 5 years under normal use. Modular replacement is done when battery performance drops.'],
                        ['category' => 'Maintenance', 'question' => 'What spare parts are needed?', 'answer' => 'Wheels, brake pads and fork bearings wear depending on usage. May need replacement in 2-3 years.'],
                        ['category' => 'Performance', 'question' => 'What is the maximum speed?', 'answer' => 'It reaches 6 km/h when loaded, 6.5 km/h when empty.'],
                        ['category' => 'Performance', 'question' => 'Can it work on ramps?', 'answer' => 'Yes, with 4% climbing capacity, it can work on slightly inclined ramps.'],
                        ['category' => 'Performance', 'question' => 'How long does it work on a single charge?', 'answer' => 'Works 6-8 hours continuously in medium-intensity use. 4-6 hours in intensive use.'],
                        ['category' => 'Performance', 'question' => 'What is the noise level?', 'answer' => 'Operates quietly with only 60 dB noise level. Ideal for food and pharmaceutical warehouses.']
                    ]
                ], JSON_UNESCAPED_UNICODE),

                // Keywords (3 kategori, TR + EN)
                'keywords' => json_encode([
                    'tr' => [
                        'primary' => ['transpalet', 'elektrikli transpalet', 'li-ion transpalet', '1.5 ton transpalet', 'lityum iyon transpalet'],
                        'synonyms' => ['palet taşıyıcı', 'elektrikli palet kaldırıcı', 'akülü transpalet', 'palet jack'],
                        'usage_jargon' => ['depo transpaleti', 'lojistik transpaleti', 'dar koridor transpaleti', 'kompakt transpalet']
                    ],
                    'en' => [
                        'primary' => ['pallet truck', 'electric pallet truck', 'li-ion pallet truck', '1.5 ton pallet truck', 'lithium-ion pallet truck'],
                        'synonyms' => ['pallet mover', 'electric pallet lifter', 'battery pallet truck', 'pallet jack'],
                        'usage_jargon' => ['warehouse pallet truck', 'logistics pallet truck', 'narrow aisle pallet truck', 'compact pallet truck']
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]
        );

        // SeoSettings oluştur (Polymorphic relationship)
        SeoSetting::updateOrCreate(
            [
                'seoable_type' => 'Modules\Shop\app\Models\ShopProduct',
                'seoable_id' => $product->product_id
            ],
            [
                'titles' => json_encode([
                    'tr' => 'iXtif F4 1.5 Ton Li-Ion Elektrikli Transpalet | iXtif',
                    'en' => 'iXtif F4 1.5 Ton Li-Ion Electric Pallet Truck | iXtif'
                ], JSON_UNESCAPED_UNICODE),

                'descriptions' => json_encode([
                    'tr' => '1500 kg kapasiteli lityum iyon bataryalı elektrikli transpalet. Kompakt tasarım, dar koridor performansı, 6-8 saat çalışma süresi. İXTİF F4 ile depo verimliliğinizi artırın.',
                    'en' => '1500 kg capacity lithium-ion battery electric pallet truck. Compact design, narrow aisle performance, 6-8 hours operating time. Boost your warehouse efficiency with iXtif F4.'
                ], JSON_UNESCAPED_UNICODE),

                'og_titles' => json_encode([
                    'tr' => 'iXtif F4 - 1.5 Ton Li-Ion Elektrikli Transpalet',
                    'en' => 'iXtif F4 - 1.5 Ton Li-Ion Electric Pallet Truck'
                ], JSON_UNESCAPED_UNICODE),

                'og_descriptions' => json_encode([
                    'tr' => 'Hafif, güçlü ve dayanıklı lityum iyon bataryalı elektrikli transpalet. Dar koridorlar için ideal, uzun çalışma süresi, sıfır bakım.',
                    'en' => 'Lightweight, powerful and durable lithium-ion battery electric pallet truck. Ideal for narrow aisles, long operating time, zero maintenance.'
                ], JSON_UNESCAPED_UNICODE),

                'og_image' => null, // Medya eklenince güncelle

                // Robots Meta (9 alan - 2025 Google best practices)
                'robots_meta' => json_encode([
                    'index' => true,
                    'follow' => true,
                    'max-snippet' => -1,
                    'max-image-preview' => 'large',
                    'max-video-preview' => -1,
                    'noarchive' => false,
                    'noimageindex' => false,
                    'notranslate' => false,
                    'indexifembedded' => true,
                    'noydir' => true,
                    'noodp' => true
                ], JSON_UNESCAPED_UNICODE),

                'schema_type' => 'Product',

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
