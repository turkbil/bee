<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;

/**
 * Portfolio Tenant 4 Database Seeder
 *
 * Seeds portfolios for Tenant 4 - Creative Portfolio theme.
 * Creates demo portfolios with TR/EN translations.
 *
 * @package Modules\Portfolio\Database\Seeders
 */
class PortfolioSeederTenant4 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Portfolio SADECE tenant database'lerde olmalı
        if (\App\Helpers\TenantHelpers::isCentral()) {
            $this->command->info('🏢 Portfolio Tenant4: sadece tenant database için, atlanıyor...');
            return;
        }

        // Central tenant (ID=1 / laravel database) kontrolü
        if (tenancy()->initialized && tenant('tenancy_db_name') === 'laravel') {
            $this->command->error('❌ Central tenant detected, portfolio tables do not exist in central!');
            return;
        }

        $this->command->info('🏢 TENANT 4 - Creative Portfolio Seeding');
        $this->command->newLine();

        // Duplicate check
        if (Portfolio::count() > 0) {
            $this->command->info('📋 Portfolios already exist, skipping...');
            return;
        }

        // Önce kategorileri oluştur
        $this->call(PortfolioCategorySeeder::class);

        $categories = PortfolioCategory::all();

        // Grafik Tasarım Portfolioları
        $graphicDesignCategory = $categories->where('slug->en', 'graphic-design')->first();
        if ($graphicDesignCategory) {
            $this->createGraphicDesignPortfolios($graphicDesignCategory);
        }

        // UI/UX Tasarım Portfolioları
        $uiuxCategory = $categories->where('slug->en', 'ui-ux-design')->first();
        if ($uiuxCategory) {
            $this->createUIUXPortfolios($uiuxCategory);
        }

        // Web Tasarım Portfolioları
        $webDesignCategory = $categories->where('slug->en', 'web-design')->first();
        if ($webDesignCategory) {
            $this->createWebDesignPortfolios($webDesignCategory);
        }

        // Mobil Uygulama Portfolioları
        $mobileAppCategory = $categories->where('slug->en', 'mobile-application')->first();
        if ($mobileAppCategory) {
            $this->createMobileAppPortfolios($mobileAppCategory);
        }

        $totalCount = Portfolio::count();
        $this->command->info("✅ Total {$totalCount} portfolios created for Tenant 4");
    }

    /**
     * Create Graphic Design portfolios
     */
    private function createGraphicDesignPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Marka Kimliği Tasarımı', 'en' => 'Brand Identity Design'],
                'slug' => ['tr' => 'marka-kimligi-tasarimi', 'en' => 'brand-identity-design'],
                'body' => [
                    'tr' => '<h2>Tasarım Özeti</h2><p>Kapsamlı marka kimliği tasarımı projesi. Logo, kurumsal kimlik ve marka rehberi.</p><h3>Çalışmalar</h3><ul><li>Logo Design</li><li>Brand Guidelines</li><li>Business Card</li><li>Letterhead</li><li>Social Media Templates</li></ul>',
                    'en' => '<h2>Design Summary</h2><p>Comprehensive brand identity design project. Logo, corporate identity and brand guidelines.</p><h3>Deliverables</h3><ul><li>Logo Design</li><li>Brand Guidelines</li><li>Business Card</li><li>Letterhead</li><li>Social Media Templates</li></ul>'
                ],
            ],
            [
                'title' => ['tr' => 'Ürün Ambalaj Tasarımı', 'en' => 'Product Packaging Design'],
                'slug' => ['tr' => 'urun-ambalaj-tasarimi', 'en' => 'product-packaging-design'],
                'body' => [
                    'tr' => '<h2>Tasarım Özeti</h2><p>Modern ve çekici ürün ambalaj tasarımları. Gıda ürünleri için premium ambalaj çözümleri.</p><h3>Özellikler</h3><ul><li>Premium Design</li><li>Print Ready Files</li><li>3D Mockups</li><li>Brand Integration</li></ul>',
                    'en' => '<h2>Design Summary</h2><p>Modern and attractive product packaging designs. Premium packaging solutions for food products.</p><h3>Features</h3><ul><li>Premium Design</li><li>Print Ready Files</li><li>3D Mockups</li><li>Brand Integration</li></ul>'
                ],
            ],
            [
                'title' => ['tr' => 'Sosyal Medya Grafikleri', 'en' => 'Social Media Graphics'],
                'slug' => ['tr' => 'sosyal-medya-grafikleri', 'en' => 'social-media-graphics'],
                'body' => [
                    'tr' => '<h2>Tasarım Özeti</h2><p>Instagram, Facebook ve LinkedIn için profesyonel sosyal medya görselleri. Marka kimliğine uygun tutarlı tasarımlar.</p><h3>İçerikler</h3><ul><li>Instagram Posts & Stories</li><li>Facebook Cover & Posts</li><li>LinkedIn Graphics</li><li>Highlight Icons</li></ul>',
                    'en' => '<h2>Design Summary</h2><p>Professional social media graphics for Instagram, Facebook and LinkedIn. Consistent designs aligned with brand identity.</p><h3>Contents</h3><ul><li>Instagram Posts & Stories</li><li>Facebook Cover & Posts</li><li>LinkedIn Graphics</li><li>Highlight Icons</li></ul>'
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            Portfolio::create(array_merge($data, [
                'category_id' => $category->category_id,
                'is_active' => true,
            ]));
            $this->command->info("✓ Graphic Design: {$data['title']['en']}");
        }
    }

    /**
     * Create UI/UX Design portfolios
     */
    private function createUIUXPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'E-Öğrenme Platform Tasarımı', 'en' => 'E-Learning Platform Design'],
                'slug' => ['tr' => 'e-ogrenme-platform-tasarimi', 'en' => 'e-learning-platform-design'],
                'body' => [
                    'tr' => '<h2>Tasarım Özeti</h2><p>Online eğitim platformu için kullanıcı deneyimi odaklı arayüz tasarımı. Öğrenci ve öğretmen panelleri.</p><h3>Tasarım Süreci</h3><ul><li>User Research & Personas</li><li>Wireframing</li><li>High-Fidelity Mockups</li><li>Interactive Prototypes</li><li>Usability Testing</li></ul>',
                    'en' => '<h2>Design Summary</h2><p>User experience focused interface design for online education platform. Student and teacher dashboards.</p><h3>Design Process</h3><ul><li>User Research & Personas</li><li>Wireframing</li><li>High-Fidelity Mockups</li><li>Interactive Prototypes</li><li>Usability Testing</li></ul>'
                ],
            ],
            [
                'title' => ['tr' => 'Sağlık Uygulaması UX', 'en' => 'Healthcare App UX'],
                'slug' => ['tr' => 'saglik-uygulamasi-ux', 'en' => 'healthcare-app-ux'],
                'body' => [
                    'tr' => '<h2>Tasarım Özeti</h2><p>Hasta ve doktor için mobil sağlık uygulaması tasarımı. Randevu sistemi, reçete takibi ve sağlık raporları.</p><h3>Özellikler</h3><ul><li>Appointment Booking</li><li>Prescription Tracking</li><li>Health Records</li><li>Video Consultation</li></ul>',
                    'en' => '<h2>Design Summary</h2><p>Mobile healthcare application design for patients and doctors. Appointment system, prescription tracking and health reports.</p><h3>Features</h3><ul><li>Appointment Booking</li><li>Prescription Tracking</li><li>Health Records</li><li>Video Consultation</li></ul>'
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

    /**
     * Create Web Design portfolios
     */
    private function createWebDesignPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Kişisel Portfolio Sitesi', 'en' => 'Personal Portfolio Website'],
                'slug' => ['tr' => 'kisisel-portfolio-sitesi', 'en' => 'personal-portfolio-website'],
                'body' => [
                    'tr' => '<h2>Proje Özeti</h2><p>Yaratıcı profesyoneller için etkileyici portfolio web sitesi. Animasyonlar ve interaktif öğelerle zenginleştirildi.</p><h3>Özellikler</h3><ul><li>Smooth Animations</li><li>Interactive Elements</li><li>Project Gallery</li><li>Contact Form</li></ul>',
                    'en' => '<h2>Project Summary</h2><p>Impressive portfolio website for creative professionals. Enriched with animations and interactive elements.</p><h3>Features</h3><ul><li>Smooth Animations</li><li>Interactive Elements</li><li>Project Gallery</li><li>Contact Form</li></ul>'
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
     * Create Mobile App portfolios
     */
    private function createMobileAppPortfolios(PortfolioCategory $category): void
    {
        $portfolios = [
            [
                'title' => ['tr' => 'Fitness Takip Uygulaması', 'en' => 'Fitness Tracking App'],
                'slug' => ['tr' => 'fitness-takip-uygulamasi', 'en' => 'fitness-tracking-app'],
                'body' => [
                    'tr' => '<h2>Uygulama Özeti</h2><p>Spor ve sağlık aktivitelerini takip eden mobil uygulama. Egzersiz planları, kalori sayacı ve ilerleme grafikleri.</p><h3>Özellikler</h3><ul><li>Workout Plans</li><li>Calorie Counter</li><li>Progress Charts</li><li>Social Features</li></ul>',
                    'en' => '<h2>Application Summary</h2><p>Mobile application for tracking sports and health activities. Exercise plans, calorie counter and progress charts.</p><h3>Features</h3><ul><li>Workout Plans</li><li>Calorie Counter</li><li>Progress Charts</li><li>Social Features</li></ul>'
                ],
            ],
            [
                'title' => ['tr' => 'Meditasyon Uygulaması', 'en' => 'Meditation App'],
                'slug' => ['tr' => 'meditasyon-uygulamasi', 'en' => 'meditation-app'],
                'body' => [
                    'tr' => '<h2>Uygulama Özeti</h2><p>Günlük meditasyon ve farkındalık uygulaması. Rehberli meditasyonlar, nefes egzersizleri ve uyku hikayeleri.</p><h3>Özellikler</h3><ul><li>Guided Meditations</li><li>Breathing Exercises</li><li>Sleep Stories</li><li>Progress Tracking</li></ul>',
                    'en' => '<h2>Application Summary</h2><p>Daily meditation and mindfulness application. Guided meditations, breathing exercises and sleep stories.</p><h3>Features</h3><ul><li>Guided Meditations</li><li>Breathing Exercises</li><li>Sleep Stories</li><li>Progress Tracking</li></ul>'
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
}
