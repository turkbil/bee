<?php

namespace Modules\Payment\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentCategory;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Payment SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('📦 Payment: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, payment tables do not exist in central!');
            return;
        }

        // Tenant context kontrolü
        if (!tenancy()->initialized) {
            $this->command->error('❌ Tenant context not initialized for Payment!');
            return;
        }

        // Duplicate check - eğer zaten veri varsa skip
        if (PaymentCategory::count() > 0) {
            $this->command->warn("⚠️  Payment categories already exist. Skipping...");
            return;
        }

        $faker = Faker::create('tr_TR');

        // 5 Kategori
        $categoryNames = [
            'Web Tasarım' => 'Web Design',
            'Mobil Uygulama' => 'Mobile App',
            'E-Ticaret' => 'E-Commerce',
            'Kurumsal' => 'Corporate',
            'Dijital Pazarlama' => 'Digital Marketing'
        ];

        foreach ($categoryNames as $nameTr => $nameEn) {
            $category = PaymentCategory::create([
                'title' => [
                    'tr' => $nameTr,
                    'en' => $nameEn,
                    'ar' => $nameTr
                ],
                'slug' => [
                    'tr' => Str::slug($nameTr),
                    'en' => Str::slug($nameEn),
                    'ar' => Str::slug($nameTr)
                ],
                'description' => [
                    'tr' => $faker->paragraph(3),
                    'en' => $faker->paragraph(3),
                    'ar' => $faker->paragraph(3)
                ],
                'is_active' => true,
                'sort_order' => 0,
                'parent_id' => null,
            ]);

            // Her kategori için 10 payment
            for ($i = 1; $i <= 10; $i++) {
                $title = ucwords($faker->words(rand(2, 4), true));

                Payment::create([
                    'title' => [
                        'tr' => $title,
                        'en' => $title,
                        'ar' => $title
                    ],
                    'slug' => [
                        'tr' => Str::slug($title) . '-' . $faker->unique()->numberBetween(1000, 9999),
                        'en' => Str::slug($title) . '-' . $faker->unique()->numberBetween(1000, 9999),
                        'ar' => Str::slug($title) . '-' . $faker->unique()->numberBetween(1000, 9999)
                    ],
                    'body' => [
                        'tr' => $this->generateContent($faker),
                        'en' => $this->generateContent($faker),
                        'ar' => $this->generateContent($faker)
                    ],
                    'payment_category_id' => $category->category_id,
                    'is_active' => $faker->boolean(90),
                ]);
            }
        }
    }

    private function generateContent($faker): string
    {
        $content = '<h2>' . $faker->sentence(4) . '</h2>';
        $content .= '<p>' . $faker->paragraph(5) . '</p>';

        $content .= '<h3>' . $faker->sentence(3) . '</h3>';
        $content .= '<ul>';
        for ($i = 0; $i < rand(3, 5); $i++) {
            $content .= '<li>' . $faker->sentence() . '</li>';
        }
        $content .= '</ul>';

        $content .= '<p>' . $faker->paragraph(4) . '</p>';

        return $content;
    }
}
