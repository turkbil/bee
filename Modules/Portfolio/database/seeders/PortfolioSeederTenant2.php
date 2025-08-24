<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Portfolio Seeder for Tenant2 - E-TİCARET & DİJİTAL ÇÖZÜMLER
 * Theme: Modern E-Commerce Platform
 * Languages: tr, en
 */
class PortfolioSeederTenant2 extends Seeder
{
    public function run(): void
    {
        $this->command->info('🛍️ Creating TENANT2 E-Commerce portfolios (tr, en)...');
        
        $existingCount = Portfolio::count();
        if ($existingCount > 0) {
            $this->command->info("E-Commerce portfolios already exist in TENANT2 database ({$existingCount} portfolios), skipping...");
            return;
        }
        
        // Kategorilerin var olduğundan emin ol
        $ecommerceCategory = PortfolioCategory::where('slug->tr', 'e-ticaret')->first();
        $mobileCategory = PortfolioCategory::where('slug->tr', 'mobil-uygulama')->first();
        $webDesignCategory = PortfolioCategory::where('slug->tr', 'web-tasarim')->first();
        
        if (!$ecommerceCategory || !$mobileCategory || !$webDesignCategory) {
            $this->command->error('Portfolio categories not found! Please run PortfolioCategorySeederTenant2 first.');
            return;
        }
        
        Portfolio::truncate();
        
        $this->createShopMaxPortfolio($ecommerceCategory);
        $this->createMobileECommerceApp($mobileCategory);
        $this->createFashionStorePortfolio($ecommerceCategory);
        $this->createElectronicsStorePortfolio($ecommerceCategory);
        $this->createPaymentSystemPortfolio($webDesignCategory);
        $this->createInventorySystemPortfolio($webDesignCategory);
        
        $this->command->info('🛍️ TENANT2 E-Commerce portfolios created successfully!');
    }
    
