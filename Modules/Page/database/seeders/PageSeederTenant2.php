<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use Modules\SeoManagement\App\Models\SeoSetting;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

/**
 * Page Seeder for Tenant 2 - Digital Agency
 *
 * Creates modern digital agency website pages.
 * Focus: Digital marketing, web design, e-commerce solutions
 *
 * Languages: Turkish (tr), English (en)
 * Theme: Digital Agency / E-Commerce Platform
 *
 * Features:
 * - Modern purple/pink gradient designs
 * - Service-focused content
 * - Blog integration ready
 * - SEO-optimized pages
 *
 * @package Modules\Page\Database\Seeders
 */
class PageSeederTenant2 extends Seeder
{
    /**
     * Pages created counter
     */
    private int $pagesCreated = 0;

    /**
     * Run the tenant 2 database seeds
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Tenant 2 Page Seeding...');
        $this->command->newLine();

        // Duplicate check
        $existingCount = Page::count();
        if ($existingCount > 0) {
            $this->command->warn("⚠️  Pages already exist ({$existingCount} pages)");
            $this->command->info('💡 Skipping seeder to prevent duplicates');
            return;
        }

        // Clean slate
        $this->command->info('🧹 Cleaning existing data...');
        Page::truncate();

        // Create pages
        $this->command->info('📝 Creating pages...');
        $this->createHomepage();
        $this->createAboutPage();
        $this->createProductsPage();
        $this->createBlogPage();
        $this->createContactPage();

        // Create additional development pages
        $this->command->newLine();
        $this->command->info('🔨 Creating additional pages for development...');
        $this->createDevelopmentPages();

        // Summary
        $this->command->newLine();
        $this->showSummary();
    }
    
    /**
     * Create homepage for digital agency
     */
    private function createHomepage(): void
    {
        $this->command->info('  → Homepage (Digital Agency)');

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

        $this->pagesCreated++;
    }
    
    /**
     * Create about us page
     */
    private function createAboutPage(): void
    {
        $this->command->info('  → About Us');

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

        $this->pagesCreated++;
    }
    
    /**
     * Create services/products page
     */
    private function createProductsPage(): void
    {
        $this->command->info('  → Services');

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

        $this->pagesCreated++;
    }
    
    /**
     * Create blog page
     */
    private function createBlogPage(): void
    {
        $this->command->info('  → Blog');

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

        $this->pagesCreated++;
    }
    
    /**
     * Create contact page
     */
    private function createContactPage(): void
    {
        $this->command->info('  → Contact');

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

        $this->pagesCreated++;
    }

    /**
     * Create SEO settings for a page
     * Supports 2 languages (tr, en)
     */
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

    /**
     * Create additional pages for development/testing
     */
    private function createDevelopmentPages(): void
    {
        $count = 8;

        Page::factory()
            ->simple()
            ->count($count)
            ->create();

        $this->command->info("  ✓ Created {$count} random pages for testing");
        $this->pagesCreated += $count;
    }

    /**
     * Show seeding summary
     */
    private function showSummary(): void
    {
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ TENANT 2 DATABASE SEEDING COMPLETED');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Pages Created', $this->pagesCreated],
                ['Languages Supported', 'Turkish, English'],
                ['Theme', 'Digital Agency / E-Commerce'],
                ['Homepage', '1 (Digital Agency themed)'],
                ['Standard Pages', '4 (About, Services, Blog, Contact)'],
                ['Development Pages', ($this->pagesCreated - 5)],
                ['SEO Settings', 'Auto-generated for all pages'],
            ]
        );
        $this->command->newLine();
    }

    /**
     * Create main navigation menu - NOT USED IN THIS SEEDER
     * Menu creation handled by MenuManagement module seeder
     */
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

        $this->command->info('  ✓ Menu created (deprecated - use MenuManagement seeder instead)');
    }
}