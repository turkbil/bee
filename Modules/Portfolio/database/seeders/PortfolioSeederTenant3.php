<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;

/**
 * Portfolio Tenant 3 Database Seeder
 *
 * Seeds portfolios for Tenant 3 - Corporate Business theme.
 * Creates demo portfolios with TR/EN translations.
 *
 * @package Modules\Portfolio\Database\Seeders
 */
class PortfolioSeederTenant3 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Portfolio SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('🏢 Portfolio Tenant3: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, portfolio tables do not exist in central!');
            return;
        }

        $this->command->info('🏢 TENANT 3 - Corporate Business Portfolio Seeding');
        $this->command->newLine();

        // Duplicate check
        if (Portfolio::count() > 0) {
            $this->command->info('📋 Portfolios already exist, skipping...');
            return;
        }

        // Önce kategorileri oluştur
        $this->call(PortfolioCategorySeeder::class);

        $categories = PortfolioCategory::all();

        // Kurumsal Yazılım Portfolioları
        $corporateCategory = $categories->where('slug->en', 'corporate-software')->first();
        if ($corporateCategory) {
            $this->createCorporatePortfolios($corporateCategory);
        }

        // Web Tasarım Portfolioları
        $webDesignCategory = $categories->where('slug->en', 'web-design')->first();
        if ($webDesignCategory) {
            $this->createWebDesignPortfolios($webDesignCategory);
        }

        // Dijital Pazarlama Portfolioları
        $digitalMarketingCategory = $categories->where('slug->en', 'digital-marketing')->first();
        if ($digitalMarketingCategory) {
            $this->createDigitalMarketingPortfolios($digitalMarketingCategory);
        }

        $totalCount = Portfolio::count();
        $this->command->info("✅ Total {$totalCount} portfolios created for Tenant 3");
    }

    /**
     * Create Corporate Software portfolios
     */
    private function createCorporatePortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'ERP Yazılımı', 'en' => 'ERP Software'],
                'slug' => ['tr' => 'erp-yazilimi', 'en' => 'erp-software'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Şirket kaynaklarını yöneten entegre ERP sistemi. Muhasebe, stok, insan kaynakları ve CRM modülleri.</p><h3>Modüller</h3><ul><li>Accounting</li><li>Inventory Management</li><li>Human Resources</li><li>CRM</li><li>Reporting</li></ul><h3>Teknolojiler</h3><p>Laravel, PostgreSQL, Redis, Vue.js</p>',
                    'en' => '<h2>Project Summary</h2><p>Integrated ERP system for managing company resources. Accounting, inventory, human resources and CRM modules.</p><h3>Modules</h3><ul><li>Accounting</li><li>Inventory Management</li><li>Human Resources</li><li>CRM</li><li>Reporting</li></ul><h3>Technologies</h3><p>Laravel, PostgreSQL, Redis, Vue.js</p>'
                ],
            ],
            [
                'title' => ['tr' => 'CRM Sistemi', 'en' => 'CRM System'],
                'slug' => ['tr' => 'crm-sistemi', 'en' => 'crm-system'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Müşteri ilişkileri yönetim sistemi. Satış takibi, müşteri yönetimi ve raporlama özellikleri.</p><h3>Özellikler</h3><ul><li>Lead Management</li><li>Sales Pipeline</li><li>Email Integration</li><li>Analytics Dashboard</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Customer relationship management system. Sales tracking, customer management and reporting features.</p><h3>Features</h3><ul><li>Lead Management</li><li>Sales Pipeline</li><li>Email Integration</li><li>Analytics Dashboard</li></ul>'
                ],
            ],
            [
                'title' => ['tr' => 'İnsan Kaynakları Yönetim Sistemi', 'en' => 'HR Management System'],
                'slug' => ['tr' => 'insan-kaynaklari-yonetim-sistemi', 'en' => 'hr-management-system'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Çalışan ve bordro yönetimi için kapsamlı İK sistemi. Özlük işlemleri, izin takibi ve performans değerlendirme.</p><h3>Modüller</h3><ul><li>Employee Management</li><li>Payroll</li><li>Leave Management</li><li>Performance Reviews</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Comprehensive HR system for employee and payroll management. Personnel operations, leave tracking and performance evaluation.</p><h3>Modules</h3><ul><li>Employee Management</li><li>Payroll</li><li>Leave Management</li><li>Performance Reviews</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ Corporate: {$data['title']['en']}");
        }
    }

    /**
     * Create Web Design portfolios
     */
    private function createWebDesignPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Holding Web Sitesi', 'en' => 'Holding Website'],
                'slug' => ['tr' => 'holding-web-sitesi', 'en' => 'holding-website'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Büyük ölçekli holding için kurumsal web sitesi. Şirket grupları, yatırımcı ilişkileri ve kariyer portalı.</p><h3>Özellikler</h3><ul><li>Multi-company Structure</li><li>Investor Relations</li><li>Career Portal</li><li>News & Press</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Corporate website for large-scale holding. Company groups, investor relations and career portal.</p><h3>Features</h3><ul><li>Multi-company Structure</li><li>Investor Relations</li><li>Career Portal</li><li>News & Press</li></ul>'
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
     * Create Digital Marketing portfolios
     */
    private function createDigitalMarketingPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'SEO & Dijital Strateji Projesi', 'en' => 'SEO & Digital Strategy Project'],
                'slug' => ['tr' => 'seo-dijital-strateji-projesi', 'en' => 'seo-digital-strategy-project'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Kapsamlı SEO ve dijital pazarlama stratejisi. 6 ayda organik trafikte %250 artış sağlandı.</p><h3>Sonuçlar</h3><ul><li>%250 Traffic Increase</li><li>Top 3 Rankings for 50+ Keywords</li><li>%180 Conversion Rate Increase</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Comprehensive SEO and digital marketing strategy. Achieved 250% increase in organic traffic in 6 months.</p><h3>Results</h3><ul><li>%250 Traffic Increase</li><li>Top 3 Rankings for 50+ Keywords</li><li>%180 Conversion Rate Increase</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ Digital Marketing: {$data['title']['en']}");
        }
    }
}
