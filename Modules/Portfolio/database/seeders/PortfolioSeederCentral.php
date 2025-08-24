<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Portfolio Seeder for Central Database
 * Languages: tr, en, ar
 * Pattern: Same as PageSeederCentral
 */
class PortfolioSeederCentral extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎨 Creating CENTRAL portfolios (tr, en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = Portfolio::count();
        if ($existingCount > 0) {
            $this->command->info("Portfolios already exist in CENTRAL database ({$existingCount} portfolios), skipping seeder...");
            return;
        }
        
        // Kategorilerin var olduğundan emin ol
        $webDesignCategory = PortfolioCategory::where('slug->tr', 'web-tasarim')->first();
        $mobileCategory = PortfolioCategory::where('slug->tr', 'mobil-uygulama')->first();
        $ecommerceCategory = PortfolioCategory::where('slug->tr', 'e-ticaret')->first();
        $corporateCategory = PortfolioCategory::where('slug->tr', 'kurumsal-web')->first();
        
        $this->command->info('🔍 Category Debug:');
        $this->command->info("Web Design Category: " . ($webDesignCategory ? "ID {$webDesignCategory->portfolio_category_id}" : "NOT FOUND"));
        $this->command->info("Mobile Category: " . ($mobileCategory ? "ID {$mobileCategory->portfolio_category_id}" : "NOT FOUND"));
        $this->command->info("E-commerce Category: " . ($ecommerceCategory ? "ID {$ecommerceCategory->portfolio_category_id}" : "NOT FOUND"));
        $this->command->info("Corporate Category: " . ($corporateCategory ? "ID {$corporateCategory->portfolio_category_id}" : "NOT FOUND"));
        $this->command->info("Total categories in DB: " . PortfolioCategory::count());
        
        if (!$webDesignCategory || !$mobileCategory || !$ecommerceCategory || !$corporateCategory) {
            $this->command->error('Portfolio categories not found! Please run PortfolioCategorySeederCentral first.');
            return;
        }
        
        // Mevcut portfolioları sil (sadece boşsa)
        Portfolio::truncate();
        
        
        $this->createWebDesignPortfolio($webDesignCategory);
        $this->createMobileAppPortfolio($mobileCategory);
        $this->createECommercePortfolio($ecommerceCategory);
        $this->createCorporatePortfolio($corporateCategory);
        
        $this->command->info('🎨 CENTRAL portfolios created successfully!');
    }
    
    private function createWebDesignPortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'Türk Bilişim Web Sitesi',
                'en' => 'Türk Bilişim Website',
                'ar' => 'موقع تورك بيليشيم'
            ],
            'slug' => [
                'tr' => 'turk-bilisim-web-sitesi',
                'en' => 'turk-bilisim-website',
                'ar' => 'موقع-تورك-بيليشيم'
            ],
            'body' => [
                'tr' => '<h2>Modern Web Tasarım Projesi</h2>
                    <p>Kurumsal kimliğe uygun, responsive ve kullanıcı dostu web sitesi tasarımı. Modern teknolojiler kullanılarak geliştirilmiş profesyonel bir çözüm.</p>
                    <h3>Özellikler:</h3>
                    <ul>
                        <li>Responsive tasarım</li>
                        <li>SEO uyumlu yapı</li>
                        <li>Hızlı performans</li>
                        <li>Modern arayüz</li>
                    </ul>',
                'en' => '<h2>Modern Web Design Project</h2>
                    <p>Corporate identity-compliant, responsive and user-friendly website design. A professional solution developed using modern technologies.</p>
                    <h3>Features:</h3>
                    <ul>
                        <li>Responsive design</li>
                        <li>SEO-friendly structure</li>
                        <li>Fast performance</li>
                        <li>Modern interface</li>
                    </ul>',
                'ar' => '<h2>مشروع تصميم ويب حديث</h2>
                    <p>تصميم موقع ويب متوافق مع الهوية المؤسسية ومتجاوب وسهل الاستخدام. حل احترافي مطور باستخدام التقنيات الحديثة.</p>
                    <h3>الميزات:</h3>
                    <ul>
                        <li>تصميم متجاوب</li>
                        <li>بنية متوافقة مع SEO</li>
                        <li>أداء سريع</li>
                        <li>واجهة حديثة</li>
                    </ul>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'Türk Bilişim',
            'date' => '2025',
            'url' => 'https://turkbilisim.com.tr',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'Türk Bilişim Web Sitesi',
            'Türk Bilişim Website',
            'موقع تورك بيليشيم',
            'Kurumsal kimliğe uygun, responsive ve kullanıcı dostu web sitesi tasarımı. Modern teknolojiler kullanılarak geliştirilmiş profesyonel bir çözüm.',
            'Corporate identity-compliant, responsive and user-friendly website design. A professional solution developed using modern technologies.',
            'تصميم موقع ويب متوافق مع الهوية المؤسسية ومتجاوب وسهل الاستخدام. حل احترافي مطور باستخدام التقنيات الحديثة.'
        );
        
        $this->command->info('✅ Web Design Portfolio created');
    }
    
    private function createMobileAppPortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'E-Ticaret Mobil Uygulaması',
                'en' => 'E-Commerce Mobile App',
                'ar' => 'تطبيق التجارة الإلكترونية للهاتف المحمول'
            ],
            'slug' => [
                'tr' => 'e-ticaret-mobil-uygulamasi',
                'en' => 'e-commerce-mobile-app',
                'ar' => 'تطبيق-التجارة-الإلكترونية'
            ],
            'body' => [
                'tr' => '<h2>Flutter ile Geliştirilmiş Mobil Uygulama</h2>
                    <p>iOS ve Android için geliştirilmiş modern e-ticaret uygulaması. Kullanıcı dostu arayüz ve güvenli ödeme sistemi.</p>
                    <h3>Teknik Özellikler:</h3>
                    <ul>
                        <li>Flutter Framework</li>
                        <li>Cross-platform uyumluluk</li>
                        <li>Güvenli ödeme entegrasyonu</li>
                        <li>Push notification desteği</li>
                    </ul>',
                'en' => '<h2>Mobile App Developed with Flutter</h2>
                    <p>Modern e-commerce application developed for iOS and Android. User-friendly interface and secure payment system.</p>
                    <h3>Technical Features:</h3>
                    <ul>
                        <li>Flutter Framework</li>
                        <li>Cross-platform compatibility</li>
                        <li>Secure payment integration</li>
                        <li>Push notification support</li>
                    </ul>',
                'ar' => '<h2>تطبيق الهاتف المحمول المطور باستخدام Flutter</h2>
                    <p>تطبيق تجارة إلكترونية حديث مطور لنظامي iOS و Android. واجهة سهلة الاستخدام ونظام دفع آمن.</p>
                    <h3>الميزات التقنية:</h3>
                    <ul>
                        <li>إطار عمل Flutter</li>
                        <li>التوافق متعدد المنصات</li>
                        <li>تكامل دفع آمن</li>
                        <li>دعم الإشعارات الفورية</li>
                    </ul>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'Tech Store',
            'date' => '2024',
            'url' => 'https://play.google.com/store',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'E-Ticaret Mobil Uygulaması',
            'E-Commerce Mobile App',
            'تطبيق التجارة الإلكترونية للهاتف المحمول',
            'iOS ve Android için geliştirilmiş modern e-ticaret uygulaması. Kullanıcı dostu arayüz ve güvenli ödeme sistemi.',
            'Modern e-commerce application developed for iOS and Android. User-friendly interface and secure payment system.',
            'تطبيق تجارة إلكترونية حديث مطور لنظامي iOS و Android. واجهة سهلة الاستخدام ونظام دفع آمن.'
        );
        
        $this->command->info('✅ Mobile App Portfolio created');
    }
    
    private function createECommercePortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'Online Mağaza Sistemi',
                'en' => 'Online Store System',
                'ar' => 'نظام المتجر الإلكتروني'
            ],
            'slug' => [
                'tr' => 'online-magaza-sistemi',
                'en' => 'online-store-system',
                'ar' => 'نظام-المتجر-الإلكتروني'
            ],
            'body' => [
                'tr' => '<h2>Kapsamlı E-Ticaret Çözümü</h2>
                    <p>Özel olarak geliştirilmiş, entegre ödeme sistemli e-ticaret platformu. Stok yönetiminden kargo entegrasyonuna kadar tüm süreçler.</p>
                    <h3>Platform Özellikleri:</h3>
                    <ul>
                        <li>Çoklu ödeme sistemi</li>
                        <li>Stok ve envanter yönetimi</li>
                        <li>Kargo firması entegrasyonları</li>
                        <li>Admin panel ve raporlama</li>
                    </ul>',
                'en' => '<h2>Comprehensive E-Commerce Solution</h2>
                    <p>Custom developed e-commerce platform with integrated payment system. All processes from stock management to cargo integration.</p>
                    <h3>Platform Features:</h3>
                    <ul>
                        <li>Multiple payment system</li>
                        <li>Stock and inventory management</li>
                        <li>Cargo company integrations</li>
                        <li>Admin panel and reporting</li>
                    </ul>',
                'ar' => '<h2>حل شامل للتجارة الإلكترونية</h2>
                    <p>منصة تجارة إلكترونية مطورة خصيصاً مع نظام دفع متكامل. جميع العمليات من إدارة المخزون إلى تكامل الشحن.</p>
                    <h3>ميزات المنصة:</h3>
                    <ul>
                        <li>نظام دفع متعدد</li>
                        <li>إدارة المخزون والمخزون</li>
                        <li>تكامل شركات الشحن</li>
                        <li>لوحة الإدارة والتقارير</li>
                    </ul>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'Online Market',
            'date' => '2024',
            'url' => 'https://onlinemarket.com',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'Online Mağaza Sistemi',
            'Online Store System',
            'نظام المتجر الإلكتروني',
            'Özel olarak geliştirilmiş, entegre ödeme sistemli e-ticaret platformu. Stok yönetiminden kargo entegrasyonuna kadar tüm süreçler.',
            'Custom developed e-commerce platform with integrated payment system. All processes from stock management to cargo integration.',
            'منصة تجارة إلكترونية مطورة خصيصاً مع نظام دفع متكامل. جميع العمليات من إدارة المخزون إلى تكامل الشحن.'
        );
        
        $this->command->info('✅ E-Commerce Portfolio created');
    }
    
    private function createCorporatePortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'Kurumsal Web Portalı',
                'en' => 'Corporate Web Portal',
                'ar' => 'البوابة الإلكترونية المؤسسية'
            ],
            'slug' => [
                'tr' => 'kurumsal-web-portali',
                'en' => 'corporate-web-portal',
                'ar' => 'البوابة-الإلكترونية-المؤسسية'
            ],
            'body' => [
                'tr' => '<h2>Profesyonel Kurumsal Portal</h2>
                    <p>Çalışan yönetimi ve iş süreçlerini destekleyen kurumsal portal çözümü. Güvenli erişim ve rol tabanlı yetkilendirme sistemi.</p>
                    <h3>Portal Modülleri:</h3>
                    <ul>
                        <li>İnsan kaynakları yönetimi</li>
                        <li>Proje takip sistemi</li>
                        <li>Döküman yönetimi</li>
                        <li>İç iletişim platformu</li>
                    </ul>',
                'en' => '<h2>Professional Corporate Portal</h2>
                    <p>Corporate portal solution supporting employee management and business processes. Secure access and role-based authorization system.</p>
                    <h3>Portal Modules:</h3>
                    <ul>
                        <li>Human resources management</li>
                        <li>Project tracking system</li>
                        <li>Document management</li>
                        <li>Internal communication platform</li>
                    </ul>',
                'ar' => '<h2>البوابة المؤسسية الاحترافية</h2>
                    <p>حل البوابة المؤسسية الذي يدعم إدارة الموظفين والعمليات التجارية. نظام وصول آمن وتفويض قائم على الأدوار.</p>
                    <h3>وحدات البوابة:</h3>
                    <ul>
                        <li>إدارة الموارد البشرية</li>
                        <li>نظام تتبع المشاريع</li>
                        <li>إدارة الوثائق</li>
                        <li>منصة الاتصال الداخلي</li>
                    </ul>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'Corporate Inc.',
            'date' => '2024',
            'url' => 'https://corporateportal.com',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'Kurumsal Web Portalı',
            'Corporate Web Portal',
            'البوابة الإلكترونية المؤسسية',
            'Çalışan yönetimi ve iş süreçlerini destekleyen kurumsal portal çözümü. Güvenli erişim ve rol tabanlı yetkilendirme sistemi.',
            'Corporate portal solution supporting employee management and business processes. Secure access and role-based authorization system.',
            'حل البوابة المؤسسية الذي يدعم إدارة الموظفين والعمليات التجارية. نظام وصول آمن وتفويض قائم على الأدوار.'
        );
        
        $this->command->info('✅ Corporate Portal Portfolio created');
    }

    /**
     * Create SEO settings for portfolio
     */
    private function createSeoSetting($portfolio, $titleTr, $titleEn, $titleAr, $descTr, $descEn, $descAr): void
    {
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($portfolio->seoSetting()->exists()) {
            $portfolio->seoSetting()->delete();
        }
        
        $portfolio->seoSetting()->create([
            'titles' => [
                'tr' => $titleTr,
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'descriptions' => [
                'tr' => $descTr,
                'en' => $descEn,
                'ar' => $descAr
            ],
            'og_titles' => [
                'tr' => $titleTr,
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'og_descriptions' => [
                'tr' => $descTr,
                'en' => $descEn,
                'ar' => $descAr
            ],
            'seo_score' => rand(80, 95),
        ]);
    }
}