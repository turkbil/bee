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
                'name' => 'Başlangıç',
                'description' => 'Küçük projeler ve bireysel kullanım için ideal',
                'credit_amount' => 10,
                'price' => 170.00,
                'currency' => 'TRY',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    '10 kredi',
                    'Temel AI özellikleri',
                    'Email desteği',
                    '30 gün geçerlilik'
                ]
            ],
            [
                'name' => 'Standart',
                'description' => 'Küçük işletmeler ve orta ölçekli projeler için',
                'credit_amount' => 50,
                'price' => 680.00,
                'currency' => 'TRY',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    '50 kredi',
                    'Tüm AI özellikleri',
                    'Öncelikli email desteği',
                    '60 gün geçerlilik',
                    '%15 bonus kredi'
                ]
            ],
            [
                'name' => 'Premium',
                'description' => 'Büyük işletmeler ve yoğun kullanım için',
                'credit_amount' => 150,
                'price' => 1700.00,
                'currency' => 'TRY',
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
                ]
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Büyük organizasyonlar ve sınırsız kullanım için',
                'credit_amount' => 500,
                'price' => 5100.00,
                'currency' => 'TRY',
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
                ]
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