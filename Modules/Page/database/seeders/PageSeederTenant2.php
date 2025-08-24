<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use Modules\SeoManagement\App\Models\SeoSetting;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

/**
 * Page Seeder for Tenant2 - E-TİCARET & DİJİTAL ÇÖZÜMLER
 * Theme: Modern E-Commerce Platform
 * Languages: tr, en
 */
class PageSeederTenant2 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT2 E-Commerce pages (tr, en)...');
        
        // Duplicate kontrolü
        $existingCount = Page::count();
        if ($existingCount > 0) {
            $this->command->info("E-Commerce pages already exist in TENANT2 database ({$existingCount} pages), skipping seeder...");
            return;
        }
        
        // Mevcut sayfaları sil (sadece boşsa)
        Page::truncate();
        
        $this->createHomepage();
        $this->createAboutPage();
        $this->createProductsPage();
        $this->createBlogPage();
        $this->createContactPage();
    }
    
    private function createHomepage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'Dijital Ajans - Ana Sayfa', 
                'en' => 'Digital Agency - Homepage'
            ],
            'slug' => [
                'tr' => 'anasayfa', 
                'en' => 'homepage'
            ],
            'body' => [
                'tr' => '<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50">
                    <div class="container mx-auto px-4 py-8">
                        <div class="text-center mb-8">
                            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                                <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Dijital Dünyada</span><br>
                                <span class="text-gray-800">Markanızı Büyütün</span>
                            </h1>
                            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                                Profesyonel dijital pazarlama ve web tasarım hizmetleriyle işletmenizi online dünyada zirveye taşıyoruz.
                            </p>
                            <div class="flex gap-4 justify-center">
                                <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-full font-semibold">
                                    Hemen Başlayın
                                </button>
                                <button class="border-2 border-purple-600 text-purple-600 px-8 py-3 rounded-full font-semibold">
                                    Portfolyo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50">
                    <div class="container mx-auto px-4 py-8">
                        <div class="text-center mb-8">
                            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                                <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Grow Your Brand</span><br>
                                <span class="text-gray-800">In Digital World</span>
                            </h1>
                            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                                We take your business to the top of the online world with professional digital marketing and web design services.
                            </p>
                            <div class="flex gap-4 justify-center">
                                <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-full font-semibold">
                                    Get Started
                                </button>
                                <button class="border-2 border-purple-600 text-purple-600 px-8 py-3 rounded-full font-semibold">
                                    Portfolio
                                </button>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => true,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Dijital Ajans - Markanızı Online Dünyada Büyütün',
            'Digital Agency - Grow Your Brand Online',
            'Profesyonel dijital pazarlama ve web tasarım hizmetleriyle işletmenizi online dünyada zirveye taşıyoruz.',
            'We take your business to the top of the online world with professional digital marketing and web design services.'
        );
    }
    
    private function createAboutPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'Hakkımızda',
                'en' => 'About Us'
            ],
            'slug' => [
                'tr' => 'hakkimizda',
                'en' => 'about-us'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">Hakkımızda</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                2015 yılından beri dijital pazarlama ve web tasarım alanında hizmet veren deneyimli ekibimizle müşterilerimizin dijital varlığını güçlendiriyoruz.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold mb-4">Deneyim</h3>
                                <p class="text-gray-600">8+ yıllık sektör deneyimi</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-2xl font-bold mb-4">Proje</h3>
                                <p class="text-gray-600">500+ başarılı proje</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-2xl font-bold mb-4">Müşteri</h3>
                                <p class="text-gray-600">200+ mutlu müşteri</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">About Us</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                We have been strengthening our customers\' digital presence with our experienced team serving in digital marketing and web design since 2015.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold mb-4">Experience</h3>
                                <p class="text-gray-600">8+ years of industry experience</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-2xl font-bold mb-4">Projects</h3>
                                <p class="text-gray-600">500+ successful projects</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-2xl font-bold mb-4">Clients</h3>
                                <p class="text-gray-600">200+ happy customers</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Hakkımızda - Dijital Ajans',
            'About Us - Digital Agency',
            '2015 yılından beri dijital pazarlama ve web tasarım alanında hizmet veren deneyimli ekibimizle müşterilerimizin dijital varlığını güçlendiriyoruz.',
            'We have been strengthening our customers\' digital presence with our experienced team serving in digital marketing and web design since 2015.'
        );
    }
    
    private function createProductsPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'Hizmetlerimiz',
                'en' => 'Our Services'
            ],
            'slug' => [
                'tr' => 'hizmetlerimiz',
                'en' => 'services'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Hizmetlerimiz</h1>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">🎨</div>
                            <h3 class="text-2xl font-bold mb-4">Web Tasarım</h3>
                            <p class="text-gray-600">Modern, responsive ve kullanıcı dostu web siteleri tasarlıyoruz.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">📱</div>
                            <h3 class="text-2xl font-bold mb-4">Sosyal Medya</h3>
                            <p class="text-gray-600">Sosyal medya hesaplarınızı profesyonelce yönetiyoruz.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">🔍</div>
                            <h3 class="text-2xl font-bold mb-4">SEO Optimizasyonu</h3>
                            <p class="text-gray-600">Google\'da üst sıralarda yer almanızı sağlıyoruz.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">💰</div>
                            <h3 class="text-2xl font-bold mb-4">Google Ads</h3>
                            <p class="text-gray-600">Etkili Google Ads kampanyalarıyla hedef kitlenize ulaşın.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">📊</div>
                            <h3 class="text-2xl font-bold mb-4">Analytics</h3>
                            <p class="text-gray-600">Web sitenizin performansını detaylı olarak analiz ediyoruz.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">🛒</div>
                            <h3 class="text-2xl font-bold mb-4">E-Ticaret</h3>
                            <p class="text-gray-600">Online mağaza kurulumu ve yönetimi hizmetleri.</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Our Services</h1>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">🎨</div>
                            <h3 class="text-2xl font-bold mb-4">Web Design</h3>
                            <p class="text-gray-600">We design modern, responsive and user-friendly websites.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">📱</div>
                            <h3 class="text-2xl font-bold mb-4">Social Media</h3>
                            <p class="text-gray-600">We professionally manage your social media accounts.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">🔍</div>
                            <h3 class="text-2xl font-bold mb-4">SEO Optimization</h3>
                            <p class="text-gray-600">We ensure you rank high on Google.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">💰</div>
                            <h3 class="text-2xl font-bold mb-4">Google Ads</h3>
                            <p class="text-gray-600">Reach your target audience with effective Google Ads campaigns.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">📊</div>
                            <h3 class="text-2xl font-bold mb-4">Analytics</h3>
                            <p class="text-gray-600">We analyze your website\'s performance in detail.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="text-4xl mb-4">🛒</div>
                            <h3 class="text-2xl font-bold mb-4">E-Commerce</h3>
                            <p class="text-gray-600">Online store setup and management services.</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Hizmetlerimiz - Dijital Pazarlama ve Web Tasarım',
            'Our Services - Digital Marketing and Web Design',
            'Web tasarım, sosyal medya, SEO, Google Ads ve e-ticaret hizmetlerimiz hakkında detaylı bilgi alın.',
            'Get detailed information about our web design, social media, SEO, Google Ads and e-commerce services.'
        );
    }
    
    private function createBlogPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'Blog',
                'en' => 'Blog'
            ],
            'slug' => [
                'tr' => 'blog',
                'en' => 'blog'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Blog</h1>
                    <div class="max-w-4xl mx-auto">
                        <p class="text-xl text-gray-600 text-center mb-12">
                            Dijital pazarlama, web tasarım ve teknoloji dünyasından en güncel haberleri ve ipuçlarını takip edin.
                        </p>
                        <div class="grid md:grid-cols-2 gap-8">
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-2xl font-bold mb-4">SEO İpuçları 2024</h2>
                                    <p class="text-gray-600 mb-4">Google algoritmalarının son güncellemelerine göre SEO stratejinizi nasıl güncelleyebileceğinizi öğrenin.</p>
                                    <a href="#" class="text-purple-600 font-semibold">Devamını Oku →</a>
                                </div>
                            </article>
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-2xl font-bold mb-4">Modern Web Tasarım Trendleri</h2>
                                    <p class="text-gray-600 mb-4">2024 yılında dikkat çeken web tasarım trendleri ve bunları nasıl uygulayabileceğiniz hakkında.</p>
                                    <a href="#" class="text-purple-600 font-semibold">Devamını Oku →</a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Blog</h1>
                    <div class="max-w-4xl mx-auto">
                        <p class="text-xl text-gray-600 text-center mb-12">
                            Follow the latest news and tips from the world of digital marketing, web design and technology.
                        </p>
                        <div class="grid md:grid-cols-2 gap-8">
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-2xl font-bold mb-4">SEO Tips 2024</h2>
                                    <p class="text-gray-600 mb-4">Learn how to update your SEO strategy based on the latest Google algorithm updates.</p>
                                    <a href="#" class="text-purple-600 font-semibold">Read More →</a>
                                </div>
                            </article>
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-2xl font-bold mb-4">Modern Web Design Trends</h2>
                                    <p class="text-gray-600 mb-4">About the web design trends that attract attention in 2024 and how you can apply them.</p>
                                    <a href="#" class="text-purple-600 font-semibold">Read More →</a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Blog - Dijital Pazarlama ve Web Tasarım Yazıları',
            'Blog - Digital Marketing and Web Design Articles',
            'Dijital pazarlama, web tasarım ve teknoloji dünyasından en güncel haberleri ve ipuçlarını takip edin.',
            'Follow the latest news and tips from the world of digital marketing, web design and technology.'
        );
    }
    
    private function createContactPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'İletişim',
                'en' => 'Contact'
            ],
            'slug' => [
                'tr' => 'iletisim',
                'en' => 'contact'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">İletişim</h1>
                    <div class="max-w-6xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Bizimle İletişime Geçin</h2>
                                <div class="space-y-6">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-purple-600 text-xl">📧</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">E-posta</h3>
                                            <p class="text-gray-600">info@dijitalajans.com</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-purple-600 text-xl">📱</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">Telefon</h3>
                                            <p class="text-gray-600">+90 212 555 01 23</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-purple-600 text-xl">📍</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">Adres</h3>
                                            <p class="text-gray-600">Levent, İstanbul</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-8">
                                <h2 class="text-2xl font-bold mb-6">Proje Teklifi Alın</h2>
                                <p class="text-gray-600 mb-6">Projeniz hakkında bizimle iletişime geçin, size özel çözümler sunalım.</p>
                                <div class="space-y-4">
                                    <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold">
                                        Teklif Formu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Contact</h1>
                    <div class="max-w-6xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Get In Touch</h2>
                                <div class="space-y-6">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-purple-600 text-xl">📧</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">Email</h3>
                                            <p class="text-gray-600">info@dijitalajans.com</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-purple-600 text-xl">📱</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">Phone</h3>
                                            <p class="text-gray-600">+90 212 555 01 23</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-purple-600 text-xl">📍</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">Address</h3>
                                            <p class="text-gray-600">Levent, Istanbul</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-8">
                                <h2 class="text-2xl font-bold mb-6">Get Project Quote</h2>
                                <p class="text-gray-600 mb-6">Contact us about your project and let us offer you customized solutions.</p>
                                <div class="space-y-4">
                                    <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold">
                                        Quote Form
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'İletişim - Dijital Ajans',
            'Contact - Digital Agency',
            'Projeniz hakkında bizimle iletişime geçin, size özel dijital pazarlama çözümleri sunalım.',
            'Contact us about your project and let us offer you customized digital marketing solutions.'
        );
    }

    private function createSeoSetting($page, $titleTr, $titleEn, $descriptionTr, $descriptionEn): void
    {
        // Eğer bu sayfa için zaten SEO ayarı varsa oluşturma
        if ($page->seoSetting()->exists()) {
            return;
        }
        
        $page->seoSetting()->create([
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
            'og_type' => 'website',
            'twitter_card' => 'summary',
            'seo_score' => rand(80, 95),
        ]);
    }
    
    private function createServicesPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'E-Ticaret Çözümleri',
                'en' => 'E-Commerce Solutions'
            ],
            'slug' => [
                'tr' => 'e-ticaret-cozumleri',
                'en' => 'ecommerce-solutions'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">E-Ticaret Çözümleri</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Online satış kanalınızı kurmak ve büyütmek için ihtiyacınız olan tüm hizmetler</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-2xl">🛍️</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Online Mağaza Kurulumu</h3>
                            <p class="text-gray-600">Sıfırdan e-ticaret sitenizi kurar, ürün kataloğu ve ödeme sistemlerini entegre ederiz.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-2xl">💳</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Ödeme Sistemi Entegrasyonu</h3>
                            <p class="text-gray-600">Güvenli ödeme yöntemlerini sitenize entegre ederek müşterilerinizin güvenle alışveriş yapmasını sağlarız.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-2xl">📊</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Envanter Yönetimi</h3>
                            <p class="text-gray-600">Stok takibi, ürün yönetimi ve sipariş süreçlerinizi otomatikleştiren sistemler kuruyoruz.</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">E-Commerce Solutions</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">All the services you need to set up and grow your online sales channel</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-2xl">🛍️</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Online Store Setup</h3>
                            <p class="text-gray-600">We build your e-commerce site from scratch and integrate product catalogs and payment systems.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-2xl">💳</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Payment System Integration</h3>
                            <p class="text-gray-600">We integrate secure payment methods to ensure your customers shop with confidence.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                                <span class="text-2xl">📊</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Inventory Management</h3>
                            <p class="text-gray-600">We set up systems that automate your stock tracking, product management and order processes.</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'E-Ticaret Çözümleri - ShopMax',
            'E-Commerce Solutions - ShopMax',
            'Online satış kanalınızı kurmak ve büyütmek için ihtiyacınız olan tüm e-ticaret hizmetleri.',
            'All the services you need to set up and grow your online sales channel.'
        );
    }
    
    private function createPrivacyPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'Gizlilik Politikası',
                'en' => 'Privacy Policy'
            ],
            'slug' => [
                'tr' => 'gizlilik-politikasi',
                'en' => 'privacy-policy'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>ShopMax olarak müşterilerimizin gizliliğini korumaya büyük önem veriyoruz. Bu gizlilik politikası, kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklamaktadır.</p>
                        
                        <h2>Toplanan Bilgiler</h2>
                        <p>Alışveriş deneyiminizi geliştirmek için aşağıdaki bilgileri toplayabiliriz:</p>
                        <ul>
                            <li>Ad, soyad ve iletişim bilgileri</li>
                            <li>Teslimat adresi bilgileri</li>
                            <li>Ödeme bilgileri (güvenli şekilde işlenir)</li>
                            <li>Sipariş geçmişi ve tercihler</li>
                        </ul>
                        
                        <h2>Veri Güvenliği</h2>
                        <p>Kişisel verilerinizi korumak için endüstri standardı güvenlik önlemleri alıyoruz. Verileriniz SSL şifreleme ile korunur ve güvenli sunucularda saklanır.</p>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>At ShopMax, we place great importance on protecting the privacy of our customers. This privacy policy explains how your personal data is collected, used and protected.</p>
                        
                        <h2>Information Collected</h2>
                        <p>We may collect the following information to improve your shopping experience:</p>
                        <ul>
                            <li>Name, surname and contact information</li>
                            <li>Delivery address information</li>
                            <li>Payment information (processed securely)</li>
                            <li>Order history and preferences</li>
                        </ul>
                        
                        <h2>Data Security</h2>
                        <p>We take industry-standard security measures to protect your personal data. Your data is protected with SSL encryption and stored on secure servers.</p>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Gizlilik Politikası - ShopMax',
            'Privacy Policy - ShopMax',
            'ShopMax gizlilik politikası - Kişisel verilerinizin nasıl korunduğu hakkında bilgi.',
            'ShopMax privacy policy - Learn how your personal data is protected.'
        );
    }
    
    private function createTermsPage(): void
    {
        $page = Page::create([
            'title' => [
                'tr' => 'Kullanım Şartları',
                'en' => 'Terms of Service'
            ],
            'slug' => [
                'tr' => 'kullanim-sartlari',
                'en' => 'terms-of-service'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>ShopMax platformunu kullanarak aşağıdaki şart ve koşulları kabul etmiş sayılırsınız.</p>
                        
                        <h2>Genel Şartlar</h2>
                        <ul>
                            <li>Sitemizi kullanmak için 18 yaşından büyük olmalısınız</li>
                            <li>Doğru ve güncel bilgiler sağlamalısınız</li>
                            <li>Hesabınızın güvenliğinden sorumlusunuz</li>
                        </ul>
                        
                        <h2>Sipariş ve Ödeme</h2>
                        <ul>
                            <li>Tüm fiyatlar KDV dahildir</li>
                            <li>Ödeme onaylandıktan sonra sipariş kesinleşir</li>
                            <li>500 TL üzeri siparişlerde kargo ücretsizdir</li>
                        </ul>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>By using the ShopMax platform, you are deemed to have accepted the following terms and conditions.</p>
                        
                        <h2>General Terms</h2>
                        <ul>
                            <li>You must be over 18 years old to use our site</li>
                            <li>You must provide accurate and up-to-date information</li>
                            <li>You are responsible for the security of your account</li>
                        </ul>
                        
                        <h2>Order and Payment</h2>
                        <ul>
                            <li>All prices include VAT</li>
                            <li>Order is confirmed after payment approval</li>
                            <li>Free shipping for orders over 500 TL</li>
                        </ul>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Kullanım Şartları - ShopMax',
            'Terms of Service - ShopMax',
            'ShopMax kullanım şartları ve koşulları.',
            'ShopMax terms and conditions.'
        );
    }
    
    private function createMainMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'tr' => 'Ana Menü', 
                'en' => 'Main Menu'
            ],
            'slug' => 'main-menu-ecommerce',
            'location' => 'header',
            'is_active' => true,
            'is_default' => true,
        ]);
        
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Anasayfa', 'en' => 'Home'],
            'url_type' => 'page',
            'url_data' => ['page' => 'homepage'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Hakkımızda', 'en' => 'About Us'],
            'url_type' => 'page',
            'url_data' => ['page' => 'about-us'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Ürünler', 'en' => 'Products'],
            'url_type' => 'page',
            'url_data' => ['page' => 'services'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Çözümler', 'en' => 'Solutions'],
            'url_type' => 'page',
            'url_data' => ['page' => 'ecommerce-solutions'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Blog', 'en' => 'Blog'],
            'url_type' => 'page',
            'url_data' => ['page' => 'blog'],
            'target' => '_self',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'İletişim', 'en' => 'Contact'],
            'url_type' => 'page',
            'url_data' => ['page' => 'contact'],
            'target' => '_self',
            'sort_order' => 6,
            'is_active' => true,
        ]);

        $this->command->info('✅ Tenant2 E-Commerce menu created');
    }
}