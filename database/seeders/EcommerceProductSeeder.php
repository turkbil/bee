<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommerceProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // CPD15TVL
            [
                'id' => 1,
                'tenant_id' => 1,
                'category_id' => 11, // CPD Series
                'brand_id' => 1, // iXtif
                'sku' => 'CPD15TVL',
                'model_number' => 'CPD15TVL',
                'series_name' => 'CPD TVL',
                'name' => json_encode([
                    'tr' => 'CPD15TVL Elektrikli 3 Tekerlekli Forklift',
                    'en' => 'CPD15TVL Electric 3-Wheel Counterbalance Forklift'
                ]),
                'slug' => 'cpd15tvl-elektrikli-forklift',
                'short_description' => json_encode([
                    'tr' => '1500kg kapasiteli kompakt 80V Li-ion teknolojili 3 tekerlekli elektrikli forklift',
                    'en' => '1500kg capacity compact 3-wheel electric forklift with 80V Li-ion technology'
                ]),
                'body' => json_encode([
                    'tr' => '80V Li-ion teknolojisi ile donatılmış kompakt 3 tekerlekli forklift. Güçlü dual drive AC traction motorlar, geniş operatör alanı (394mm), ayarlanabilir direksiyon ve entegre şarj sistemi ile öne çıkar. Dar koridor kullanımına ideal.',
                    'en' => 'Compact 3 wheels truck designed around 80V Li-Ion battery. Powerful dual drive AC traction motors, big legroom (394mm) for higher operator comfort, great ergonomic design with adjustable steering wheel and single phase integrated charger.'
                ]),
                'features' => json_encode([
                    'tr' => [
                        '80V Li-ion teknolojisi',
                        'Kompakt 3 tekerlek tasarımı',
                        'Güçlü dual drive AC traction motorlar',
                        'Geniş operatör alanı (394mm)',
                        'Ergonomik tasarım ile ayarlanabilir direksiyon',
                        'Entegre tek fazlı şarj cihazı (35A)',
                        'Geliştirilmiş mast ile optimal görüş',
                        'OPS (Operatör Varlık) sistemi',
                        'LED ön lamba',
                        'Hidrolik direksiyon',
                        'Dar koridor kullanımına uygun'
                    ],
                    'en' => [
                        '80V Li-ion technology',
                        'Compact 3-wheel design',
                        'Powerful dual drive AC traction motors',
                        'Big legroom (394mm) for higher operator comfort',
                        'Great ergonomic design with adjustable steering wheel',
                        'Single phase integrated charger (35A)',
                        'Enhanced mast with optimal visibility and stability',
                        'OPS (Operator Presence System)',
                        'LED front lamp',
                        'Hydraulic steering',
                        'Perfect for narrow aisles within 3.5m'
                    ]
                ]),
                'technical_specs' => json_encode([
                    'capacity' => ['value' => 1500, 'unit' => 'kg'],
                    'load_center_distance' => ['value' => 500, 'unit' => 'mm'],
                    'service_weight' => ['value' => 2950, 'unit' => 'kg'],
                    'lift_height' => ['value' => 3000, 'unit' => 'mm'],
                    'turning_radius' => ['value' => 1450, 'unit' => 'mm'],
                    'battery_voltage' => ['value' => 80, 'unit' => 'V'],
                    'battery_capacity' => ['value' => 150, 'unit' => 'Ah'],
                    'drive_motor_rating' => ['value' => '2x5.0', 'unit' => 'kW'],
                    'travel_speed_laden' => ['value' => 13, 'unit' => 'km/h'],
                    'sound_pressure_level' => ['value' => 68, 'unit' => 'dB(A)']
                ]),
                'highlighted_features' => json_encode([
                    'capacity' => [
                        'icon' => 'fa-solid fa-weight-hanging',
                        'value' => 1500,
                        'unit' => 'kg',
                        'name' => ['tr' => 'Yük Kapasitesi', 'en' => 'Load Capacity'],
                        'priority' => 1,
                        'category' => 'performance',
                        'highlight' => true
                    ],
                    'battery' => [
                        'icon' => 'fa-solid fa-battery-full',
                        'voltage' => 80,
                        'capacity' => 150,
                        'type' => 'Li-ion',
                        'name' => ['tr' => 'Batarya Sistemi', 'en' => 'Battery System'],
                        'priority' => 3,
                        'category' => 'power',
                        'highlight' => true
                    ],
                    'turning_radius' => [
                        'icon' => 'fa-solid fa-circle-arrow-right',
                        'value' => 1450,
                        'unit' => 'mm',
                        'name' => ['tr' => 'Dönüş Yarıçapı', 'en' => 'Turning Radius'],
                        'priority' => 2,
                        'category' => 'performance',
                        'highlight' => true
                    ]
                ]),
                'price_on_request' => true,
                'currency' => 'USD',
                'weight' => 2950,
                'dimensions' => json_encode([
                    'length' => 2733,
                    'width' => 1070,
                    'height' => 2075,
                    'unit' => 'mm'
                ]),
                'condition' => 'new',
                'availability' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'is_bestseller' => true,
                'sort_order' => 1,
                'tags' => json_encode(['electric', 'compact', '3-wheel', 'lithium-ion', 'narrow-aisle']),
                'use_cases' => json_encode([
                    'tr' => [
                        'Dar koridorlu depolar (3.5m genişlik)',
                        'İç mekan yük taşıma',
                        'Hafif-orta yoğunluk operasyonlar',
                        'Lojistik merkezleri',
                        'Üretim tesisleri',
                        'Perakende mağaza depoları',
                        'Temiz çevre uygulamaları'
                    ],
                    'en' => [
                        'Narrow aisle warehouses (3.5m width)',
                        'Indoor material handling',
                        'Light to medium duty operations',
                        'Logistics centers',
                        'Manufacturing facilities',
                        'Retail store warehouses',
                        'Clean environment applications'
                    ]
                ]),
                'competitive_advantages' => json_encode([
                    'tr' => [
                        'Lityum-iyon teknolojisi ile 6 saat kesintisiz çalışma',
                        'Kompakt tasarım ile üstün manevra kabiliyeti',
                        'Düşük gürültü seviyesi (68 dB)',
                        'Entegre şarj sistemi ile kolay kullanım',
                        'Güçlü dual motor sistemi',
                        'Geniş operatör konforu',
                        'Düşük bakım maliyeti',
                        'Çevre dostu sıfır emisyon'
                    ],
                    'en' => [
                        '6 hours continuous operation with lithium-ion technology',
                        'Superior maneuverability with compact design',
                        'Low noise level (68 dB)',
                        'Easy operation with integrated charger',
                        'Powerful dual motor system',
                        'Enhanced operator comfort',
                        'Low maintenance cost',
                        'Eco-friendly zero emission'
                    ]
                ]),
                'target_industries' => json_encode([
                    'tr' => ['Lojistik', 'Depolama', 'Üretim', 'Perakende', 'E-ticaret', 'Otomotiv', 'Gıda'],
                    'en' => ['Logistics', 'Warehousing', 'Manufacturing', 'Retail', 'E-commerce', 'Automotive', 'Food']
                ]),
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'CPD15TVL Elektrikli Forklift 1500kg | iXtif',
                        'description' => '1500kg kapasiteli CPD15TVL elektrikli forklift. 80V Li-ion teknolojisi, kompakt tasarım, 1450mm dönüş yarıçapı. Dar koridorlar için ideal.',
                        'keywords' => ['elektrikli forklift', 'CPD15TVL', 'iXtif', '1500kg', 'lityum ion', 'dar koridor', '3 tekerlek']
                    ],
                    'en' => [
                        'title' => 'CPD15TVL Electric Forklift 1500kg | iXtif',
                        'description' => '1500kg capacity CPD15TVL electric forklift. 80V Li-ion technology, compact design, 1450mm turning radius. Perfect for narrow aisles.',
                        'keywords' => ['electric forklift', 'CPD15TVL', 'iXtif', '1500kg', 'lithium ion', 'narrow aisle', '3 wheel']
                    ]
                ]),
                'faq_data' => json_encode([
                    'questions' => [
                        [
                            'question' => [
                                'tr' => 'CPD15TVL hangi koridor genişliklerinde kullanılabilir?',
                                'en' => 'What aisle widths can CPD15TVL operate in?'
                            ],
                            'answer' => [
                                'tr' => '1450mm dönüş yarıçapı sayesinde minimum 3.5m genişlikteki dar koridorlarda rahatlıkla hareket edebilir.',
                                'en' => 'With a 1450mm turning radius, it can easily maneuver in narrow aisles as small as 3.5m wide.'
                            ],
                            'category' => 'usage',
                            'priority' => 1
                        ],
                        [
                            'question' => [
                                'tr' => 'Batarya ne kadar süre dayanır?',
                                'en' => 'How long does the battery last?'
                            ],
                            'answer' => [
                                'tr' => '80V 150Ah Li-ion batarya ile günde 6 saat kesintisiz çalışma sağlar.',
                                'en' => 'The 80V 150Ah Li-ion battery provides 6 hours of continuous operation daily.'
                            ],
                            'category' => 'technical',
                            'priority' => 2
                        ]
                    ]
                ]),
                'metadata' => json_encode([
                    'source_pdf' => '02_CPD15-18-20TVL-EN-Brochure.pdf',
                    'extraction_date' => '2025-01-15',
                    'category_path' => '1/11',
                    'category_name' => 'Forklift > CPD Series'
                ]),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // CPD18TVL
            [
                'id' => 2,
                'tenant_id' => 1,
                'category_id' => 11, // CPD Series
                'brand_id' => 1, // iXtif
                'sku' => 'CPD18TVL',
                'model_number' => 'CPD18TVL',
                'series_name' => 'CPD TVL',
                'name' => json_encode([
                    'tr' => 'CPD18TVL Elektrikli 3 Tekerlekli Forklift',
                    'en' => 'CPD18TVL Electric 3-Wheel Counterbalance Forklift'
                ]),
                'slug' => 'cpd18tvl-elektrikli-forklift',
                'short_description' => json_encode([
                    'tr' => '1800kg kapasiteli kompakt 80V Li-ion teknolojili 3 tekerlekli elektrikli forklift',
                    'en' => '1800kg capacity compact 3-wheel electric forklift with 80V Li-ion technology'
                ]),
                'body' => json_encode([
                    'tr' => '80V Li-ion teknolojisi ile donatılmış kompakt 3 tekerlekli forklift. Güçlü dual drive AC traction motorlar, geniş operatör alanı (394mm), ayarlanabilir direksiyon ve entegre şarj sistemi ile öne çıkar. Dar koridor kullanımına ideal.',
                    'en' => 'Compact 3 wheels truck designed around 80V Li-Ion battery. Powerful dual drive AC traction motors, big legroom (394mm) for higher operator comfort, great ergonomic design with adjustable steering wheel and single phase integrated charger.'
                ]),
                'technical_specs' => json_encode([
                    'capacity' => ['value' => 1800, 'unit' => 'kg'],
                    'load_center_distance' => ['value' => 500, 'unit' => 'mm'],
                    'service_weight' => ['value' => 3269, 'unit' => 'kg'],
                    'lift_height' => ['value' => 3000, 'unit' => 'mm'],
                    'turning_radius' => ['value' => 1550, 'unit' => 'mm'],
                    'battery_voltage' => ['value' => 80, 'unit' => 'V'],
                    'battery_capacity' => ['value' => 205, 'unit' => 'Ah'],
                    'drive_motor_rating' => ['value' => '2x5.0', 'unit' => 'kW'],
                    'sound_pressure_level' => ['value' => 70, 'unit' => 'dB(A)']
                ]),
                'highlighted_features' => json_encode([
                    'capacity' => [
                        'icon' => 'fa-solid fa-weight-hanging',
                        'value' => 1800,
                        'unit' => 'kg',
                        'name' => ['tr' => 'Yük Kapasitesi', 'en' => 'Load Capacity'],
                        'priority' => 1,
                        'category' => 'performance',
                        'highlight' => true
                    ],
                    'battery' => [
                        'icon' => 'fa-solid fa-battery-full',
                        'voltage' => 80,
                        'capacity' => 205,
                        'type' => 'Li-ion',
                        'name' => ['tr' => 'Batarya Sistemi', 'en' => 'Battery System'],
                        'priority' => 3,
                        'category' => 'power',
                        'highlight' => true
                    ]
                ]),
                'price_on_request' => true,
                'weight' => 3269,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'tags' => json_encode(['electric', 'compact', '3-wheel', 'lithium-ion', 'medium-duty']),
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'CPD18TVL Elektrikli Forklift 1800kg | iXtif',
                        'description' => '1800kg kapasiteli CPD18TVL elektrikli forklift. 80V Li-ion teknolojisi, 205Ah batarya, 1550mm dönüş yarıçapı.',
                        'keywords' => ['elektrikli forklift', 'CPD18TVL', 'iXtif', '1800kg', 'orta tonaj']
                    ],
                    'en' => [
                        'title' => 'CPD18TVL Electric Forklift 1800kg | iXtif',
                        'description' => '1800kg capacity CPD18TVL electric forklift. 80V Li-ion technology, 205Ah battery, 1550mm turning radius.',
                        'keywords' => ['electric forklift', 'CPD18TVL', 'iXtif', '1800kg', 'medium duty']
                    ]
                ]),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // CPD20TVL
            [
                'id' => 3,
                'tenant_id' => 1,
                'category_id' => 11, // CPD Series
                'brand_id' => 1, // iXtif
                'sku' => 'CPD20TVL',
                'model_number' => 'CPD20TVL',
                'series_name' => 'CPD TVL',
                'name' => json_encode([
                    'tr' => 'CPD20TVL Elektrikli 3 Tekerlekli Forklift',
                    'en' => 'CPD20TVL Electric 3-Wheel Counterbalance Forklift'
                ]),
                'slug' => 'cpd20tvl-elektrikli-forklift',
                'short_description' => json_encode([
                    'tr' => '2000kg kapasiteli kompakt 80V Li-ion teknolojili 3 tekerlekli elektrikli forklift',
                    'en' => '2000kg capacity compact 3-wheel electric forklift with 80V Li-ion technology'
                ]),
                'body' => json_encode([
                    'tr' => '80V Li-ion teknolojisi ile donatılmış, 2000kg yük kapasiteli güçlü 3 tekerlekli forklift. Dual drive AC traction motorlar, geniş operatör alanı, ergonomik tasarım ve entegre şarj sistemi ile ağır iş yükleri için tasarlanmış.',
                    'en' => 'Powerful 3-wheel forklift with 2000kg load capacity, equipped with 80V Li-ion technology. Dual drive AC traction motors, spacious operator area, ergonomic design and integrated charging system designed for heavy-duty applications.'
                ]),
                'technical_specs' => json_encode([
                    'capacity' => ['value' => 2000, 'unit' => 'kg'],
                    'load_center_distance' => ['value' => 500, 'unit' => 'mm'],
                    'service_weight' => ['value' => 3429, 'unit' => 'kg'],
                    'lift_height' => ['value' => 3000, 'unit' => 'mm'],
                    'turning_radius' => ['value' => 1585, 'unit' => 'mm'],
                    'battery_voltage' => ['value' => 80, 'unit' => 'V'],
                    'battery_capacity' => ['value' => 205, 'unit' => 'Ah'],
                    'drive_motor_rating' => ['value' => '2x5.0', 'unit' => 'kW'],
                    'sound_pressure_level' => ['value' => 74, 'unit' => 'dB(A)']
                ]),
                'highlighted_features' => json_encode([
                    'capacity' => [
                        'icon' => 'fa-solid fa-weight-hanging',
                        'value' => 2000,
                        'unit' => 'kg',
                        'name' => ['tr' => 'Yük Kapasitesi', 'en' => 'Load Capacity'],
                        'priority' => 1,
                        'category' => 'performance',
                        'highlight' => true
                    ],
                    'fork_specs' => [
                        'icon' => 'fa-solid fa-arrows-up-down',
                        'dimensions' => '122x40x1070',
                        'unit' => 'mm',
                        'name' => ['tr' => 'Çatal Boyutları', 'en' => 'Fork Dimensions'],
                        'priority' => 5,
                        'category' => 'mechanics',
                        'highlight' => true
                    ]
                ]),
                'price_on_request' => true,
                'weight' => 3429,
                'dimensions' => json_encode([
                    'length' => 3020,
                    'width' => 1170,
                    'height' => 2075,
                    'unit' => 'mm'
                ]),
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'tags' => json_encode(['electric', 'heavy-duty', '3-wheel', 'lithium-ion', '2000kg']),
                'target_industries' => json_encode([
                    'tr' => ['Ağır Sanayi', 'Metallurji', 'İnşaat', 'Lojistik', 'Otomotiv', 'Makine İmalat'],
                    'en' => ['Heavy Industry', 'Metallurgy', 'Construction', 'Logistics', 'Automotive', 'Machinery Manufacturing']
                ]),
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'CPD20TVL Elektrikli Forklift 2000kg | iXtif',
                        'description' => '2000kg kapasiteli CPD20TVL elektrikli forklift. Ağır işler için 80V Li-ion teknolojisi.',
                        'keywords' => ['elektrikli forklift', 'CPD20TVL', 'iXtif', '2000kg', 'ağır iş']
                    ],
                    'en' => [
                        'title' => 'CPD20TVL Electric Forklift 2000kg | iXtif',
                        'description' => '2000kg capacity CPD20TVL electric forklift. 80V Li-ion technology for heavy-duty applications.',
                        'keywords' => ['electric forklift', 'CPD20TVL', 'iXtif', '2000kg', 'heavy duty']
                    ]
                ]),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert products
        DB::table('ecommerce_products')->insert($products);

        // Insert product variants for CPD15TVL
        $variants = [
            [
                'product_id' => 1,
                'sku' => 'CPD15TVL-STD-3000',
                'name' => json_encode([
                    'tr' => 'Standart Mast (3000mm)',
                    'en' => 'Standard Mast (3000mm)'
                ]),
                'variant_type' => 'mast_height',
                'option_values' => json_encode([
                    'mast_type' => '2-Standard',
                    'lift_height' => '3000mm'
                ]),
                'is_default' => true,
                'sort_order' => 1,
                'variant_data' => json_encode([
                    'mast_type' => '2-Standard',
                    'lift_height' => 3000,
                    'mast_extended_height' => 4055,
                    'free_lift' => 100
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'sku' => 'CPD15TVL-HIGH-3600',
                'name' => json_encode([
                    'tr' => 'Yüksek Mast (3600mm)',
                    'en' => 'High Mast (3600mm)'
                ]),
                'variant_type' => 'mast_height',
                'option_values' => json_encode([
                    'mast_type' => '2-Standard',
                    'lift_height' => '3600mm'
                ]),
                'sort_order' => 2,
                'variant_data' => json_encode([
                    'mast_type' => '2-Standard',
                    'lift_height' => 3600,
                    'mast_extended_height' => 4655,
                    'free_lift' => 100
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('ecommerce_product_variants')->insert($variants);

        $this->command->info('E-commerce products and variants seeded successfully!');
    }
}
