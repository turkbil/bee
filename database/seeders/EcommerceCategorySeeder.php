<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommerceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // LEVEL 1 - MAIN CATEGORIES
            [
                'id' => 1,
                'tenant_id' => 1,
                'parent_id' => null,
                'name' => json_encode([
                    'tr' => 'Forklift',
                    'en' => 'Forklift'
                ]),
                'slug' => 'forklift',
                'description' => json_encode([
                    'tr' => 'Elektrikli ve dizel forkliftler, çeşitli tonajlarda yük taşıma çözümleri',
                    'en' => 'Electric and diesel forklifts, load handling solutions in various capacities'
                ]),
                'icon_class' => 'fa-solid fa-truck-pickup',
                'level' => 1,
                'path' => '1',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'Forklift Çeşitleri ve Modelleri | iXtif',
                        'description' => 'Elektrikli ve dizel forklift modelleri. CPD, EFL, X serisi forkliftler. Profesyonel yük taşıma çözümleri.',
                        'keywords' => ['forklift', 'elektrikli forklift', 'CPD serisi', 'yük taşıma', 'depo ekipmanı']
                    ],
                    'en' => [
                        'title' => 'Forklift Types and Models | iXtif',
                        'description' => 'Electric and diesel forklift models. CPD, EFL, X series forklifts. Professional material handling solutions.',
                        'keywords' => ['forklift', 'electric forklift', 'CPD series', 'material handling', 'warehouse equipment']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'parent_id' => null,
                'name' => json_encode([
                    'tr' => 'Transpalet',
                    'en' => 'Pallet Truck'
                ]),
                'slug' => 'transpalet',
                'description' => json_encode([
                    'tr' => 'Elektrikli ve manuel transpaletler, palet taşıma çözümleri',
                    'en' => 'Electric and manual pallet trucks, pallet handling solutions'
                ]),
                'icon_class' => 'fa-solid fa-dolly',
                'level' => 1,
                'path' => '2',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => true,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'Transpalet Modelleri | iXtif',
                        'description' => 'Elektrikli ve manuel transpalet modelleri. EPL, EPT serisi palet trucklarımızı keşfedin.',
                        'keywords' => ['transpalet', 'pallet truck', 'elektrikli transpalet', 'EPL serisi']
                    ],
                    'en' => [
                        'title' => 'Pallet Truck Models | iXtif',
                        'description' => 'Electric and manual pallet truck models. Discover our EPL, EPT series pallet trucks.',
                        'keywords' => ['pallet truck', 'electric pallet truck', 'EPL series', 'material handling']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'tenant_id' => 1,
                'parent_id' => null,
                'name' => json_encode([
                    'tr' => 'İstif Makineleri',
                    'en' => 'Stacking Machines'
                ]),
                'slug' => 'istif-makineleri',
                'description' => json_encode([
                    'tr' => 'Yüksek kaldırma kapasiteli istif makineleri ve platformları',
                    'en' => 'High lifting capacity stacking machines and platforms'
                ]),
                'icon_class' => 'fa-solid fa-layer-group',
                'level' => 1,
                'path' => '3',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => true,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'İstif Makineleri | iXtif',
                        'description' => 'Yüksek kaldırma kapasiteli istif makineleri. ES, RSC, WSA serisi çözümlerimizi inceleyin.',
                        'keywords' => ['istif makinesi', 'stacker', 'yüksek kaldırma', 'ES serisi']
                    ],
                    'en' => [
                        'title' => 'Stacking Machines | iXtif',
                        'description' => 'High lifting capacity stacking machines. Explore our ES, RSC, WSA series solutions.',
                        'keywords' => ['stacking machine', 'stacker', 'high lift', 'ES series']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'tenant_id' => 1,
                'parent_id' => null,
                'name' => json_encode([
                    'tr' => 'Sipariş Toplayıcı',
                    'en' => 'Order Picker'
                ]),
                'slug' => 'siparis-toplayici',
                'description' => json_encode([
                    'tr' => 'Dikey sipariş toplama makineleri, yüksek raf erişimi',
                    'en' => 'Vertical order picking machines, high shelf access'
                ]),
                'icon_class' => 'fa-solid fa-hand-point-up',
                'level' => 1,
                'path' => '4',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => false,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'Sipariş Toplayıcı Makineler | iXtif',
                        'description' => 'Dikey sipariş toplama makineleri. Yüksek raflara güvenli erişim.',
                        'keywords' => ['sipariş toplayıcı', 'order picker', 'yüksek raf', 'dikey']
                    ],
                    'en' => [
                        'title' => 'Order Picker Machines | iXtif',
                        'description' => 'Vertical order picking machines. Safe access to high shelves.',
                        'keywords' => ['order picker', 'vertical picking', 'high shelf', 'warehouse']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'tenant_id' => 1,
                'parent_id' => null,
                'name' => json_encode([
                    'tr' => 'Otonom Makineler',
                    'en' => 'Autonomous Machines'
                ]),
                'slug' => 'otonom-makineler',
                'description' => json_encode([
                    'tr' => 'Sürücüsüz otonom yük taşıma sistemleri',
                    'en' => 'Driverless autonomous material handling systems'
                ]),
                'icon_class' => 'fa-solid fa-robot',
                'level' => 1,
                'path' => '5',
                'sort_order' => 5,
                'is_active' => true,
                'is_featured' => true,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'Otonom Makineler | iXtif',
                        'description' => 'Sürücüsüz otonom yük taşıma sistemleri. Gelecek teknolojisi ile verimlilik.',
                        'keywords' => ['otonom', 'AGV', 'sürücüsüz', 'teknoloji']
                    ],
                    'en' => [
                        'title' => 'Autonomous Machines | iXtif',
                        'description' => 'Driverless autonomous material handling systems. Efficiency with future technology.',
                        'keywords' => ['autonomous', 'AGV', 'driverless', 'technology']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'tenant_id' => 1,
                'parent_id' => null,
                'name' => json_encode([
                    'tr' => 'Reach Truck',
                    'en' => 'Reach Truck'
                ]),
                'slug' => 'reach-truck',
                'description' => json_encode([
                    'tr' => 'Dar koridor çalışma kapasiteli reach truck\'lar',
                    'en' => 'Narrow aisle capability reach trucks'
                ]),
                'icon_class' => 'fa-solid fa-arrows-left-right',
                'level' => 1,
                'path' => '6',
                'sort_order' => 6,
                'is_active' => true,
                'is_featured' => false,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'Reach Truck Modelleri | iXtif',
                        'description' => 'Dar koridor çalışma kapasiteli reach truck\'lar. Yüksek raflı depo çözümleri.',
                        'keywords' => ['reach truck', 'dar koridor', 'yüksek raf', 'depo']
                    ],
                    'en' => [
                        'title' => 'Reach Truck Models | iXtif',
                        'description' => 'Narrow aisle capability reach trucks. High rack warehouse solutions.',
                        'keywords' => ['reach truck', 'narrow aisle', 'high rack', 'warehouse']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // LEVEL 2 - SUB CATEGORIES - FORKLIFT
            [
                'id' => 11,
                'tenant_id' => 1,
                'parent_id' => 1,
                'name' => json_encode([
                    'tr' => 'CPD Serisi',
                    'en' => 'CPD Series'
                ]),
                'slug' => 'cpd-serisi',
                'description' => json_encode([
                    'tr' => 'Kompakt elektrikli 3 tekerlekli forkliftler',
                    'en' => 'Compact electric 3-wheel forklifts'
                ]),
                'level' => 2,
                'path' => '1/11',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'seo_data' => json_encode([
                    'tr' => [
                        'title' => 'CPD Serisi Forklift | iXtif',
                        'description' => 'CPD15TVL, CPD18TVL, CPD20TVL modelleri. 80V Li-ion teknolojisi.',
                        'keywords' => ['CPD serisi', 'CPD15TVL', 'CPD18TVL', 'CPD20TVL']
                    ],
                    'en' => [
                        'title' => 'CPD Series Forklift | iXtif',
                        'description' => 'CPD15TVL, CPD18TVL, CPD20TVL models. 80V Li-ion technology.',
                        'keywords' => ['CPD series', 'CPD15TVL', 'CPD18TVL', 'CPD20TVL']
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 12,
                'tenant_id' => 1,
                'parent_id' => 1,
                'name' => json_encode([
                    'tr' => 'EFL Serisi',
                    'en' => 'EFL Series'
                ]),
                'slug' => 'efl-serisi',
                'description' => json_encode([
                    'tr' => 'Elektrikli 4 tekerlekli counterbalance forkliftler',
                    'en' => 'Electric 4-wheel counterbalance forklifts'
                ]),
                'level' => 2,
                'path' => '1/12',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 13,
                'tenant_id' => 1,
                'parent_id' => 1,
                'name' => json_encode([
                    'tr' => 'X Serisi',
                    'en' => 'X Series'
                ]),
                'slug' => 'x-serisi',
                'description' => json_encode([
                    'tr' => 'Yüksek performanslı X2, X3, X4, X5 modelleri',
                    'en' => 'High performance X2, X3, X4, X5 models'
                ]),
                'level' => 2,
                'path' => '1/13',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 14,
                'tenant_id' => 1,
                'parent_id' => 1,
                'name' => json_encode([
                    'tr' => 'Yüksek Voltaj Serisi',
                    'en' => 'High Voltage Series'
                ]),
                'slug' => 'yuksek-voltaj-serisi',
                'description' => json_encode([
                    'tr' => '80V-120V yüksek voltaj teknolojili forkliftler',
                    'en' => '80V-120V high voltage technology forklifts'
                ]),
                'level' => 2,
                'path' => '1/14',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 15,
                'tenant_id' => 1,
                'parent_id' => 1,
                'name' => json_encode([
                    'tr' => 'Dizel Forklift',
                    'en' => 'Diesel Forklift'
                ]),
                'slug' => 'dizel-forklift',
                'description' => json_encode([
                    'tr' => 'TDL serisi dizel yakıtlı forkliftler',
                    'en' => 'TDL series diesel powered forklifts'
                ]),
                'level' => 2,
                'path' => '1/15',
                'sort_order' => 5,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // LEVEL 2 - SUB CATEGORIES - TRANSPALET
            [
                'id' => 21,
                'tenant_id' => 1,
                'parent_id' => 2,
                'name' => json_encode([
                    'tr' => 'EPL Serisi',
                    'en' => 'EPL Series'
                ]),
                'slug' => 'epl-serisi',
                'description' => json_encode([
                    'tr' => 'Elektrikli palet truck\'lar',
                    'en' => 'Electric pallet trucks'
                ]),
                'level' => 2,
                'path' => '2/21',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 22,
                'tenant_id' => 1,
                'parent_id' => 2,
                'name' => json_encode([
                    'tr' => 'EPT Serisi',
                    'en' => 'EPT Series'
                ]),
                'slug' => 'ept-serisi',
                'description' => json_encode([
                    'tr' => 'Elektrikli transpalet çeşitleri',
                    'en' => 'Electric pallet truck varieties'
                ]),
                'level' => 2,
                'path' => '2/22',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 23,
                'tenant_id' => 1,
                'parent_id' => 2,
                'name' => json_encode([
                    'tr' => 'Manuel Transpalet',
                    'en' => 'Manual Pallet Truck'
                ]),
                'slug' => 'manuel-transpalet',
                'description' => json_encode([
                    'tr' => 'F serisi manuel palet truck\'lar',
                    'en' => 'F series manual pallet trucks'
                ]),
                'level' => 2,
                'path' => '2/23',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // LEVEL 2 - SUB CATEGORIES - İSTIF MAKİNELERİ
            [
                'id' => 31,
                'tenant_id' => 1,
                'parent_id' => 3,
                'name' => json_encode([
                    'tr' => 'ES Serisi',
                    'en' => 'ES Series'
                ]),
                'slug' => 'es-serisi',
                'description' => json_encode([
                    'tr' => 'Elektrikli istif makineleri',
                    'en' => 'Electric stacking machines'
                ]),
                'level' => 2,
                'path' => '3/31',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 32,
                'tenant_id' => 1,
                'parent_id' => 3,
                'name' => json_encode([
                    'tr' => 'RSC Serisi',
                    'en' => 'RSC Series'
                ]),
                'slug' => 'rsc-serisi',
                'description' => json_encode([
                    'tr' => 'Reach stacker çeşitleri',
                    'en' => 'Reach stacker varieties'
                ]),
                'level' => 2,
                'path' => '3/32',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 33,
                'tenant_id' => 1,
                'parent_id' => 3,
                'name' => json_encode([
                    'tr' => 'WSA Serisi',
                    'en' => 'WSA Series'
                ]),
                'slug' => 'wsa-serisi',
                'description' => json_encode([
                    'tr' => 'Çalışma platformlu istif makineleri',
                    'en' => 'Work platform stacking machines'
                ]),
                'level' => 2,
                'path' => '3/33',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert categories
        DB::table('ecommerce_categories')->insert($categories);

        $this->command->info('E-commerce categories seeded successfully!');
    }
}