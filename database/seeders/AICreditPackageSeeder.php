<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AICreditPackage;

class AICreditPackageSeeder extends Seeder
{
    /**
     * Kredi paketlerini seed et
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Başlangıç Paketi',
                'description' => 'Küçük projeler ve bireysel kullanım için ideal',
                'credits' => 10.00,
                'price_usd' => 5.00,
                'price_try' => 170.00,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    '10 kredi',
                    'Temel AI özellikleri',
                    'Email desteği',
                    '30 gün geçerlilik'
                ],
                'discount_percentage' => 0
            ],
            [
                'name' => 'Standart Paket',
                'description' => 'Küçük işletmeler ve orta ölçekli projeler için',
                'credits' => 50.00,
                'price_usd' => 20.00,
                'price_try' => 680.00,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    '50 kredi',
                    'Tüm AI özellikleri',
                    'Öncelikli email desteği',
                    '60 gün geçerlilik',
                    '%15 bonus kredi'
                ],
                'discount_percentage' => 15
            ],
            [
                'name' => 'Premium Paket',
                'description' => 'Büyük işletmeler ve yoğun kullanım için',
                'credits' => 150.00,
                'price_usd' => 50.00,
                'price_try' => 1700.00,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    '150 kredi',
                    'Tüm AI özellikleri',
                    'Canlı destek',
                    '90 gün geçerlilik',
                    '%25 bonus kredi',
                    'API erişimi'
                ],
                'discount_percentage' => 25
            ],
            [
                'name' => 'Kurumsal Paket',
                'description' => 'Büyük organizasyonlar ve sınırsız kullanım için',
                'credits' => 500.00,
                'price_usd' => 150.00,
                'price_try' => 5100.00,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
                'features' => [
                    '500 kredi',
                    'Tüm AI özellikleri',
                    'Özel destek temsilcisi',
                    '120 gün geçerlilik',
                    '%35 bonus kredi',
                    'Özel API erişimi',
                    'Özel entegrasyonlar'
                ],
                'discount_percentage' => 35
            ]
        ];

        foreach ($packages as $package) {
            AICreditPackage::updateOrCreate(
                ['name' => $package['name']], 
                $package
            );
        }

        $this->command->info('AI Credit Packages seeded successfully!');
    }
}