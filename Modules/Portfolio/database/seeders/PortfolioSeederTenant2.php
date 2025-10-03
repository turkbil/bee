<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;

/**
 * Portfolio Tenant 2 Database Seeder
 *
 * Seeds portfolios for Tenant 2 - Digital Agency / E-Commerce theme.
 * Creates demo portfolios with TR/EN translations.
 *
 * @package Modules\Portfolio\Database\Seeders
 */
class PortfolioSeederTenant2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Portfolio SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('🏢 Portfolio Tenant2: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, portfolio tables do not exist in central!');
            return;
        }

        $this->command->info('🏢 TENANT 2 - Digital Agency / E-Commerce Portfolio Seeding');
        $this->command->newLine();

        // Duplicate check
        if (Portfolio::count() > 0) {
            $this->command->info('📋 Portfolios already exist, skipping...');
            return;
        }

        // Önce kategorileri oluştur
        $this->call(PortfolioCategorySeeder::class);

        $categories = PortfolioCategory::all();

        // Web Tasarım Portfolioları
        $webDesignCategory = $categories->where('slug->en', 'web-design')->first();
        if ($webDesignCategory) {
            $this->createWebDesignPortfolios($webDesignCategory);
        }

        // E-Ticaret Portfolioları
        $ecommerceCategory = $categories->where('slug->en', 'e-commerce')->first();
        if ($ecommerceCategory) {
            $this->createEcommercePortfolios($ecommerceCategory);
        }

        // Mobil Uygulama Portfolioları
        $mobileAppCategory = $categories->where('slug->en', 'mobile-application')->first();
        if ($mobileAppCategory) {
            $this->createMobileAppPortfolios($mobileAppCategory);
        }

        // UI/UX Tasarım Portfolioları
        $uiuxCategory = $categories->where('slug->en', 'ui-ux-design')->first();
        if ($uiuxCategory) {
            $this->createUIUXPortfolios($uiuxCategory);
        }

        $totalCount = Portfolio::count();
        $this->command->info("✅ Total {$totalCount} portfolios created for Tenant 2");
    }

    /**
     * Create Web Design portfolios
     */
    private function createWebDesignPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Modern E-Ticaret Sitesi', 'en' => 'Modern E-Commerce Website'],
                'slug' => ['tr' => 'modern-e-ticaret-sitesi', 'en' => 'modern-e-commerce-website'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Kullanıcı dostu arayüzü ile modern ve responsive e-ticaret platformu. Mobil öncelikli tasarım ve hızlı yükleme süreleri ile öne çıkıyor.</p><h3>Özellikler</h3><ul><li>Responsive Design</li><li>SEO Optimized</li><li>Fast Loading</li><li>Payment Integration</li></ul><h3>Kullanılan Teknolojiler</h3><p>Laravel, Vue.js, MySQL, Redis, Stripe API</p>',
                    'en' => '<h2>Project Summary</h2><p>Modern and responsive e-commerce platform with user-friendly interface. Stands out with mobile-first design and fast loading times.</p><h3>Features</h3><ul><li>Responsive Design</li><li>SEO Optimized</li><li>Fast Loading</li><li>Payment Integration</li></ul><h3>Technologies Used</h3><p>Laravel, Vue.js, MySQL, Redis, Stripe API</p>'
                ],
            ],
            [
                'title' => ['tr' => 'Kurumsal Web Sitesi', 'en' => 'Corporate Website'],
                'slug' => ['tr' => 'kurumsal-web-sitesi', 'en' => 'corporate-website'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Profesyonel ve etkileyici kurumsal web sitesi tasarımı. Çok dilli destek ve gelişmiş içerik yönetim sistemi.</p><h3>Özellikler</h3><ul><li>Multi-language Support</li><li>CMS Integration</li><li>Blog System</li><li>Contact Forms</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Professional and impressive corporate website design. Multi-language support and advanced content management system.</p><h3>Features</h3><ul><li>Multi-language Support</li><li>CMS Integration</li><li>Blog System</li><li>Contact Forms</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ Web Design: {$data['title']['en']}");
        }
    }

    /**
     * Create E-Commerce portfolios
     */
    private function createEcommercePortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'B2B E-Ticaret Platformu', 'en' => 'B2B E-Commerce Platform'],
                'slug' => ['tr' => 'b2b-e-ticaret-platformu', 'en' => 'b2b-e-commerce-platform'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Toptan satış için gelişmiş B2B e-ticaret çözümü. Müşteri bazlı fiyatlandırma ve sipariş yönetimi.</p><h3>Özellikler</h3><ul><li>Customer Pricing</li><li>Order Management</li><li>Bulk Ordering</li><li>Credit System</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Advanced B2B e-commerce solution for wholesale. Customer-based pricing and order management.</p><h3>Features</h3><ul><li>Customer Pricing</li><li>Order Management</li><li>Bulk Ordering</li><li>Credit System</li></ul>'
                ],
            ],
            [
                'title' => ['tr' => 'Multi-Vendor Marketplace', 'en' => 'Multi-Vendor Marketplace'],
                'slug' => ['tr' => 'multi-vendor-marketplace', 'en' => 'multi-vendor-marketplace'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Çoklu satıcı e-ticaret platformu. Satıcı paneli, komisyon sistemi ve gelişmiş raporlama.</p><h3>Özellikler</h3><ul><li>Vendor Dashboard</li><li>Commission System</li><li>Advanced Reports</li><li>Review System</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Multi-vendor e-commerce platform. Vendor dashboard, commission system and advanced reporting.</p><h3>Features</h3><ul><li>Vendor Dashboard</li><li>Commission System</li><li>Advanced Reports</li><li>Review System</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ E-Commerce: {$data['title']['en']}");
        }
    }

    /**
     * Create Mobile App portfolios
     */
    private function createMobileAppPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Yemek Sipariş Uygulaması', 'en' => 'Food Ordering App'],
                'slug' => ['tr' => 'yemek-siparis-uygulamasi', 'en' => 'food-ordering-app'],
                'body' => [
                    'tr' => '<h2>Uygulama Özeti</h2><p>iOS ve Android platformları için yemek sipariş mobil uygulaması. Gerçek zamanlı sipariş takibi ve push notification desteği.</p><h3>Özellikler</h3><ul><li>Real-time Tracking</li><li>Push Notifications</li><li>Payment Integration</li><li>Review System</li></ul>',
                    'en' => '<h2>Application Summary</h2><p>Food ordering mobile application for iOS and Android platforms. Real-time order tracking and push notification support.</p><h3>Features</h3><ul><li>Real-time Tracking</li><li>Push Notifications</li><li>Payment Integration</li><li>Review System</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ Mobile App: {$data['title']['en']}");
        }
    }

    /**
     * Create UI/UX Design portfolios
     */
    private function createUIUXPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Finans Uygulaması UI/UX', 'en' => 'Finance App UI/UX'],
                'slug' => ['tr' => 'finans-uygulamasi-ui-ux', 'en' => 'finance-app-ui-ux'],
                'body' => [
                    'tr' => '<h2>Tasarım Özeti</h2><p>Modern finans uygulaması için kullanıcı arayüzü ve deneyim tasarımı. Kullanıcı testleri ile optimize edildi.</p><h3>Tasarım Süreci</h3><ul><li>User Research</li><li>Wireframing</li><li>Prototyping</li><li>User Testing</li></ul>',
                    'en' => '<h2>Design Summary</h2><p>User interface and experience design for modern finance application. Optimized with user testing.</p><h3>Design Process</h3><ul><li>User Research</li><li>Wireframing</li><li>Prototyping</li><li>User Testing</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ UI/UX: {$data['title']['en']}");
        }
    }
}
