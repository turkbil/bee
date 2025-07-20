<?php
namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder hem central hem tenant'ta çalışabilir
        if (TenantHelpers::isCentral()) {
            $this->command->info('PortfolioSeeder central veritabanında çalışıyor...');
        } else {
            $this->command->info('PortfolioSeeder tenant veritabanında çalışıyor...');
        }
        
        // Tablo var mı kontrol et
        if (!Schema::hasTable('portfolio_categories') || !Schema::hasTable('portfolios')) {
            $this->command->info('Portfolio tabloları bulunamadı, işlem atlanıyor...');
            return;
        }

        $faker = Faker::create('tr_TR');
        
        // Önce kategorileri oluştur (yoksa)
        $this->call(\Modules\Portfolio\Database\Seeders\PortfolioCategorySeeder::class);
        
        // Kategorileri al
        $webDesignCategory = PortfolioCategory::where('slug->tr', 'web-tasarim')->first();
        $mobileAppCategory = PortfolioCategory::where('slug->tr', 'mobil-uygulama')->first();
        $ecommerceCategory = PortfolioCategory::where('slug->tr', 'e-ticaret')->first();

        // JSON formatında çoklu dil portfolio verileri
        $portfolios = [
            // Web Tasarım Projeleri
            [
                'category_id' => $webDesignCategory?->portfolio_category_id ?? 1,
                'title' => [
                    'tr' => 'Kurumsal Web Sitesi - ABC Holding',
                    'en' => 'Corporate Website - ABC Holding',
                    'ar' => 'موقع الشركة - ABC القابضة'
                ],
                'slug' => [
                    'tr' => 'kurumsal-web-sitesi-abc-holding',
                    'en' => 'corporate-website-abc-holding',
                    'ar' => 'موقع-الشركة-abc-القابضة'
                ],
                'body' => [
                    'tr' => '<div class="portfolio-content">
                        <h2>Proje Hakkında</h2>
                        <p>ABC Holding için geliştirdiğimiz kurumsal web sitesi, modern tasarım anlayışı ve kullanıcı deneyimi odaklı yaklaşımımızın mükemmel bir örneğidir.</p>
                        
                        <h3>Özellikler</h3>
                        <ul>
                            <li>Tamamen responsive tasarım</li>
                            <li>Çok dilli altyapı (TR, EN, AR)</li>
                            <li>Yönetim paneli ile kolay içerik yönetimi</li>
                            <li>SEO optimizasyonu</li>
                            <li>Hızlı yükleme süreleri</li>
                        </ul>
                        
                        <h3>Kullanılan Teknolojiler</h3>
                        <p>Laravel 11, Vue.js, Tailwind CSS, MySQL</p>
                    </div>',
                    'en' => '<div class="portfolio-content">
                        <h2>About Project</h2>
                        <p>The corporate website we developed for ABC Holding is a perfect example of our modern design approach and user experience-focused approach.</p>
                        
                        <h3>Features</h3>
                        <ul>
                            <li>Fully responsive design</li>
                            <li>Multilingual infrastructure (TR, EN, AR)</li>
                            <li>Easy content management with admin panel</li>
                            <li>SEO optimization</li>
                            <li>Fast loading times</li>
                        </ul>
                        
                        <h3>Technologies Used</h3>
                        <p>Laravel 11, Vue.js, Tailwind CSS, MySQL</p>
                    </div>',
                    'ar' => '<div class="portfolio-content">
                        <h2>حول المشروع</h2>
                        <p>الموقع الإلكتروني للشركة الذي طورناه لشركة ABC القابضة هو مثال مثالي لنهج التصميم الحديث ونهجنا الذي يركز على تجربة المستخدم.</p>
                        
                        <h3>الميزات</h3>
                        <ul>
                            <li>تصميم متجاوب بالكامل</li>
                            <li>بنية تحتية متعددة اللغات (TR، EN، AR)</li>
                            <li>إدارة محتوى سهلة مع لوحة الإدارة</li>
                            <li>تحسين محركات البحث</li>
                            <li>أوقات تحميل سريعة</li>
                        </ul>
                        
                        <h3>التقنيات المستخدمة</h3>
                        <p>Laravel 11، Vue.js، Tailwind CSS، MySQL</p>
                    </div>'
                ],
                'client' => 'ABC Holding A.Ş.',
                'date' => '2024-03',
                'url' => 'https://www.abcholding.com',
                'metakey' => [
                    'tr' => 'kurumsal web sitesi, holding, responsive tasarım',
                    'en' => 'corporate website, holding, responsive design',
                    'ar' => 'موقع الشركة، القابضة، تصميم متجاوب'
                ],
                'metadesc' => [
                    'tr' => 'ABC Holding için geliştirdiğimiz modern kurumsal web sitesi projesi',
                    'en' => 'Modern corporate website project developed for ABC Holding',
                    'ar' => 'مشروع موقع الشركة الحديث الذي تم تطويره لشركة ABC القابضة'
                ]
            ],
            // Mobil Uygulama Projeleri
            [
                'category_id' => $mobileAppCategory?->portfolio_category_id ?? 2,
                'title' => [
                    'tr' => 'Yemek Sipariş Uygulaması - FastFood',
                    'en' => 'Food Ordering App - FastFood',
                    'ar' => 'تطبيق طلب الطعام - FastFood'
                ],
                'slug' => [
                    'tr' => 'yemek-siparis-uygulamasi-fastfood',
                    'en' => 'food-ordering-app-fastfood',
                    'ar' => 'تطبيق-طلب-الطعام-fastfood'
                ],
                'body' => [
                    'tr' => '<div class="portfolio-content">
                        <h2>Proje Hakkında</h2>
                        <p>FastFood yemek sipariş uygulaması, kullanıcıların kolayca yemek siparişi verebileceği, takip edebileceği ve ödeme yapabileceği modern bir mobil uygulamadır.</p>
                        
                        <h3>Uygulama Özellikleri</h3>
                        <ul>
                            <li>Gerçek zamanlı sipariş takibi</li>
                            <li>Çoklu ödeme seçenekleri</li>
                            <li>Push notification desteği</li>
                            <li>Favori restoran ve yemek listesi</li>
                            <li>Detaylı filtre ve arama özellikleri</li>
                        </ul>
                        
                        <h3>Platform</h3>
                        <p>iOS (Swift) ve Android (Kotlin) native uygulamalar</p>
                    </div>',
                    'en' => '<div class="portfolio-content">
                        <h2>About Project</h2>
                        <p>FastFood ordering app is a modern mobile application where users can easily order food, track and make payments.</p>
                        
                        <h3>App Features</h3>
                        <ul>
                            <li>Real-time order tracking</li>
                            <li>Multiple payment options</li>
                            <li>Push notification support</li>
                            <li>Favorite restaurant and food list</li>
                            <li>Detailed filter and search features</li>
                        </ul>
                        
                        <h3>Platform</h3>
                        <p>iOS (Swift) and Android (Kotlin) native applications</p>
                    </div>',
                    'ar' => '<div class="portfolio-content">
                        <h2>حول المشروع</h2>
                        <p>تطبيق طلب الطعام FastFood هو تطبيق محمول حديث حيث يمكن للمستخدمين طلب الطعام بسهولة وتتبعه والدفع.</p>
                        
                        <h3>ميزات التطبيق</h3>
                        <ul>
                            <li>تتبع الطلبات في الوقت الفعلي</li>
                            <li>خيارات دفع متعددة</li>
                            <li>دعم الإشعارات الفورية</li>
                            <li>قائمة المطاعم والأطعمة المفضلة</li>
                            <li>ميزات البحث والتصفية المفصلة</li>
                        </ul>
                        
                        <h3>المنصة</h3>
                        <p>تطبيقات أصلية لـ iOS (Swift) و Android (Kotlin)</p>
                    </div>'
                ],
                'client' => 'FastFood Technology Ltd.',
                'date' => '2024-01',
                'url' => null,
                'metakey' => [
                    'tr' => 'mobil uygulama, yemek siparişi, ios, android',
                    'en' => 'mobile app, food ordering, ios, android',
                    'ar' => 'تطبيق محمول، طلب طعام، ios، android'
                ],
                'metadesc' => [
                    'tr' => 'FastFood için geliştirdiğimiz yemek sipariş mobil uygulaması',
                    'en' => 'Food ordering mobile application developed for FastFood',
                    'ar' => 'تطبيق طلب الطعام عبر الهاتف المحمول الذي تم تطويره لـ FastFood'
                ]
            ],
            // E-Ticaret Projeleri
            [
                'category_id' => $ecommerceCategory?->portfolio_category_id ?? 3,
                'title' => [
                    'tr' => 'B2B E-Ticaret Platformu - TechWholesale',
                    'en' => 'B2B E-Commerce Platform - TechWholesale',
                    'ar' => 'منصة التجارة الإلكترونية B2B - TechWholesale'
                ],
                'slug' => [
                    'tr' => 'b2b-e-ticaret-platformu-techwholesale',
                    'en' => 'b2b-e-commerce-platform-techwholesale',
                    'ar' => 'منصة-التجارة-الإلكترونية-b2b-techwholesale'
                ],
                'body' => [
                    'tr' => '<div class="portfolio-content">
                        <h2>Proje Hakkında</h2>
                        <p>TechWholesale için geliştirdiğimiz B2B e-ticaret platformu, teknoloji ürünleri toptan satışı için özel olarak tasarlanmış kapsamlı bir çözümdür.</p>
                        
                        <h3>Platform Özellikleri</h3>
                        <ul>
                            <li>Bayi yönetim sistemi</li>
                            <li>Kademeli fiyatlandırma yapısı</li>
                            <li>Stok ve sipariş yönetimi</li>
                            <li>ERP entegrasyonu</li>
                            <li>Detaylı raporlama ve analitik</li>
                            <li>Çoklu dil ve para birimi desteği</li>
                        </ul>
                        
                        <h3>Entegrasyonlar</h3>
                        <p>SAP ERP, Paraşüt, İyzico, Kargo API\'leri</p>
                    </div>',
                    'en' => '<div class="portfolio-content">
                        <h2>About Project</h2>
                        <p>The B2B e-commerce platform we developed for TechWholesale is a comprehensive solution specifically designed for wholesale technology products.</p>
                        
                        <h3>Platform Features</h3>
                        <ul>
                            <li>Dealer management system</li>
                            <li>Tiered pricing structure</li>
                            <li>Stock and order management</li>
                            <li>ERP integration</li>
                            <li>Detailed reporting and analytics</li>
                            <li>Multi-language and currency support</li>
                        </ul>
                        
                        <h3>Integrations</h3>
                        <p>SAP ERP, Paraşüt, İyzico, Cargo APIs</p>
                    </div>',
                    'ar' => '<div class="portfolio-content">
                        <h2>حول المشروع</h2>
                        <p>منصة التجارة الإلكترونية B2B التي طورناها لـ TechWholesale هي حل شامل مصمم خصيصًا لمنتجات التكنولوجيا بالجملة.</p>
                        
                        <h3>ميزات المنصة</h3>
                        <ul>
                            <li>نظام إدارة الموزعين</li>
                            <li>هيكل التسعير المتدرج</li>
                            <li>إدارة المخزون والطلبات</li>
                            <li>تكامل ERP</li>
                            <li>تقارير وتحليلات مفصلة</li>
                            <li>دعم متعدد اللغات والعملات</li>
                        </ul>
                        
                        <h3>التكاملات</h3>
                        <p>SAP ERP، Paraşüt، İyzico، APIs الشحن</p>
                    </div>'
                ],
                'client' => 'TechWholesale Teknoloji A.Ş.',
                'date' => '2023-11',
                'url' => 'https://www.techwholesale.com.tr',
                'metakey' => [
                    'tr' => 'b2b e-ticaret, toptan satış, erp entegrasyon',
                    'en' => 'b2b e-commerce, wholesale, erp integration',
                    'ar' => 'التجارة الإلكترونية b2b، البيع بالجملة، تكامل erp'
                ],
                'metadesc' => [
                    'tr' => 'TechWholesale için geliştirdiğimiz B2B e-ticaret platformu',
                    'en' => 'B2B e-commerce platform developed for TechWholesale',
                    'ar' => 'منصة التجارة الإلكترونية B2B المطورة لـ TechWholesale'
                ]
            ]
        ];

        foreach ($portfolios as $portfolio) {
            Portfolio::create([
                'portfolio_category_id' => $portfolio['category_id'],
                'title' => json_encode($portfolio['title']),
                'slug' => json_encode($portfolio['slug']),
                'body' => json_encode($portfolio['body']),
                'image' => $portfolio['image'] ?? null,
                'client' => isset($portfolio['client']) ? json_encode($portfolio['client']) : null,
                'date' => $portfolio['date'] ?? null,
                'url' => $portfolio['url'] ?? null,
                'css' => null,
                'js' => null,
                'metakey' => json_encode($portfolio['metakey']),
                'metadesc' => json_encode($portfolio['metadesc']),
                'seo' => isset($portfolio['seo']) ? json_encode($portfolio['seo']) : null,
                'is_active' => true,
            ]);
        }
    }
}