    private function createShopMaxPortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'ShopMax Online AVM', 
                'en' => 'ShopMax Online Mall'
            ],
            'slug' => [
                'tr' => 'shopmax-online-avm', 
                'en' => 'shopmax-online-mall'
            ],
            'body' => [
                'tr' => '<div class="portfolio-content">
                    <h2>🛍️ Türkiye\'nin En Büyük Online Alışveriş Merkezi</h2>
                    <p>2+ milyon ürün çeşidi ile modern e-ticaret platformu. Güvenli ödeme sistemi, hızlı kargo ve 7/24 müşteri desteği ile mükemmel alışveriş deneyimi.</p>
                    
                    <h3>🎯 Proje Özellikleri:</h3>
                    <ul>
                        <li>🛒 2+ milyon ürün kataloğu</li>
                        <li>💳 Çoklu ödeme sistemi entegrasyonu</li>
                        <li>📱 Mobil uyumlu responsive tasarım</li>
                        <li>🚚 Gerçek zamanlı kargo takibi</li>
                        <li>🔍 Akıllı arama ve filtreleme</li>
                        <li>⭐ Kullanıcı değerlendirme sistemi</li>
                        <li>🔐 SSL güvenlik sertifikası</li>
                        <li>📊 Advanced analytics dashboard</li>
                    </ul>
                    
                    <h3>💡 Teknolojiler:</h3>
                    <p>Laravel, Vue.js, MySQL, Redis, Elasticsearch, AWS Cloud Infrastructure</p>
                </div>',
                'en' => '<div class="portfolio-content">
                    <h2>🛍️ Turkey\'s Biggest Online Shopping Center</h2>
                    <p>Modern e-commerce platform with 2+ million product varieties. Perfect shopping experience with secure payment system, fast shipping and 24/7 customer support.</p>
                    
                    <h3>🎯 Project Features:</h3>
                    <ul>
                        <li>🛒 2+ million product catalog</li>
                        <li>💳 Multi-payment system integration</li>
                        <li>📱 Mobile-friendly responsive design</li>
                        <li>🚚 Real-time cargo tracking</li>
                        <li>🔍 Smart search and filtering</li>
                        <li>⭐ User review system</li>
                        <li>🔐 SSL security certificate</li>
                        <li>📊 Advanced analytics dashboard</li>
                    </ul>
                    
                    <h3>💡 Technologies:</h3>
                    <p>Laravel, Vue.js, MySQL, Redis, Elasticsearch, AWS Cloud Infrastructure</p>
                </div>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'ShopMax',
            'date' => '2025',
            'url' => 'https://shopmax.com.tr',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'ShopMax Online AVM - E-Ticaret Projesi',
            'ShopMax Online Mall - E-Commerce Project',
            'Türkiye\'nin en büyük online alışveriş merkezi ShopMax projesi. 2+ milyon ürün ile modern e-ticaret çözümü.',
            'Turkey\'s biggest online shopping center ShopMax project. Modern e-commerce solution with 2+ million products.'
        );
    }
    
    private function createMobileECommerceApp($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'ShopMax Mobil Uygulama',
                'en' => 'ShopMax Mobile App'
            ],
            'slug' => [
                'tr' => 'shopmax-mobil-uygulama',
                'en' => 'shopmax-mobile-app'
            ],
            'body' => [
                'tr' => '<div class="portfolio-content">
                    <h2>📱 Yeni Nesil Mobil Alışveriş Uygulaması</h2>
                    <p>iOS ve Android platformları için geliştirilen modern mobil alışveriş uygulaması. Native performans ve kullanıcı dostu arayüz.</p>
                    
                    <h3>🎯 Uygulama Özellikleri:</h3>
                    <ul>
                        <li>📱 Cross-platform React Native</li>
                        <li>🔔 Push notification sistemi</li>
                        <li>📷 AR ile ürün deneme</li>
                        <li>💰 Tek tıkla ödeme</li>
                        <li>🚚 Gerçek zamanlı teslimat takibi</li>
                        <li>🎁 Sadakat programı entegrasyonu</li>
                        <li>🔒 Biyometrik güvenlik</li>
                        <li>🌙 Dark mode desteği</li>
                    </ul>
                    
                    <h3>📊 Başarı Metrikleri:</h3>
                    <p>500K+ indirme, 4.8 ⭐ App Store/Google Play puanı, %25 mobil dönüşüm artışı</p>
                </div>',
                'en' => '<div class="portfolio-content">
                    <h2>📱 Next Generation Mobile Shopping App</h2>
                    <p>Modern mobile shopping app developed for iOS and Android platforms. Native performance and user-friendly interface.</p>
                    
                    <h3>🎯 App Features:</h3>
                    <ul>
                        <li>📱 Cross-platform React Native</li>
                        <li>🔔 Push notification system</li>
                        <li>📷 AR product try-on</li>
                        <li>💰 One-click payment</li>
                        <li>🚚 Real-time delivery tracking</li>
                        <li>🎁 Loyalty program integration</li>
                        <li>🔒 Biometric security</li>
                        <li>🌙 Dark mode support</li>
                    </ul>
                    
                    <h3>📊 Success Metrics:</h3>
                    <p>500K+ downloads, 4.8 ⭐ App Store/Google Play rating, 25% mobile conversion increase</p>
                </div>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'ShopMax',
            'date' => '2025',
            'url' => 'https://apps.apple.com/shopmax',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'ShopMax Mobil Uygulama - React Native',
            'ShopMax Mobile App - React Native',
            'iOS ve Android için ShopMax mobil alışveriş uygulaması. 500K+ indirme ile başarılı proje.',
            'ShopMax mobile shopping app for iOS and Android. Successful project with 500K+ downloads.'
        );
    }
    
    private function createFashionStorePortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'FashionHub Moda Mağazası',
                'en' => 'FashionHub Fashion Store'
            ],
            'slug' => [
                'tr' => 'fashionhub-moda-magazasi',
                'en' => 'fashionhub-fashion-store'
            ],
            'body' => [
                'tr' => '<div class="portfolio-content">
                    <h2>👗 Premium Moda E-Ticaret Platformu</h2>
                    <p>Lüks moda markalarına özel tasarlanmış e-ticaret sitesi. Görsel odaklı tasarım ve premium alışveriş deneyimi.</p>
                    
                    <h3>🎯 Özellikler:</h3>
                    <ul>
                        <li>👗 10,000+ moda ürünü</li>
                        <li>📸 360° ürün görüntüleme</li>
                        <li>👥 Stil danışmanı canlı chat</li>
                        <li>📏 Sanal beden rehberi</li>
                        <li>💅 Renk ve stil filtreleri</li>
                        <li>🎁 Hediye paketi servisi</li>
                        <li>↩️ 30 gün kolay iade</li>
                        <li>🌟 Influencer koleksiyonları</li>
                    </ul>
                    
                    <h3>📈 Sonuçlar:</h3>
                    <p>%40 dönüşüm artışı, %60 müşteri memnuniyeti artışı, 25K+ aktif müşteri</p>
                </div>',
                'en' => '<div class="portfolio-content">
                    <h2>👗 Premium Fashion E-Commerce Platform</h2>
                    <p>E-commerce site specially designed for luxury fashion brands. Visual-focused design and premium shopping experience.</p>
                    
                    <h3>🎯 Features:</h3>
                    <ul>
                        <li>👗 10,000+ fashion products</li>
                        <li>📸 360° product viewing</li>
                        <li>👥 Style consultant live chat</li>
                        <li>📏 Virtual size guide</li>
                        <li>💅 Color and style filters</li>
                        <li>🎁 Gift wrapping service</li>
                        <li>↩️ 30 days easy return</li>
                        <li>🌟 Influencer collections</li>
                    </ul>
                    
                    <h3>📈 Results:</h3>
                    <p>40% conversion increase, 60% customer satisfaction increase, 25K+ active customers</p>
                </div>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'FashionHub',
            'date' => '2024',
            'url' => 'https://fashionhub.com.tr',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'FashionHub Moda Mağazası - E-Ticaret',
            'FashionHub Fashion Store - E-Commerce',
            'Premium moda e-ticaret platformu. 10,000+ ürün ile lüks alışveriş deneyimi.',
            'Premium fashion e-commerce platform. Luxury shopping experience with 10,000+ products.'
        );
    }
    
    private function createElectronicsStorePortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'TechZone Elektronik Mağazası',
                'en' => 'TechZone Electronics Store'
            ],
            'slug' => [
                'tr' => 'techzone-elektronik-magazasi',
                'en' => 'techzone-electronics-store'
            ],
            'body' => [
                'tr' => '<div class="portfolio-content">
                    <h2>📱 Teknoloji ve Elektronik E-Ticaret</h2>
                    <p>En yeni teknoloji ürünleri için özelleştirilmiş e-ticaret platformu. Teknik detaylar ve karşılaştırma özellikleri.</p>
                    
                    <h3>🔧 Özellikler:</h3>
                    <ul>
                        <li>📱 50,000+ elektronik ürün</li>
                        <li>⚖️ Ürün karşılaştırma sistemi</li>
                        <li>📋 Teknik özellik detayları</li>
                        <li>🎯 Uzman tavsiyeleri</li>
                        <li>🛠️ Teknik destek hattı</li>
                        <li>📦 Hızlı kargo garantisi</li>
                        <li>🔧 Kurulum hizmetleri</li>
                        <li>💰 Taksit seçenekleri</li>
                    </ul>
                    
                    <h3>🏆 Başarılar:</h3>
                    <p>100K+ mutlu müşteri, %95 teslimat başarısı, 4.9⭐ müşteri puanı</p>
                </div>',
                'en' => '<div class="portfolio-content">
                    <h2>📱 Technology and Electronics E-Commerce</h2>
                    <p>E-commerce platform customized for the latest technology products. Technical details and comparison features.</p>
                    
                    <h3>🔧 Features:</h3>
                    <ul>
                        <li>📱 50,000+ electronic products</li>
                        <li>⚖️ Product comparison system</li>
                        <li>📋 Technical specification details</li>
                        <li>🎯 Expert recommendations</li>
                        <li>🛠️ Technical support line</li>
                        <li>📦 Fast shipping guarantee</li>
                        <li>🔧 Installation services</li>
                        <li>💰 Installment options</li>
                    </ul>
                    
                    <h3>🏆 Achievements:</h3>
                    <p>100K+ happy customers, 95% delivery success, 4.9⭐ customer rating</p>
                </div>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'TechZone',
            'date' => '2024',
            'url' => 'https://techzone.com.tr',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'TechZone Elektronik Mağazası - E-Ticaret',
            'TechZone Electronics Store - E-Commerce',
            'Teknoloji ve elektronik e-ticaret platformu. 50,000+ ürün ile teknik alışveriş çözümü.',
            'Technology and electronics e-commerce platform. Technical shopping solution with 50,000+ products.'
        );
    }
    
    private function createPaymentSystemPortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'SecurePay Ödeme Sistemi',
                'en' => 'SecurePay Payment System'
            ],
            'slug' => [
                'tr' => 'securepay-odeme-sistemi',
                'en' => 'securepay-payment-system'
            ],
            'body' => [
                'tr' => '<div class="portfolio-content">
                    <h2>💳 Güvenli Ödeme Sistemi Entegrasyonu</h2>
                    <p>E-ticaret siteleri için güvenli ve hızlı ödeme gateway çözümü. PCI DSS uyumlu güvenlik standardları.</p>
                    
                    <h3>🔐 Güvenlik Özellikleri:</h3>
                    <ul>
                        <li>💳 Çoklu kredi kartı desteği</li>
                        <li>🔒 SSL 256-bit şifreleme</li>
                        <li>🛡️ 3D Secure doğrulama</li>
                        <li>💰 Anında ödeme işlemi</li>
                        <li>📱 Mobil ödeme desteği</li>
                        <li>🔄 Otomatik geri ödeme</li>
                        <li>📊 Detaylı raporlama</li>
                        <li>🌍 Multi-currency desteği</li>
                    </ul>
                    
                    <h3>⚡ Performans:</h3>
                    <p>%99.9 uptime, <2sn işlem süresi, 1M+ başarılı işlem/ay</p>
                </div>',
                'en' => '<div class="portfolio-content">
                    <h2>💳 Secure Payment System Integration</h2>
                    <p>Secure and fast payment gateway solution for e-commerce sites. PCI DSS compliant security standards.</p>
                    
                    <h3>🔐 Security Features:</h3>
                    <ul>
                        <li>💳 Multi credit card support</li>
                        <li>🔒 SSL 256-bit encryption</li>
                        <li>🛡️ 3D Secure authentication</li>
                        <li>💰 Instant payment processing</li>
                        <li>📱 Mobile payment support</li>
                        <li>🔄 Automatic refund</li>
                        <li>📊 Detailed reporting</li>
                        <li>🌍 Multi-currency support</li>
                    </ul>
                    
                    <h3>⚡ Performance:</h3>
                    <p>99.9% uptime, <2s processing time, 1M+ successful transactions/month</p>
                </div>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'SecurePay',
            'date' => '2024',
            'url' => 'https://securepay.com.tr',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'SecurePay Ödeme Sistemi - Fintech',
            'SecurePay Payment System - Fintech',
            'Güvenli ödeme gateway sistemi. PCI DSS uyumlu, 1M+ aylık işlem kapasitesi.',
            'Secure payment gateway system. PCI DSS compliant, 1M+ monthly transaction capacity.'
        );
    }
    
    private function createInventorySystemPortfolio($category): void
    {
        $portfolio = Portfolio::create([
            'title' => [
                'tr' => 'SmartStock Envanter Yönetimi',
                'en' => 'SmartStock Inventory Management'
            ],
            'slug' => [
                'tr' => 'smartstock-envanter-yonetimi',
                'en' => 'smartstock-inventory-management'
            ],
            'body' => [
                'tr' => '<div class="portfolio-content">
                    <h2>📊 Akıllı Envanter Yönetim Sistemi</h2>
                    <p>E-ticaret firmaları için gelişmiş stok takip ve envanter yönetim çözümü. AI destekli tahmin algoritmaları.</p>
                    
                    <h3>🤖 Akıllı Özellikler:</h3>
                    <ul>
                        <li>📦 Gerçek zamanlı stok takibi</li>
                        <li>🧠 AI tahmin algoritmaları</li>
                        <li>⚠️ Otomatik uyarı sistemi</li>
                        <li>📈 Satış analiz raporları</li>
                        <li>🔄 Otomatik sipariş oluşturma</li>
                        <li>📱 Mobil stok kontrol</li>
                        <li>🏷️ Barkod yönetimi</li>
                        <li>📊 Dashboard raporlama</li>
                    </ul>
                    
                    <h3>💼 Faydalar:</h3>
                    <p>%30 stok maliyeti azalması, %50 sipariş hızı artışı, %95 stok doğruluk oranı</p>
                </div>',
                'en' => '<div class="portfolio-content">
                    <h2>📊 Smart Inventory Management System</h2>
                    <p>Advanced stock tracking and inventory management solution for e-commerce companies. AI-powered prediction algorithms.</p>
                    
                    <h3>🤖 Smart Features:</h3>
                    <ul>
                        <li>📦 Real-time stock tracking</li>
                        <li>🧠 AI prediction algorithms</li>
                        <li>⚠️ Automatic alert system</li>
                        <li>📈 Sales analysis reports</li>
                        <li>🔄 Automatic order creation</li>
                        <li>📱 Mobile stock control</li>
                        <li>🏷️ Barcode management</li>
                        <li>📊 Dashboard reporting</li>
                    </ul>
                    
                    <h3>💼 Benefits:</h3>
                    <p>30% stock cost reduction, 50% order speed increase, 95% stock accuracy rate</p>
                </div>'
            ],
            'portfolio_category_id' => $category->portfolio_category_id,
            'image' => '',
            'client' => 'SmartStock',
            'date' => '2024',
            'url' => 'https://smartstock.com.tr',
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $portfolio,
            'SmartStock Envanter Yönetimi - SaaS',
            'SmartStock Inventory Management - SaaS',
            'AI destekli envanter yönetim sistemi. %30 maliyet azalması ile akıllı stok çözümü.',
            'AI-powered inventory management system. Smart stock solution with 30% cost reduction.'
        );
    }

    private function createSeoSetting($portfolio, $titleTr, $titleEn, $descriptionTr, $descriptionEn): void
    {
        if ($portfolio->seoSetting()->exists()) {
            return;
        }
        
        $portfolio->seoSetting()->create([
            'titles' => [
                'tr' => $titleTr,
                'en' => $titleEn
            ],
            'descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn
            ],
            'robots_meta' => ['index' => true, 'follow' => true, 'archive' => true],
            'og_titles' => [
                'tr' => $titleTr,
                'en' => $titleEn
            ],
            'og_descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn
            ],
            'og_type' => 'article',
            'seo_score' => rand(85, 95),
        ]);
    }
}