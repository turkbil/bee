<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Models\SeoSetting;

/**
 * Portfolio Category Seeder for Central Database
 * Languages: tr, en, ar
 * Pattern: Same as PageSeederCentral
 */
class PortfolioCategorySeederCentral extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎯 Creating CENTRAL portfolio categories (tr, en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = PortfolioCategory::count();
        if ($existingCount > 0) {
            $this->command->info("Portfolio categories already exist in CENTRAL database ({$existingCount} categories), skipping seeder...");
            return;
        }
        
        // Mevcut verileri sil (foreign key sırası önemli)
        // Önce portfolios tablosunu sil (foreign key reference)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (\Schema::hasTable('portfolios')) {
            \DB::table('portfolios')->truncate();
        }
        PortfolioCategory::truncate();
        SeoSetting::where('seoable_type', 'like', '%PortfolioCategory%')->delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Ana kategorileri oluştur
        $webDesignCategory = $this->createWebDesignCategory();
        $mobileAppCategory = $this->createMobileAppCategory();
        $eCommerceCategory = $this->createECommerceCategory();
        $corporateWebCategory = $this->createCorporateWebCategory();
        
        // Alt kategorileri oluştur
        $this->createWebDesignSubCategories($webDesignCategory);
        $this->createMobileAppSubCategories($mobileAppCategory);
        $this->createECommerceSubCategories($eCommerceCategory);
        $this->createCorporateWebSubCategories($corporateWebCategory);
        
        $this->command->info('🎯 CENTRAL portfolio categories with subcategories created successfully!');
    }
    
    private function createWebDesignCategory(): PortfolioCategory
    {
        $category = PortfolioCategory::create([
            'title' => [
                'tr' => 'Web Tasarım',
                'en' => 'Web Design',
                'ar' => 'تصميم الويب'
            ],
            'slug' => [
                'tr' => 'web-tasarim',
                'en' => 'web-design',
                'ar' => 'تصميم-الويب'
            ],
            'body' => [
                'tr' => '<h2>Web Tasarım Projelerimiz</h2>
                    <p>Modern web tasarım anlayışı ile kullanıcı deneyimi odaklı projeler üretiyoruz. Responsive tasarım, SEO uyumlu kodlama ve hızlı performans önceliklerimizdir.</p>
                    <h3>Hizmetlerimiz:</h3>
                    <ul>
                        <li>Kurumsal web siteleri</li>
                        <li>E-ticaret platformları</li>
                        <li>Landing page tasarımları</li>
                        <li>Blog ve içerik siteleri</li>
                    </ul>',
                'en' => '<h2>Our Web Design Projects</h2>
                    <p>We produce user experience-focused projects with modern web design approach. Responsive design, SEO-compatible coding and fast performance are our priorities.</p>
                    <h3>Our Services:</h3>
                    <ul>
                        <li>Corporate websites</li>
                        <li>E-commerce platforms</li>
                        <li>Landing page designs</li>
                        <li>Blog and content sites</li>
                    </ul>',
                'ar' => '<h2>مشاريع تصميم الويب لدينا</h2>
                    <p>نحن ننتج مشاريع تركز على تجربة المستخدم مع نهج تصميم الويب الحديث. التصميم المتجاوب والترميز المتوافق مع SEO والأداء السريع هي أولوياتنا.</p>
                    <h3>خدماتنا:</h3>
                    <ul>
                        <li>مواقع الويب المؤسسية</li>
                        <li>منصات التجارة الإلكترونية</li>
                        <li>تصاميم صفحات الهبوط</li>
                        <li>مواقع المدونات والمحتوى</li>
                    </ul>'
            ],
            'order' => 1,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $category,
            'Web Tasarım',
            'Web Design',
            'تصميم الويب',
            'Modern web tasarım anlayışı ile kullanıcı deneyimi odaklı projeler üretiyoruz. Responsive tasarım, SEO uyumlu kodlama ve hızlı performans önceliklerimizdir.',
            'We produce user experience-focused projects with modern web design approach. Responsive design, SEO-compatible coding and fast performance are our priorities.',
            'نحن ننتج مشاريع تركز على تجربة المستخدم مع نهج تصميم الويب الحديث. التصميم المتجاوب والترميز المتوافق مع SEO والأداء السريع هي أولوياتنا.'
        );
        
        $this->command->info('✅ Web Design Category created');
        
        return $category;
    }
    
    private function createMobileAppCategory(): PortfolioCategory
    {
        $category = PortfolioCategory::create([
            'title' => [
                'tr' => 'Mobil Uygulama',
                'en' => 'Mobile Application',
                'ar' => 'تطبيق الهاتف المحمول'
            ],
            'slug' => [
                'tr' => 'mobil-uygulama',
                'en' => 'mobile-application',
                'ar' => 'تطبيق-الهاتف-المحمول'
            ],
            'body' => [
                'tr' => '<h2>Mobil Uygulama Geliştirme</h2>
                    <p>iOS ve Android platformları için native ve cross-platform uygulamalar geliştiriyoruz. Modern teknolojiler kullanarak kullanıcı dostu mobil deneyimler sunuyoruz.</p>
                    <h3>Teknolojiler:</h3>
                    <ul>
                        <li>Native iOS (Swift)</li>
                        <li>Native Android (Kotlin)</li>
                        <li>Cross-platform (Flutter, React Native)</li>
                        <li>Progressive Web Apps (PWA)</li>
                    </ul>',
                'en' => '<h2>Mobile Application Development</h2>
                    <p>We develop native and cross-platform applications for iOS and Android platforms. We offer user-friendly mobile experiences using modern technologies.</p>
                    <h3>Technologies:</h3>
                    <ul>
                        <li>Native iOS (Swift)</li>
                        <li>Native Android (Kotlin)</li>
                        <li>Cross-platform (Flutter, React Native)</li>
                        <li>Progressive Web Apps (PWA)</li>
                    </ul>',
                'ar' => '<h2>تطوير تطبيقات الهاتف المحمول</h2>
                    <p>نقوم بتطوير تطبيقات أصلية ومتعددة المنصات لمنصات iOS و Android. نقدم تجارب محمولة سهلة الاستخدام باستخدام التقنيات الحديثة.</p>
                    <h3>التقنيات:</h3>
                    <ul>
                        <li>iOS أصلي (Swift)</li>
                        <li>Android أصلي (Kotlin)</li>
                        <li>متعدد المنصات (Flutter، React Native)</li>
                        <li>تطبيقات الويب التقدمية (PWA)</li>
                    </ul>'
            ],
            'order' => 2,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $category,
            'Mobil Uygulama',
            'Mobile Application',
            'تطبيق الهاتف المحمول',
            'iOS ve Android platformları için native ve cross-platform uygulamalar geliştiriyoruz. Modern teknolojiler kullanarak kullanıcı dostu mobil deneyimler sunuyoruz.',
            'We develop native and cross-platform applications for iOS and Android platforms. We offer user-friendly mobile experiences using modern technologies.',
            'نقوم بتطوير تطبيقات أصلية ومتعددة المنصات لمنصات iOS و Android. نقدم تجارب محمولة سهلة الاستخدام باستخدام التقنيات الحديثة.'
        );
        
        $this->command->info('✅ Mobile Application Category created');
        
        return $category;
    }
    
    private function createECommerceCategory(): PortfolioCategory
    {
        $category = PortfolioCategory::create([
            'title' => [
                'tr' => 'E-Ticaret',
                'en' => 'E-Commerce',
                'ar' => 'التجارة الإلكترونية'
            ],
            'slug' => [
                'tr' => 'e-ticaret',
                'en' => 'e-commerce',
                'ar' => 'التجارة-الإلكترونية'
            ],
            'body' => [
                'tr' => '<h2>E-Ticaret Çözümleri</h2>
                    <p>Güvenli ve kullanıcı dostu e-ticaret sistemleri kuruyoruz. Özel yazılımlardan hazır platformlara kadar geniş çözüm yelpazesi sunuyoruz.</p>
                    <h3>Çözümlerimiz:</h3>
                    <ul>
                        <li>Özel e-ticaret yazılımları</li>
                        <li>Ödeme sistemi entegrasyonları</li>
                        <li>Stok ve sipariş yönetimi</li>
                        <li>B2B ve B2C çözümleri</li>
                    </ul>',
                'en' => '<h2>E-Commerce Solutions</h2>
                    <p>We build secure and user-friendly e-commerce systems. We offer a wide range of solutions from custom software to ready-made platforms.</p>
                    <h3>Our Solutions:</h3>
                    <ul>
                        <li>Custom e-commerce software</li>
                        <li>Payment system integrations</li>
                        <li>Stock and order management</li>
                        <li>B2B and B2C solutions</li>
                    </ul>',
                'ar' => '<h2>حلول التجارة الإلكترونية</h2>
                    <p>نبني أنظمة تجارة إلكترونية آمنة وسهلة الاستخدام. نقدم مجموعة واسعة من الحلول من البرمجيات المخصصة إلى المنصات الجاهزة.</p>
                    <h3>حلولنا:</h3>
                    <ul>
                        <li>برمجيات التجارة الإلكترونية المخصصة</li>
                        <li>تكامل أنظمة الدفع</li>
                        <li>إدارة المخزون والطلبات</li>
                        <li>حلول B2B و B2C</li>
                    </ul>'
            ],
            'order' => 3,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $category,
            'E-Ticaret',
            'E-Commerce',
            'التجارة الإلكترونية',
            'Güvenli ve kullanıcı dostu e-ticaret sistemleri kuruyoruz. Özel yazılımlardan hazır platformlara kadar geniş çözüm yelpazesi sunuyoruz.',
            'We build secure and user-friendly e-commerce systems. We offer a wide range of solutions from custom software to ready-made platforms.',
            'نبني أنظمة تجارة إلكترونية آمنة وسهلة الاستخدام. نقدم مجموعة واسعة من الحلول من البرمجيات المخصصة إلى المنصات الجاهزة.'
        );
        
        $this->command->info('✅ E-Commerce Category created');
        
        return $category;
    }
    
    private function createCorporateWebCategory(): PortfolioCategory
    {
        $category = PortfolioCategory::create([
            'title' => [
                'tr' => 'Kurumsal Web',
                'en' => 'Corporate Web',
                'ar' => 'الويب المؤسسي'
            ],
            'slug' => [
                'tr' => 'kurumsal-web',
                'en' => 'corporate-web',
                'ar' => 'الويب-المؤسسي'
            ],
            'body' => [
                'tr' => '<h2>Kurumsal Web Siteleri</h2>
                    <p>Kurumsal kimliğinizi yansıtan profesyonel web siteleri tasarlıyoruz. İş süreçlerinizi destekleyen güçlü altyapı çözümleri sunuyoruz.</p>
                    <h3>Özellikler:</h3>
                    <ul>
                        <li>Kurumsal tasarım ve kimlik</li>
                        <li>İçerik yönetim sistemleri</li>
                        <li>Güvenli admin panelleri</li>
                        <li>SEO ve performans optimizasyonu</li>
                    </ul>',
                'en' => '<h2>Corporate Websites</h2>
                    <p>We design professional websites that reflect your corporate identity. We offer powerful infrastructure solutions that support your business processes.</p>
                    <h3>Features:</h3>
                    <ul>
                        <li>Corporate design and identity</li>
                        <li>Content management systems</li>
                        <li>Secure admin panels</li>
                        <li>SEO and performance optimization</li>
                    </ul>',
                'ar' => '<h2>مواقع الويب المؤسسية</h2>
                    <p>نحن نصمم مواقع ويب احترافية تعكس هويتك المؤسسية. نقدم حلول بنية تحتية قوية تدعم عمليات عملك.</p>
                    <h3>الميزات:</h3>
                    <ul>
                        <li>التصميم والهوية المؤسسية</li>
                        <li>أنظمة إدارة المحتوى</li>
                        <li>لوحات إدارة آمنة</li>
                        <li>تحسين SEO والأداء</li>
                    </ul>'
            ],
            'order' => 4,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $category,
            'Kurumsal Web',
            'Corporate Web',
            'الويب المؤسسي',
            'Kurumsal kimliğinizi yansıtan profesyonel web siteleri tasarılıyoruz. İş süreçlerinizi destekleyen güçlü altyapı çözümleri sunuyoruz.',
            'We design professional websites that reflect your corporate identity. We offer powerful infrastructure solutions that support your business processes.',
            'نحن نصمم مواقع ويب احترافية تعكس هويتك المؤسسية. نقدم حلول بنية تحتية قوية تدعم عمليات عملك.'
        );
        
        $this->command->info('✅ Corporate Web Category created');
        
        return $category;
    }

    /**
     * Web Tasarım alt kategorileri
     */
    private function createWebDesignSubCategories(PortfolioCategory $parentCategory): void
    {
        // Frontend Development
        $frontendCategory = PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'Frontend Geliştirme',
                'en' => 'Frontend Development',
                'ar' => 'تطوير الواجهة الأمامية'
            ],
            'slug' => [
                'tr' => 'frontend-gelistirme',
                'en' => 'frontend-development',
                'ar' => 'تطوير-الواجهة-الأمامية'
            ],
            'body' => [
                'tr' => '<p>React, Vue.js, Angular gibi modern teknolojilerle kullanıcı dostu arayüzler geliştiriyoruz.</p>',
                'en' => '<p>We develop user-friendly interfaces with modern technologies like React, Vue.js, Angular.</p>',
                'ar' => '<p>نطور واجهات سهلة الاستخدام بتقنيات حديثة مثل React و Vue.js و Angular.</p>'
            ],
            'order' => 1,
            'is_active' => true,
        ]);

        // Backend Development
        $backendCategory = PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'Backend Geliştirme',
                'en' => 'Backend Development',
                'ar' => 'تطوير الخلفية'
            ],
            'slug' => [
                'tr' => 'backend-gelistirme',
                'en' => 'backend-development',
                'ar' => 'تطوير-الخلفية'
            ],
            'body' => [
                'tr' => '<p>Laravel, Node.js, Python ile güçlü ve güvenli backend sistemleri kuruyoruz.</p>',
                'en' => '<p>We build powerful and secure backend systems with Laravel, Node.js, Python.</p>',
                'ar' => '<p>نبني أنظمة خلفية قوية وآمنة باستخدام Laravel و Node.js و Python.</p>'
            ],
            'order' => 2,
            'is_active' => true,
        ]);

        $this->command->info('✅ Web Design subcategories created');
    }

    /**
     * Mobil Uygulama alt kategorileri
     */
    private function createMobileAppSubCategories(PortfolioCategory $parentCategory): void
    {
        // iOS Development
        PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'iOS Geliştirme',
                'en' => 'iOS Development',
                'ar' => 'تطوير iOS'
            ],
            'slug' => [
                'tr' => 'ios-gelistirme',
                'en' => 'ios-development',
                'ar' => 'تطوير-ios'
            ],
            'body' => [
                'tr' => '<p>Swift ve Objective-C ile native iOS uygulamaları geliştiriyoruz.</p>',
                'en' => '<p>We develop native iOS applications with Swift and Objective-C.</p>',
                'ar' => '<p>نطور تطبيقات iOS الأصلية باستخدام Swift و Objective-C.</p>'
            ],
            'order' => 1,
            'is_active' => true,
        ]);

        // Android Development
        PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'Android Geliştirme',
                'en' => 'Android Development',
                'ar' => 'تطوير Android'
            ],
            'slug' => [
                'tr' => 'android-gelistirme',
                'en' => 'android-development',
                'ar' => 'تطوير-android'
            ],
            'body' => [
                'tr' => '<p>Kotlin ve Java ile native Android uygulamaları geliştiriyoruz.</p>',
                'en' => '<p>We develop native Android applications with Kotlin and Java.</p>',
                'ar' => '<p>نطور تطبيقات Android الأصلية باستخدام Kotlin و Java.</p>'
            ],
            'order' => 2,
            'is_active' => true,
        ]);

        $this->command->info('✅ Mobile App subcategories created');
    }

    /**
     * E-Ticaret alt kategorileri
     */
    private function createECommerceSubCategories(PortfolioCategory $parentCategory): void
    {
        // B2B Solutions
        PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'B2B Çözümleri',
                'en' => 'B2B Solutions',
                'ar' => 'حلول B2B'
            ],
            'slug' => [
                'tr' => 'b2b-cozumleri',
                'en' => 'b2b-solutions',
                'ar' => 'حلول-b2b'
            ],
            'body' => [
                'tr' => '<p>İşletmeler arası e-ticaret platformları ve toptan satış sistemleri.</p>',
                'en' => '<p>Business-to-business e-commerce platforms and wholesale systems.</p>',
                'ar' => '<p>منصات التجارة الإلكترونية بين الشركات وأنظمة البيع بالجملة.</p>'
            ],
            'order' => 1,
            'is_active' => true,
        ]);

        // B2C Solutions
        PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'B2C Çözümleri',
                'en' => 'B2C Solutions',
                'ar' => 'حلول B2C'
            ],
            'slug' => [
                'tr' => 'b2c-cozumleri',
                'en' => 'b2c-solutions',
                'ar' => 'حلول-b2c'
            ],
            'body' => [
                'tr' => '<p>Perakende satış odaklı e-ticaret platformları ve mağaza çözümleri.</p>',
                'en' => '<p>Retail-focused e-commerce platforms and store solutions.</p>',
                'ar' => '<p>منصات التجارة الإلكترونية المركزة على التجزئة وحلول المتاجر.</p>'
            ],
            'order' => 2,
            'is_active' => true,
        ]);

        $this->command->info('✅ E-Commerce subcategories created');
    }

    /**
     * Kurumsal Web alt kategorileri
     */
    private function createCorporateWebSubCategories(PortfolioCategory $parentCategory): void
    {
        // CMS Development
        PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'CMS Geliştirme',
                'en' => 'CMS Development',
                'ar' => 'تطوير نظام إدارة المحتوى'
            ],
            'slug' => [
                'tr' => 'cms-gelistirme',
                'en' => 'cms-development',
                'ar' => 'تطوير-نظام-إدارة-المحتوى'
            ],
            'body' => [
                'tr' => '<p>Özel içerik yönetim sistemleri ve WordPress çözümleri.</p>',
                'en' => '<p>Custom content management systems and WordPress solutions.</p>',
                'ar' => '<p>أنظمة إدارة المحتوى المخصصة وحلول WordPress.</p>'
            ],
            'order' => 1,
            'is_active' => true,
        ]);

        // API Development
        PortfolioCategory::create([
            'parent_id' => $parentCategory->portfolio_category_id,
            'title' => [
                'tr' => 'API Geliştirme',
                'en' => 'API Development',
                'ar' => 'تطوير API'
            ],
            'slug' => [
                'tr' => 'api-gelistirme',
                'en' => 'api-development',
                'ar' => 'تطوير-api'
            ],
            'body' => [
                'tr' => '<p>RESTful API ve GraphQL entegrasyonları ile sistem bağlantıları.</p>',
                'en' => '<p>System integrations with RESTful API and GraphQL integrations.</p>',
                'ar' => '<p>تكامل الأنظمة مع تكامل RESTful API و GraphQL.</p>'
            ],
            'order' => 2,
            'is_active' => true,
        ]);

        $this->command->info('✅ Corporate Web subcategories created');
    }

    /**
     * Create SEO settings for portfolio category
     */
    private function createSeoSetting($category, $titleTr, $titleEn, $titleAr, $descTr, $descEn, $descAr): void
    {
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($category->seoSetting()->exists()) {
            $category->seoSetting()->delete();
        }
        
        $category->seoSetting()->create([
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
            'keywords' => [
                'tr' => ['kategori', 'portfolio', 'hizmet', 'teknoloji', 'çözüm'],
                'en' => ['category', 'portfolio', 'service', 'technology', 'solution'],
                'ar' => ['فئة', 'محفظة', 'خدمة', 'تكنولوجيا', 'حل']
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
            'available_languages' => ['tr', 'en', 'ar'],
            'default_language' => 'tr',
            'seo_score' => rand(80, 95),
        ]);
    }
}