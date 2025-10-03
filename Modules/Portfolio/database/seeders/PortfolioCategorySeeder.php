<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;

/**
 * Portfolio Category Seeder
 *
 * Temel portfolio kategorilerini oluşturur.
 * Tüm kategoriler JSON çoklu dil desteği ile gelir.
 *
 * @package Modules\Portfolio\Database\Seeders
 */
class PortfolioCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Portfolio kategorileri SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('📁 Portfolio categories: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, portfolio tables do not exist in central!');
            return;
        }

        // Tenant context kontrolü
        if (!tenancy()->initialized) {
            $this->command->error('❌ Tenant context not initialized for Portfolio Categories!');
            return;
        }

        $this->command->info('📁 Creating portfolio categories...');

        $categories = [
            [
                'name' => [
                    'tr' => 'Web Tasarım',
                    'en' => 'Web Design'
                ],
                'slug' => [
                    'tr' => 'web-tasarim',
                    'en' => 'web-design'
                ],
                'description' => [
                    'tr' => 'Modern ve responsive web tasarım projeleri. Kullanıcı deneyimi odaklı, SEO uyumlu ve mobil uyumlu web siteleri.',
                    'en' => 'Modern and responsive web design projects. User experience focused, SEO compatible and mobile friendly websites.'
                ],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'Mobil Uygulama',
                    'en' => 'Mobile Application'
                ],
                'slug' => [
                    'tr' => 'mobil-uygulama',
                    'en' => 'mobile-application'
                ],
                'description' => [
                    'tr' => 'iOS ve Android mobil uygulama geliştirme projeleri. Native ve cross-platform çözümler.',
                    'en' => 'iOS and Android mobile application development projects. Native and cross-platform solutions.'
                ],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'E-Ticaret',
                    'en' => 'E-Commerce'
                ],
                'slug' => [
                    'tr' => 'e-ticaret',
                    'en' => 'e-commerce'
                ],
                'description' => [
                    'tr' => 'Online satış platformları ve e-ticaret çözümleri. B2B ve B2C ticaret sistemleri.',
                    'en' => 'Online sales platforms and e-commerce solutions. B2B and B2C commerce systems.'
                ],
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'Kurumsal Yazılım',
                    'en' => 'Corporate Software'
                ],
                'slug' => [
                    'tr' => 'kurumsal-yazilim',
                    'en' => 'corporate-software'
                ],
                'description' => [
                    'tr' => 'Kurumsal web siteleri ve özel yazılım projeleri. ERP, CRM ve iş süreçleri yönetimi.',
                    'en' => 'Corporate websites and custom software projects. ERP, CRM and business process management.'
                ],
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'Grafik Tasarım',
                    'en' => 'Graphic Design'
                ],
                'slug' => [
                    'tr' => 'grafik-tasarim',
                    'en' => 'graphic-design'
                ],
                'description' => [
                    'tr' => 'Logo, afiş ve görsel tasarım projeleri. Kurumsal kimlik ve marka tasarımı.',
                    'en' => 'Logo, poster and visual design projects. Corporate identity and brand design.'
                ],
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'UI/UX Tasarım',
                    'en' => 'UI/UX Design'
                ],
                'slug' => [
                    'tr' => 'ui-ux-tasarim',
                    'en' => 'ui-ux-design'
                ],
                'description' => [
                    'tr' => 'Kullanıcı arayüzü ve kullanıcı deneyimi tasarım projeleri. Interaktif prototipleme ve kullanılabilirlik testleri.',
                    'en' => 'User interface and user experience design projects. Interactive prototyping and usability testing.'
                ],
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'Dijital Pazarlama',
                    'en' => 'Digital Marketing'
                ],
                'slug' => [
                    'tr' => 'dijital-pazarlama',
                    'en' => 'digital-marketing'
                ],
                'description' => [
                    'tr' => 'SEO, SEM ve sosyal medya pazarlama projeleri. Dijital strateji ve içerik yönetimi.',
                    'en' => 'SEO, SEM and social media marketing projects. Digital strategy and content management.'
                ],
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            PortfolioCategory::create($category);
            $this->command->info("✓ Category created: {$category['name']['en']}");
        }

        $this->command->info("✅ Total {$this->count($categories)} categories created");
    }

    /**
     * Count helper
     */
    private function count(array $items): int
    {
        return count($items);
    }
}
