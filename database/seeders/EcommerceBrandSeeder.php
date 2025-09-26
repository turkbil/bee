<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommerceBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'id' => 1,
                'tenant_id' => 1,
                'parent_brand_id' => null,
                'name' => json_encode([
                    'tr' => 'iXtif',
                    'en' => 'iXtif'
                ]),
                'slug' => 'ixtif',
                'logo_url' => 'brands/ixtif-logo.png',
                'banner_url' => 'brands/ixtif-banner.jpg',
                'description' => json_encode([
                    'tr' => 'Endüstriyel yük taşıma ekipmanları konusunda lider Türk markası. Forklift, transpalet, istif makineleri ve otonom çözümler.',
                    'en' => 'Leading Turkish brand in industrial material handling equipment. Forklifts, pallet trucks, stacking machines and autonomous solutions.'
                ]),
                'short_description' => json_encode([
                    'tr' => 'Endüstriyel yük taşıma ekipmanları lideri',
                    'en' => 'Leader in industrial material handling equipment'
                ]),
                'website_url' => 'https://ixtif.com',
                'country_code' => 'TR',
                'founded_year' => 2020,
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'iXtif | Forklift ve Yük Taşıma Ekipmanları',
                        'description' => 'iXtif marka forklift, transpalet, istif makineleri. Kaliteli endüstriyel yük taşıma çözümleri.',
                        'keywords' => ['iXtif', 'forklift', 'transpalet', 'yük taşıma', 'endüstriyel ekipman']
                    ],
                    'en' => [
                        'title' => 'iXtif | Forklift and Material Handling Equipment',
                        'description' => 'iXtif brand forklifts, pallet trucks, stacking machines. Quality industrial material handling solutions.',
                        'keywords' => ['iXtif', 'forklift', 'pallet truck', 'material handling', 'industrial equipment']
                    ]
                ]),
                'contact_info' => json_encode([
                    'tr' => [
                        'phone' => '+90 212 XXX XX XX',
                        'email' => 'info@ixtif.com',
                        'address' => 'İstanbul, Türkiye'
                    ],
                    'en' => [
                        'phone' => '+90 212 XXX XX XX',
                        'email' => 'info@ixtif.com',
                        'address' => 'Istanbul, Turkey'
                    ]
                ]),
                'social_media' => json_encode([
                    'facebook' => 'https://facebook.com/ixtif',
                    'twitter' => 'https://twitter.com/ixtif',
                    'linkedin' => 'https://linkedin.com/company/ixtif',
                    'youtube' => 'https://youtube.com/@ixtif',
                    'instagram' => 'https://instagram.com/ixtif'
                ]),
                'certifications' => json_encode([
                    'tr' => [
                        'ISO 9001:2015 Kalite Yönetim Sistemi',
                        'CE Uygunluk Belgesi',
                        'TSE Türk Standardları',
                        'Endüstri 4.0 Sertifikası'
                    ],
                    'en' => [
                        'ISO 9001:2015 Quality Management System',
                        'CE Conformity Certificate',
                        'TSE Turkish Standards',
                        'Industry 4.0 Certificate'
                    ]
                ]),
                'metadata' => json_encode([
                    'primary_categories' => [1, 2, 3, 4, 5, 6],
                    'specializations' => ['electric_forklifts', 'autonomous_systems', 'lithium_ion_technology'],
                    'target_markets' => ['logistics', 'manufacturing', 'warehousing', 'retail'],
                    'unique_selling_points' => [
                        'tr' => [
                            'Lityum-iyon teknolojisi',
                            'Otonom çözümler',
                            'Türkiye yerel desteği',
                            'Çevre dostu elektrifikasyon'
                        ],
                        'en' => [
                            'Lithium-ion technology',
                            'Autonomous solutions',
                            'Turkey local support',
                            'Eco-friendly electrification'
                        ]
                    ],
                    'warranty_info' => [
                        'tr' => '2 yıl kapsamlı garanti, 5 yıl batarya garantisi',
                        'en' => '2 years comprehensive warranty, 5 years battery warranty'
                    ],
                    'brand_categories' => [
                        '1' => [ // Forklift
                            'strength' => 'primary',
                            'market_share' => 'leading',
                            'specialization' => ['electric_3_wheel', 'lithium_ion', 'compact_design']
                        ],
                        '2' => [ // Transpalet
                            'strength' => 'primary',
                            'market_share' => 'strong',
                            'specialization' => ['electric_pallet_trucks', 'long_battery_life']
                        ],
                        '3' => [ // İstif Makineleri
                            'strength' => 'secondary',
                            'market_share' => 'growing',
                            'specialization' => ['reach_stackers', 'work_platforms']
                        ],
                        '5' => [ // Otonom
                            'strength' => 'primary',
                            'market_share' => 'innovative',
                            'specialization' => ['AGV', 'autonomous_navigation', 'fleet_management']
                        ]
                    ],
                    'brand_badges' => [
                        'quality' => [
                            'tr' => 'Kalite Garantili',
                            'en' => 'Quality Guaranteed',
                            'icon' => 'fa-solid fa-certificate',
                            'color' => 'success'
                        ],
                        'local_support' => [
                            'tr' => 'Türkiye Desteği',
                            'en' => 'Turkey Support',
                            'icon' => 'fa-solid fa-flag',
                            'color' => 'primary'
                        ],
                        'eco_friendly' => [
                            'tr' => 'Çevre Dostu',
                            'en' => 'Eco Friendly',
                            'icon' => 'fa-solid fa-leaf',
                            'color' => 'success'
                        ],
                        'innovative' => [
                            'tr' => 'Yenilikçi Teknoloji',
                            'en' => 'Innovative Technology',
                            'icon' => 'fa-solid fa-lightbulb',
                            'color' => 'warning'
                        ],
                        'lithium_ion' => [
                            'tr' => 'Li-ion Teknolojisi',
                            'en' => 'Li-ion Technology',
                            'icon' => 'fa-solid fa-battery-full',
                            'color' => 'info'
                        ]
                    ],
                    'brand_colors' => [
                        'primary' => '#0066CC',
                        'secondary' => '#FF6600',
                        'accent' => '#00AA44',
                        'dark' => '#1A1A1A',
                        'light' => '#F8F9FA'
                    ],
                    'brand_typography' => [
                        'primary_font' => 'Inter, sans-serif',
                        'secondary_font' => 'Roboto, sans-serif',
                        'logo_font' => 'Inter Bold'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'parent_brand_id' => 1,
                'name' => json_encode([
                    'tr' => 'iXtif Pro',
                    'en' => 'iXtif Pro'
                ]),
                'slug' => 'ixtif-pro',
                'description' => json_encode([
                    'tr' => 'Profesyonel ve yüksek kapasiteli endüstriyel çözümler',
                    'en' => 'Professional and high-capacity industrial solutions'
                ]),
                'short_description' => json_encode([
                    'tr' => 'Profesyonel endüstriyel çözümler',
                    'en' => 'Professional industrial solutions'
                ]),
                'country_code' => 'TR',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => false,
                'metadata' => json_encode([
                    'target_segments' => ['heavy_industry', 'large_warehouses', 'high_capacity_operations'],
                    'specialization' => ['high_capacity', 'heavy_duty', 'professional_grade']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'tenant_id' => 1,
                'parent_brand_id' => 1,
                'name' => json_encode([
                    'tr' => 'iXtif Eco',
                    'en' => 'iXtif Eco'
                ]),
                'slug' => 'ixtif-eco',
                'description' => json_encode([
                    'tr' => 'Çevre dostu ve enerji tasarruflu modeller',
                    'en' => 'Eco-friendly and energy efficient models'
                ]),
                'short_description' => json_encode([
                    'tr' => 'Çevre dostu çözümler',
                    'en' => 'Eco-friendly solutions'
                ]),
                'country_code' => 'TR',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => false,
                'metadata' => json_encode([
                    'focus_areas' => ['sustainability', 'energy_efficiency', 'zero_emission'],
                    'specialization' => ['electric_only', 'renewable_energy', 'carbon_neutral']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'tenant_id' => 1,
                'parent_brand_id' => 1,
                'name' => json_encode([
                    'tr' => 'iXtif Smart',
                    'en' => 'iXtif Smart'
                ]),
                'slug' => 'ixtif-smart',
                'description' => json_encode([
                    'tr' => 'Akıllı ve otonom teknoloji çözümleri',
                    'en' => 'Smart and autonomous technology solutions'
                ]),
                'short_description' => json_encode([
                    'tr' => 'Akıllı teknoloji çözümleri',
                    'en' => 'Smart technology solutions'
                ]),
                'country_code' => 'TR',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => true,
                'metadata' => json_encode([
                    'technology_focus' => ['AI', 'IoT', 'autonomous_navigation', 'fleet_management'],
                    'specialization' => ['autonomous_vehicles', 'smart_logistics', 'connected_systems']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert brands
        DB::table('ecommerce_brands')->insert($brands);

        $this->command->info('E-commerce brands seeded successfully!');
    }
}