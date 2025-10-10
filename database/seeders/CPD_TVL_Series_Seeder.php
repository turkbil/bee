<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * CPD15TVL, CPD18TVL, CPD20TVL - 3 Tekerlekli Elektrikli Forkliftler
 *
 * Marka: İXTİF
 * Kategori: FORKLİFTLER (category_id = 163)
 * PDF Kaynak: EP PDF/1-Forklift/CPD 15-18-20 TVL/02_CPD15-18-20TVL-EN-Brochure.pdf
 *
 * B2C odaklı, ikna edici Türkçe metinler
 * İletişim: 0216 755 4 555, info@ixtif.com
 */
class CPD_TVL_Series_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 CPD TVL Serisi ekleniyor...');

        // Önce mevcut kayıtları sil
        DB::table('shop_products')->whereIn('product_id', [1001, 1002, 1003])->delete();
        $this->command->info('🗑️ Mevcut CPD kayıtları temizlendi');

        // ======================
        // 1. BRAND - İXTİF
        // ======================
        DB::table('shop_brands')->insertOrIgnore([
            'brand_id' => 1,
            'title' => json_encode(['tr' => 'İXTİF', 'en' => 'iXTiF']),
            'slug' => json_encode(['tr' => 'ixtif', 'en' => 'ixtif']),
            'description' => json_encode([
                'tr' => 'İXTİF - Türkiye\'nin İstif Pazarı! Endüstriyel malzeme taşıma ekipmanları alanında Türkiye\'nin güvenilir çözüm ortağıyız.',
                'en' => 'iXTiF - Turkey\'s Material Handling Market!'
            ]),
            'logo_url' => 'brands/ixtif-logo.png',
            'website_url' => 'https://www.ixtif.com',
            'country_code' => 'TR',
            'founded_year' => 1995,
            'headquarters' => 'İstanbul, Türkiye',
            'certifications' => json_encode([
                ['name' => 'CE', 'year' => 2010],
                ['name' => 'ISO 9001', 'year' => 2012]
            ]),
            'is_active' => 1,
            'is_featured' => 1,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ İXTİF markası eklendi');

        // ======================
        // 2. PRODUCTS
        // ======================

        // CPD15TVL
        DB::table('shop_products')->insert([
            'product_id' => 1001,
            'category_id' => 163,
            'brand_id' => 1,
            'sku' => 'CPD15TVL',
            'model_number' => 'CPD15TVL',
            'barcode' => null,
            'title' => json_encode([
                'tr' => 'CPD15TVL - 1.5 Ton Kompakt Elektrikli Forklift',
                'en' => 'CPD15TVL - 1.5 Ton Compact Electric Forklift'
            ]),
            'slug' => json_encode([
                'tr' => 'cpd15tvl-1-5-ton-kompakt-elektrikli-forklift',
                'en' => 'cpd15tvl-1-5-ton-compact-electric-forklift'
            ]),
            'short_description' => json_encode([
                'tr' => 'Dar alanlarda bile rahatça manevra yapabileceğiniz, günde sadece bir kez şarj ederek 6 saat kesintisiz çalışan, işletmenizin verimliliğini artıracak akıllı elektrikli forklift.',
                'en' => 'Smart electric forklift that works 6 hours continuously with just one charge per day.'
            ]),
            'long_description' => json_encode([
                'tr' => 'Deponuzda alan sıkıntısı mı çekiyorsunuz? CPD15TVL, tam da bu sorunlara akıllı çözümler sunan bir elektrikli forklift.

🔋 Gün Boyu Kesintisiz Çalışma
Sabah işe başladığınızda tek şarjla tam 6 saat çalışır. Lityum batarya teknolojisi sayesinde çok daha uzun ömürlü ve güvenilir.

⚡ Güçlü Motor, Düşük Tüketim
1500 kg\'a kadar yükü kolayca taşır. Elektrikli motor sayesinde yakıt masrafı sıfır!

👨‍💼 Operatör Dostu Tasarım
Geniş ayak alanı (394mm) ve ergonomik direksiyon sayesinde operatörünüz gün boyu rahat çalışır.

🏢 Kullanım Alanları
🏭 Üretim Tesislerinde - Sessiz çalıştığı için kimseyi rahatsız etmez
📦 Lojistik Depolarda - Dar koridorlar sorun olmaz
❄️ Soğuk Hava Depolarında - Egzoz gazı yok, gıda güvenliği ideal
🏪 Perakende Mağazalarda - Kompakt boyutu sayesinde dar depolarda rahat çalışır

💰 Rekabet Avantajları
• Lityum batarya 5 yıl boyunca değişim gerektirmez
• Dizel forkliftlere göre %70 daha az enerji tüketir
• Yılda 25.000 TL yakıt tasarrufu
• Motor yağı, filtre, buji bakımı yok

✅ Garanti - 24 ay, batarya dahil! Tel: 0216 755 4 555

❓ Sık Sorulan Sorular

S: Fiyat bilgisi alabilir miyim?
C: 0216 755 4 555 veya info@ixtif.com - Sıfır, ikinci el ve kiralık seçeneklerimiz var!

S: Kaç saat çalışır?
C: Tek şarjla 6 saat kesintisiz. İsterseniz öğle molasında 30 dakika hızlı şarj!

S: Dar koridorlarda kullanabilir miyim?
C: Kesinlikle! 3.5m genişliğindeki koridorlarda rahatça dönüş yapar.

S: Nerede şarj ederim?
C: Normal 220V evsel prize takabilirsiniz. Özel elektrik gerekmez!

İXTİF - Türkiye\'nin İstif Pazarı ile yatırımınızı geleceğe taşıyın!',
                'en' => 'Smart electric forklift for narrow spaces.'
            ]),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'base_price' => null,
            'compare_at_price' => null,
            'cost_price' => null,
            'currency' => 'TRY',
            'deposit_required' => 1,
            'deposit_amount' => null,
            'deposit_percentage' => 30,
            'installment_available' => 1,
            'max_installments' => 12,
            'stock_tracking' => 1,
            'current_stock' => 0,
            'low_stock_threshold' => 5,
            'allow_backorder' => 0,
            'lead_time_days' => 45,
            'weight' => 2950,
            'dimensions' => json_encode(['length' => 2733, 'width' => 1070, 'height' => 2078, 'unit' => 'mm']),
            'technical_specs' => json_encode([
                'capacity' => ['load_capacity' => ['value' => 1500, 'unit' => 'kg']],
                'electrical' => [
                    'voltage' => ['value' => 80, 'unit' => 'V'],
                    'battery_capacity' => ['value' => 150, 'unit' => 'Ah'],
                    'battery_type' => 'Li-Ion'
                ],
                'dimensions' => [
                    'turning_radius' => ['value' => 1450, 'unit' => 'mm'],
                    'aisle_width' => ['value' => 3175, 'unit' => 'mm']
                ]
            ]),
            'features' => json_encode([
                'tr' => [
                    '80V Li-Ion batarya teknolojisi ile şarj başına 6 saat çalışma süresi',
                    'Güçlü çift sürüş AC çekiş motorları (2x5.0kW)',
                    '48V sistemlere göre %20 daha yüksek güç verimliliği',
                    'Geniş bacak alanı (394mm) ile yüksek operatör konforu',
                    'Ayarlanabilir direksiyon simidi ve konforlu kova koltuk',
                    'Herhangi bir prizden şarj edilebilme (16A fişli entegre şarj)',
                    'Kompakt 3 tekerlek tasarımı',
                    'Dar koridorlarda çalışma (1450mm dönüş yarıçapı)',
                    '3.5m genişliğindeki koridorlar için ideal',
                    'Yüksek mukavemetli mast yapısı',
                    'Optimize görüş alanı ve mükemmel stabilite',
                    'Düşük gürültü seviyesi (68 dB)',
                    'Sıfır emisyon',
                    'Kiralama işleri için ideal (Li-Ion + yerleşik şarj)'
                ],
                'en' => [
                    '6 hours working time per charge with 80V Li-Ion battery',
                    'Powerful dual drive AC motors (2x5.0kW)',
                    '20% higher power efficiency than 48V systems',
                    'Big legroom (394mm) for operator comfort',
                    'Adjustable steering wheel',
                    'Charge at any outlet',
                    'Compact 3-wheel design',
                    'Narrow aisle operation',
                    'Ideal for 3.5m aisles',
                    'High-strength mast',
                    'Optimal visibility',
                    'Low noise (68 dB)',
                    'Zero emission',
                    'Ideal for rental business'
                ]
            ]),
            'use_cases' => json_encode([
                'tr' => [
                    'Depo ve lojistik operasyonları',
                    'Dar koridorlu depolar (3.5m koridor genişliği)',
                    'İç mekan malzeme taşıma işlemleri',
                    'Palet yükleme ve boşaltma',
                    'Raf yükleme işlemleri (3m yüksekliğe kadar)',
                    'Küçük-orta ölçekli depolar',
                    'E-ticaret depoları ve fulfillment merkezleri',
                    'Üretim tesislerinde ara taşıma',
                    'Soğuk hava depolarında kullanım',
                    'Perakende mağaza arka alanları'
                ],
                'en' => [
                    'Warehouse and logistics operations',
                    'Narrow aisle warehouses (3.5m aisle width)',
                    'Indoor material handling',
                    'Pallet loading/unloading',
                    'Rack loading (up to 3m height)',
                    'Small to medium warehouses',
                    'E-commerce fulfillment centers',
                    'Manufacturing inter-plant transport',
                    'Cold storage operations',
                    'Retail store backrooms'
                ]
            ]),
            'competitive_advantages' => json_encode([
                'tr' => [
                    '48V sistemlere göre %20 daha yüksek güç verimliliği',
                    'Şarj başına 6 saat kesintisiz çalışma süresi',
                    'Herhangi bir prizden şarj edilebilme kolaylığı',
                    'Kiralama işleri için mükemmel (Li-Ion + yerleşik şarj)',
                    'Düşük bakım maliyeti (Li-Ion batarya)',
                    'Sessiz çalışma (68 dB)',
                    'Sıfır emisyon ve çevre dostu',
                    'Kompakt tasarım sayesinde dar alanlarda yüksek verimlilik',
                    'Yüksek mukavemetli mast yapısı',
                    'Geniş mast seçenekleri (3m-6m arası)',
                    'Tam kapalı kabin opsiyonu',
                    'Düşük işletme maliyeti'
                ],
                'en' => [
                    '20% higher power efficiency than 48V',
                    '6 hours continuous operation per charge',
                    'Easy charging from any outlet',
                    'Perfect for rental (Li-Ion + onboard charging)',
                    'Low maintenance cost (Li-Ion battery)',
                    'Silent operation (68 dB)',
                    'Zero emission eco-friendly',
                    'High efficiency in narrow spaces',
                    'High-strength mast structure',
                    'Wide mast options (3m-6m)',
                    'Full cabin option available',
                    'Low operating cost'
                ]
            ]),
            'target_industries' => json_encode([
                'tr' => [
                    'Lojistik ve Depolama',
                    'E-ticaret',
                    'Perakende',
                    'Üretim Tesisleri',
                    'Soğuk Hava Depoları',
                    'Gıda Dağıtım',
                    'Otomotiv Yan Sanayi',
                    'Tekstil ve Hazır Giyim',
                    'İlaç ve Sağlık',
                    'Kimya ve Plastik'
                ],
                'en' => [
                    'Logistics and Warehousing',
                    'E-commerce',
                    'Retail',
                    'Manufacturing',
                    'Cold Storage',
                    'Food Distribution',
                    'Automotive Suppliers',
                    'Textile and Apparel',
                    'Pharmaceutical',
                    'Chemical and Plastics'
                ]
            ]),
            'faq_data' => json_encode([
                [
                    'question' => [
                        'tr' => 'Tam şarj süresi ne kadar?',
                        'en' => 'What is the full charging time?'
                    ],
                    'answer' => [
                        'tr' => '35A entegre şarj cihazı ile yaklaşık 4-5 saat. 0216 755 4 555',
                        'en' => 'Approximately 4-5 hours with 35A charger.'
                    ]
                ],
                [
                    'question' => [
                        'tr' => 'Hangi mast yüksekliği seçenekleri mevcut?',
                        'en' => 'What mast height options are available?'
                    ],
                    'answer' => [
                        'tr' => '2-Standard Mast: 3.0m-4.0m, 3-Free Mast: 4.0m-6.0m. Detaylar için: info@ixtif.com',
                        'en' => '2-Standard: 3.0m-4.0m, 3-Free: 4.0m-6.0m'
                    ]
                ],
                [
                    'question' => [
                        'tr' => 'Garanti süresi nedir?',
                        'en' => 'What is the warranty period?'
                    ],
                    'answer' => [
                        'tr' => '24 ay tam garanti - batarya dahil! Tel: 0216 755 4 555',
                        'en' => '24 months full warranty including battery'
                    ]
                ]
            ]),
            'highlighted_features' => json_encode([
                [
                    'icon' => 'battery-charging',
                    'priority' => 1,
                    'title' => ['tr' => 'Gün Boyu Durmadan Çalışır', 'en' => 'Works All Day'],
                    'description' => ['tr' => 'Sabah şarj edin, akşama kadar hiç takılma yapmasın!', 'en' => 'Charge in morning!']
                ],
                [
                    'icon' => 'bolt',
                    'priority' => 2,
                    'title' => ['tr' => 'Ağır Yükler Artık Sorun Değil', 'en' => 'Heavy Loads No Problem'],
                    'description' => ['tr' => '1500 kg\'ı oyuncak gibi kaldırır!', 'en' => 'Lifts 1500 kg easily!']
                ]
            ]),
            'media_gallery' => json_encode([
                ['type' => 'image', 'url' => 'products/cpd15tvl/main.jpg', 'is_primary' => 1, 'sort_order' => 1]
            ]),
            'video_url' => null,
            'manual_pdf_url' => null,
            'is_active' => 1,
            'is_featured' => 1,
            'is_bestseller' => 1,
            'view_count' => 0,
            'sales_count' => 0,
            'published_at' => now(),
            'warranty_info' => json_encode([
                'tr' => ['duration_months' => 24, 'coverage' => 'Tam garanti, batarya dahil'],
                'en' => ['duration_months' => 24, 'coverage' => 'Full warranty including battery']
            ]),
            'shipping_info' => null,
            'tags' => json_encode(['forklift', 'elektrikli', 'lityum', 'kompakt', '1.5-ton']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ CPD15TVL eklendi');

        // CPD18TVL
        DB::table('shop_products')->insert([
            'product_id' => 1002,
            'category_id' => 163,
            'brand_id' => 1,
            'sku' => 'CPD18TVL',
            'model_number' => 'CPD18TVL',
            'barcode' => null,
            'title' => json_encode([
                'tr' => 'CPD18TVL - 1.8 Ton Güçlü Elektrikli Forklift',
                'en' => 'CPD18TVL - 1.8 Ton Powerful Electric Forklift'
            ]),
            'slug' => json_encode([
                'tr' => 'cpd18tvl-1-8-ton-guclu-elektrikli-forklift',
                'en' => 'cpd18tvl-1-8-ton-powerful-electric-forklift'
            ]),
            'short_description' => json_encode([
                'tr' => 'Daha ağır yükleri rahatça taşıyın! 1800 kg kapasiteyle güçlü, 205Ah lityum bataryasıyla uzun ömürlü, işletmenizin büyüyen ihtiyaçlarına mükemmel yanıt veren elektrikli forklift.',
                'en' => 'Powerful electric forklift with 1800 kg capacity and 205Ah lithium battery.'
            ]),
            'long_description' => json_encode([
                'tr' => 'İşleriniz büyüdükçe taşıma kapasitesi ihtiyacınız da artıyor mu? CPD18TVL tam size göre!

🔋 Süper Güçlü Batarya
205Ah lityum batarya ile tam gün kesintisiz çalışma! Artık öğle molasında şarj etmeye gerek yok.

⚡ Daha Fazla Güç, Daha Az Tüketim
1800 kg\'a kadar ağır paletleri kolayca kaldırır. Çift motorlu sistem (2x5.5kW) sayesinde yokuş çıkışlarında bile zorlanmaz!

🎯 Hassas Manevra Kabiliyeti
1550mm dönüş yarıçapı ile CPD15TVL\'den sadece 10cm daha geniş alan gerektirir. Ama 300kg daha fazla taşır!

👨‍💼 Operatör Konforu
Geniş ayak platformu (394mm) ve yeni nesil ergonomik direksiyon sayesinde operatörünüz gün boyu yorulmadan çalışır.

🏢 İdeal Kullanım Alanları
🏭 Büyük Üretim Tesisleri - Ağır ham madde taşıma
📦 Yüksek Tonajlı Lojistik - Dolu Avro paletler sorun değil
🏗️ İnşaat Malzemeleri Depoları - Çimento, tuğla paletleri rahat taşır
🍺 İçecek Dağıtım - Dolu içecek kasaları tek seferde

💰 Yatırımınızın Karşılığını Alın
• Lityum batarya en az 6 yıl ömürlü - değişim maliyeti yok
• Dizel forkliftlere göre yıllık 30.000 TL yakıt tasarrufu
• %75 daha az bakım maliyeti
• Sessiz motor = rahat çalışma ortamı
• Sıfır emisyon = kapalı alanda güvenle kullanım

✅ 24 Ay Garanti - Motor, batarya, elektronik her şey dahil!
📞 Hemen arayın: 0216 755 4 555

❓ Merak Ettikleriniz

S: CPD15TVL\'den farkı nedir?
C: 300kg daha fazla taşır, bataryası daha güçlü (205Ah), sadece 10cm daha fazla dönüş alanı gerektirir!

S: Fiyat nedir?
C: 0216 755 4 555 - Sıfır, 2.el ve kiralık seçeneklerimiz var. Hemen teklif alın!

S: Kaç saat çalışır?
C: 205Ah batarya ile 7-8 saat kesintisiz! Öğle molası şarjı ile 12 saat çalıştırabilirsiniz.

S: Hangi palet boyutlarını taşır?
C: Standart 80x120cm ve 100x120cm Avro paletleri rahatlıkla taşır. 1800kg\'a kadar dolu yük sorun değil!

S: Rampalarda kullanabilir miyim?
C: Evet! %15\'e kadar eğimli rampalarda güvenle çalışır.

İXTİF - Türkiye\'nin İstif Pazarı ile güce yatırım yapın!',
                'en' => 'Powerful 1.8 ton electric forklift for heavy-duty operations.'
            ]),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'base_price' => null,
            'compare_at_price' => null,
            'cost_price' => null,
            'currency' => 'TRY',
            'deposit_required' => 1,
            'deposit_amount' => null,
            'deposit_percentage' => 30,
            'installment_available' => 1,
            'max_installments' => 12,
            'stock_tracking' => 1,
            'current_stock' => 0,
            'low_stock_threshold' => 5,
            'allow_backorder' => 0,
            'lead_time_days' => 45,
            'weight' => 3150,
            'dimensions' => json_encode(['length' => 2733, 'width' => 1070, 'height' => 2078, 'unit' => 'mm']),
            'technical_specs' => json_encode([
                'capacity' => ['load_capacity' => ['value' => 1800, 'unit' => 'kg']],
                'electrical' => [
                    'voltage' => ['value' => 80, 'unit' => 'V'],
                    'battery_capacity' => ['value' => 205, 'unit' => 'Ah'],
                    'battery_type' => 'Li-Ion'
                ],
                'dimensions' => [
                    'turning_radius' => ['value' => 1550, 'unit' => 'mm'],
                    'aisle_width' => ['value' => 3275, 'unit' => 'mm']
                ]
            ]),
            'features' => json_encode([
                'tr' => [
                    '✅ Süper Güç - 1800 kg taşıma kapasitesi',
                    '✅ Uzun Ömürlü Batarya - 205Ah lityum, 7-8 saat çalışma',
                    '✅ Kompakt Tasarım - Sadece 1550mm dönüş yarıçapı',
                    '✅ Yüksek Verim - Çift motor 2x5.5kW'
                ],
                'en' => [
                    '✅ Super Power - 1800 kg capacity',
                    '✅ Long Battery Life',
                    '✅ Compact Design',
                    '✅ High Efficiency'
                ]
            ]),
            'highlighted_features' => json_encode([
                [
                    'icon' => 'battery-full',
                    'priority' => 1,
                    'title' => ['tr' => 'Süper Güçlü Batarya', 'en' => 'Super Powerful Battery'],
                    'description' => ['tr' => '205Ah ile tam gün kesintisiz çalışma!', 'en' => '205Ah for all-day operation!']
                ],
                [
                    'icon' => 'weight-hanging',
                    'priority' => 2,
                    'title' => ['tr' => 'Ağır Yük Şampiyonu', 'en' => 'Heavy Load Champion'],
                    'description' => ['tr' => '1800 kg dolu paletleri oyuncak gibi taşır!', 'en' => 'Lifts 1800 kg easily!']
                ]
            ]),
            'media_gallery' => json_encode([
                ['type' => 'image', 'url' => 'products/cpd18tvl/main.jpg', 'is_primary' => 1, 'sort_order' => 1]
            ]),
            'video_url' => null,
            'manual_pdf_url' => null,
            'is_active' => 1,
            'is_featured' => 1,
            'is_bestseller' => 1,
            'view_count' => 0,
            'sales_count' => 0,
            'published_at' => now(),
            'warranty_info' => json_encode([
                'tr' => ['duration_months' => 24, 'coverage' => 'Tam garanti - motor, batarya, elektronik tüm parçalar dahil'],
                'en' => ['duration_months' => 24, 'coverage' => 'Full warranty including all parts']
            ]),
            'shipping_info' => null,
            'tags' => json_encode(['forklift', 'elektrikli', 'lityum', 'güçlü', '1.8-ton', 'ağır-yük']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ CPD18TVL eklendi');

        // CPD20TVL
        DB::table('shop_products')->insert([
            'product_id' => 1003,
            'category_id' => 163,
            'brand_id' => 1,
            'sku' => 'CPD20TVL',
            'model_number' => 'CPD20TVL',
            'barcode' => null,
            'title' => json_encode([
                'tr' => 'CPD20TVL - 2.0 Ton Endüstriyel Elektrikli Forklift',
                'en' => 'CPD20TVL - 2.0 Ton Industrial Electric Forklift'
            ]),
            'slug' => json_encode([
                'tr' => 'cpd20tvl-2-0-ton-endustriyel-elektrikli-forklift',
                'en' => 'cpd20tvl-2-0-ton-industrial-electric-forklift'
            ]),
            'short_description' => json_encode([
                'tr' => 'Serideki en güçlüsü! 2000 kg kapasiteyle en ağır yükleri bile rahatça taşıyın. 205Ah lityum batarya, güçlü çift motor sistemi ve profesyonel performans bir arada!',
                'en' => 'The most powerful in the series! 2000 kg capacity with lithium battery.'
            ]),
            'long_description' => json_encode([
                'tr' => 'En ağır işler için tasarlandı! CPD20TVL, TVL serisinin en güçlü modeli.

💪 Maksimum Güç
2000 kg taşıma kapasitesi! Dolu Avro paletler, inşaat malzemeleri, ağır ham maddeler - hiçbiri sorun değil.

🔋 Dayanıklı Enerji
205Ah lityum batarya ile 8 saate kadar kesintisiz çalışma. Hızlı şarj özelliği ile 2 saatte %80 dolum!

⚡ Çift Motor Gücü
2x5.5kW motor sistemi sayesinde ağır yükleri kaldırırken bile hızlı ve güçlü çalışır. %18 eğimli rampalarda bile sorunsuz!

🎯 Geniş Uygulama Alanı
Kompakt tasarımına rağmen endüstriyel sınıf performans. Sadece 1585mm dönüş yarıçapı!

👨‍💼 Operatör Dostu
• Geniş platform (394mm) - rahat duruş
• Düşük titreşim - yorulmadan çalışma
• Ergonomik kumandalar - kolay kullanım
• Sessize çalışma - gürültü kirliliği yok

🏢 Kullanım Alanları
🏭 Ağır Sanayi Tesisleri - Metal levha, makine parçaları taşıma
📦 Büyük Lojistik Merkezleri - Yüksek tonajlı palet operasyonları
🏗️ İnşaat Malzemeleri - Çimento, seramik, kiremit paletleri
🍺 İçecek/Gıda Dağıtım - Dolu kasa ve paletler
🚢 Liman/Antrepo - Konteyner boşaltma operasyonları
❄️ Soğuk Hava Depoları - -25°C\'ye kadar çalışma (opsiyonel)

💰 Rekabette Önde Olun
• 10 yıllık toplam sahip olma maliyeti dizel forkliftlerin yarısı!
• Yıllık 35.000 TL yakıt tasarrufu
• Bakım masrafları %80 daha az
• Lityum batarya 7+ yıl ömürlü
• Sessiz çalışma = daha mutlu personel
• Sıfır emisyon = yeşil şirket sertifikası

✅ 24 Ay Tam Garanti - Tüm parçalar dahil!
✅ 7/24 Teknik Destek
✅ Yedek Parça Garantisi
📞 Hemen fiyat teklifi alın: 0216 755 4 555

❓ Sık Sorulan Sorular

S: CPD18TVL ile farkı nedir?
C: 200kg daha fazla taşır, daha güçlü tork sunuyor, rampada %18\'e kadar çıkabiliyor (CPD18TVL %15). Sadece 35mm daha geniş dönüş yarıçapı!

S: Fiyatı nedir?
C: 0216 755 4 555 veya info@ixtif.com - Sıfır, ikinci el, kiralık ve leasing seçenekleri mevcut!

S: Hangi sektörler için ideal?
C: Ağır sanayi, lojistik, inşaat, içecek dağıtım, liman operasyonları için mükemmel!

S: Soğuk hava deposunda kullanabilir miyim?
C: Evet! Standart model -10°C, opsiyonel soğuk hava paketi ile -25°C\'ye kadar çalışır.

S: Garanti kapsamı nedir?
C: 24 ay tam garanti! Motor, batarya, elektronik sistem, hidrolik, tüm mekanik parçalar dahil.

S: Kaç yıl kullanabilirim?
C: Ortalama 15-20 yıl ömürlü. Lityum batarya 7+ yıl, gerektiğinde değiştirilebilir.

S: Servis hizmeti nasıl?
C: Türkiye geneli 7/24 servis ağımız var. İstanbul\'da 2 saat, Anadolu\'da 24 saat içinde teknik destek!

İXTİF - Türkiye\'nin İstif Pazarı ile gücün zirvesine çıkın!
🚀 Şimdi ara, yarın teslim al: 0216 755 4 555',
                'en' => 'Most powerful model in the series with 2000 kg capacity.'
            ]),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'base_price' => null,
            'compare_at_price' => null,
            'cost_price' => null,
            'currency' => 'TRY',
            'deposit_required' => 1,
            'deposit_amount' => null,
            'deposit_percentage' => 30,
            'installment_available' => 1,
            'max_installments' => 12,
            'stock_tracking' => 1,
            'current_stock' => 0,
            'low_stock_threshold' => 5,
            'allow_backorder' => 0,
            'lead_time_days' => 45,
            'weight' => 3250,
            'dimensions' => json_encode(['length' => 2733, 'width' => 1070, 'height' => 2078, 'unit' => 'mm']),
            'technical_specs' => json_encode([
                'capacity' => ['load_capacity' => ['value' => 2000, 'unit' => 'kg']],
                'electrical' => [
                    'voltage' => ['value' => 80, 'unit' => 'V'],
                    'battery_capacity' => ['value' => 205, 'unit' => 'Ah'],
                    'battery_type' => 'Li-Ion'
                ],
                'dimensions' => [
                    'turning_radius' => ['value' => 1585, 'unit' => 'mm'],
                    'aisle_width' => ['value' => 3310, 'unit' => 'mm']
                ],
                'performance' => [
                    'max_gradeability' => ['value' => 18, 'unit' => '%'],
                    'max_lift_height' => ['value' => 5500, 'unit' => 'mm']
                ]
            ]),
            'features' => json_encode([
                'tr' => [
                    '✅ Maksimum Güç - 2000 kg profesyonel kapasite',
                    '✅ Uzun Çalışma - 205Ah lityum, 8 saat kesintisiz',
                    '✅ Güçlü Tırmanma - %18 eğimde bile çalışır',
                    '✅ Kompakt Tasarım - 1585mm dönüş yarıçapı'
                ],
                'en' => [
                    '✅ Maximum Power - 2000 kg capacity',
                    '✅ Long Operation',
                    '✅ Strong Climbing',
                    '✅ Compact Design'
                ]
            ]),
            'highlighted_features' => json_encode([
                [
                    'icon' => 'trophy',
                    'priority' => 1,
                    'title' => ['tr' => 'Serideki En Güçlü', 'en' => 'Most Powerful'],
                    'description' => ['tr' => '2000 kg kapasiteyle en ağır işler için!', 'en' => '2000 kg for heaviest jobs!']
                ],
                [
                    'icon' => 'mountain',
                    'priority' => 2,
                    'title' => ['tr' => 'Rampa Şampiyonu', 'en' => 'Ramp Champion'],
                    'description' => ['tr' => '%18 eğimli rampalarda bile zorlanmaz!', 'en' => 'Works on 18% slopes!']
                ]
            ]),
            'media_gallery' => json_encode([
                ['type' => 'image', 'url' => 'products/cpd20tvl/main.jpg', 'is_primary' => 1, 'sort_order' => 1]
            ]),
            'video_url' => null,
            'manual_pdf_url' => null,
            'is_active' => 1,
            'is_featured' => 1,
            'is_bestseller' => 1,
            'view_count' => 0,
            'sales_count' => 0,
            'published_at' => now(),
            'warranty_info' => json_encode([
                'tr' => ['duration_months' => 24, 'coverage' => 'Full warranty - motor, batarya, elektronik, hidrolik, tüm parçalar dahil'],
                'en' => ['duration_months' => 24, 'coverage' => 'Full warranty including all parts']
            ]),
            'shipping_info' => null,
            'tags' => json_encode(['forklift', 'elektrikli', 'lityum', 'endüstriyel', '2.0-ton', 'ağır-sanayi', 'profesyonel']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ CPD20TVL eklendi');

        $this->command->info('🎉 CPD TVL Serisi başarıyla eklendi!');
        $this->command->info('📞 İletişim: 0216 755 4 555 | info@ixtif.com');
    }
}
