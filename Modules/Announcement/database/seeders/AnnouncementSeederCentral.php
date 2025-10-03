<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use Modules\SeoManagement\App\Models\SeoSetting;
use App\Helpers\TenantHelpers;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

/**
 * Announcement Seeder for Central Database
 *
 * Creates Turkish Informatics corporate website pages
 * with AI-focused content in 3 languages.
 *
 * Languages: Turkish (tr), English (en), Arabic (ar)
 * Theme: AI Solutions & Corporate Technology
 *
 * Features:
 * - Modern gradient designs with Alpine.js animations
 * - SEO-optimized content for all pages
 * - Automatic menu creation with multilingual support
 * - Factory-powered additional pages for testing
 *
 * @package Modules\Announcement\Database\Seeders
 */
class AnnouncementSeederCentral extends Seeder
{
    /**
     * Pages created counter for summary
     */
    private int $pagesCreated = 0;

    /**
     * Run the central database seeds
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Central Database Announcement Seeding...');
        $this->command->newLine();

        // Duplicate check
        $existingCount = Announcement::count();
        if ($existingCount > 0) {
            $this->command->warn("⚠️  Pages already exist ({$existingCount} pages)");
            $this->command->info('💡 Skipping seeder to prevent duplicates');
            return;
        }

        // Clean slate
        $this->command->info('🧹 Cleaning existing data...');
        Announcement::truncate();

        // Create pages
        $this->command->info('📝 Creating pages...');
        $this->createHomepage();
        $this->createAboutPage();
        $this->createServicesPage();
        $this->createContactPage();
        $this->createPrivacyPage();

        // Create menu
        $this->command->newLine();
        $this->command->info('🗂️  Creating navigation menu...');
        $this->createMainMenu();

        // Create additional development pages
        $this->command->newLine();
        $this->command->info('🔨 Creating additional pages for development...');
        $this->createDevelopmentPages();

        // Summary
        $this->command->newLine();
        $this->showSummary();
    }

    /**
     * Create homepage with modern AI-focused design
     */
    private function createHomepage(): void
    {
        $this->command->info('  → Homepage (AI Solutions)');

        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Anasayfa',
                'en' => 'Homepage',
                'ar' => 'الصفحة الرئيسية'
            ],
            'slug' => [
                'tr' => 'anasayfa',
                'en' => 'homepage',
                'ar' => 'الصفحة-الرئيسية'
            ],
            'body' => [
                'tr' => '<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                    <!-- Hero Section -->
                    <section class="relative py-16 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-blue-300 dark:bg-blue-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse"></div>
                            <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-purple-300 dark:bg-purple-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
                            <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-pink-300 dark:bg-pink-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse animation-delay-4000"></div>
                        </div>

                        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center"
                                 x-show="loaded"
                                 x-transition:enter="transition ease-out duration-1000"
                                 x-transition:enter-start="opacity-0 transform translate-y-8"
                                 x-transition:enter-end="opacity-100 transform translate-y-0">

                                <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent animate-pulse">
                                        Türk Bilişim
                                    </span><br>
                                    <span class="text-gray-800 dark:text-gray-200">Kurumsal</span><br>
                                    <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-500 bg-clip-text text-transparent">
                                        Yapay Zeka
                                    </span>
                                </h1>

                                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8 leading-relaxed">
                                    🚀 Güçlendirilmiş, öğretilmiş ve <strong>size özel çalışan</strong> yapay zeka çözümleriyle işinizi bir üst seviyeye taşıyın
                                </p>

                                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-10">
                                    <button class="group relative px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                        <span class="relative z-10">🎯 Hemen Başlayın</span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </button>
                                    <button class="px-6 py-3 border-2 border-purple-600 dark:border-purple-400 text-purple-600 dark:text-purple-400 rounded-lg font-semibold hover:bg-purple-600 dark:hover:bg-purple-500 hover:text-white transition-all duration-300">
                                        📊 Portfolyo İnceleyin
                                    </button>
                                </div>

                                <div class="flex flex-wrap justify-center gap-8 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                        Türkçe Özel Eğitim
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                                        API Entegrasyonu
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2 animate-pulse"></span>
                                        7/24 Destek
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scroll indicator -->
                        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </section>

                    <!-- Features Section -->
                    <section class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-16">
                                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                                    ⚡ <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Neden Bizi Seçmelisiniz?</span>
                                </h2>
                                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                                    Kurumsal yapay zeka çözümlerinde Türkiye\'nin öncü teknoloji şirketi olarak size en iyisini sunuyoruz
                                </p>
                            </div>

                            <div class="grid md:grid-cols-3 gap-8">
                                <!-- Feature 1 -->
                                <div class="group bg-gradient-to-br from-blue-50 to-white dark:from-gray-700 dark:to-gray-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 dark:border-gray-600"
                                     x-data="{ hovered: false }"
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false">
                                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">🧠</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Özel Eğitilmiş AI</h3>
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Türkçe dil yapısına ve iş süreçlerinize özel eğitilmiş yapay zeka modelleri ile maksimum verimlilik
                                    </p>
                                    <div class="mt-6" x-show="hovered" x-transition>
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold">→ Detayları İncele</span>
                                    </div>
                                </div>

                                <!-- Feature 2 -->
                                <div class="group bg-gradient-to-br from-purple-50 to-white dark:from-gray-700 dark:to-gray-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 dark:border-gray-600"
                                     x-data="{ hovered: false }"
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false">
                                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">⚡</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Hızlı Entegrasyon</h3>
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Mevcut sistemlerinize kolay entegrasyon. API ile dakikalar içinde kullanmaya başlayın
                                    </p>
                                    <div class="mt-6" x-show="hovered" x-transition>
                                        <span class="text-purple-600 dark:text-purple-400 font-semibold">→ API Dökümanı</span>
                                    </div>
                                </div>

                                <!-- Feature 3 -->
                                <div class="group bg-gradient-to-br from-pink-50 to-white dark:from-gray-700 dark:to-gray-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 dark:border-gray-600"
                                     x-data="{ hovered: false }"
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false">
                                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">🔐</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Güvenli & Özel</h3>
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Verileriniz tamamen güvende. Kendi altyapınızda çalışan, gizliliğinizi koruyan çözümler
                                    </p>
                                    <div class="mt-6" x-show="hovered" x-transition>
                                        <span class="text-pink-600 dark:text-pink-400 font-semibold">→ Güvenlik Detayları</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Stats Section -->
                    <section class="py-20 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                                <div>
                                    <div class="text-4xl font-bold mb-2" x-data="{ count: 0 }" x-init="setInterval(() => { if(count < 150) count += 3; }, 50)">
                                        <span x-text="count"></span>+
                                    </div>
                                    <p class="text-lg opacity-90">Mutlu Müşteri</p>
                                </div>
                                <div>
                                    <div class="text-4xl font-bold mb-2">99.9%</div>
                                    <p class="text-lg opacity-90">Uptime Garantisi</p>
                                </div>
                                <div>
                                    <div class="text-4xl font-bold mb-2">24/7</div>
                                    <p class="text-lg opacity-90">Teknik Destek</p>
                                </div>
                                <div>
                                    <div class="text-4xl font-bold mb-2">5★</div>
                                    <p class="text-lg opacity-90">Müşteri Memnuniyeti</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- CTA Section -->
                    <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
                        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                                🚀 <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Hazır mısınız?</span>
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 mb-10">
                                İşinizi bir sonraki seviyeye taşıyacak yapay zeka çözümlerini keşfedin
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <button class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                                    ✨ Ücretsiz Demo Talep Edin
                                </button>
                                <button class="px-10 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full font-bold text-lg hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-300">
                                    📞 Bizi Arayın
                                </button>
                            </div>
                        </div>
                    </section>
                </div>',
                'en' => '<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                    <!-- Hero Section -->
                    <section class="relative py-16 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-blue-300 dark:bg-blue-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse"></div>
                            <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-purple-300 dark:bg-purple-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
                            <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-pink-300 dark:bg-pink-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse animation-delay-4000"></div>
                        </div>

                        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center"
                                 x-show="loaded"
                                 x-transition:enter="transition ease-out duration-1000"
                                 x-transition:enter-start="opacity-0 transform translate-y-8"
                                 x-transition:enter-end="opacity-100 transform translate-y-0">

                                <h1 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8 leading-tight">
                                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent animate-pulse">
                                        Custom Trained
                                    </span><br>
                                    <span class="text-gray-800 dark:text-gray-200">Artificial Intelligence</span><br>
                                    <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-500 bg-clip-text text-transparent">
                                        For Business
                                    </span>
                                </h1>

                                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-4xl mx-auto mb-12 leading-relaxed">
                                    🚀 Enhanced, trained, and <strong>customized AI solutions</strong> to take your business to the next level
                                </p>

                                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
                                    <button class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                                        <span class="relative z-10">🎯 Get Started Now</span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </button>
                                    <button class="px-8 py-4 border-2 border-purple-600 dark:border-purple-400 text-purple-600 dark:text-purple-400 rounded-full font-semibold text-lg hover:bg-purple-600 dark:hover:bg-purple-500 hover:text-white transition-all duration-300">
                                        📊 View Portfolio
                                    </button>
                                </div>

                                <div class="flex flex-wrap justify-center gap-8 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                        Custom Training
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                                        API Integration
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2 animate-pulse"></span>
                                        24/7 Support
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scroll indicator -->
                        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </section>

                    <!-- Features Section -->
                    <section class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-16">
                                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                                    ⚡ <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Why Choose Us?</span>
                                </h2>
                                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                                    As Turkey\'s leading technology company in enterprise AI solutions, we offer you the best
                                </p>
                            </div>

                            <div class="grid md:grid-cols-3 gap-8">
                                <!-- Feature 1 -->
                                <div class="group bg-gradient-to-br from-blue-50 to-white dark:from-gray-700 dark:to-gray-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 dark:border-gray-600"
                                     x-data="{ hovered: false }"
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false">
                                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">🧠</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Custom Trained AI</h3>
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        AI models specially trained for your language structure and business processes for maximum efficiency
                                    </p>
                                    <div class="mt-6" x-show="hovered" x-transition>
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold">→ View Details</span>
                                    </div>
                                </div>

                                <!-- Feature 2 -->
                                <div class="group bg-gradient-to-br from-purple-50 to-white dark:from-gray-700 dark:to-gray-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 dark:border-gray-600"
                                     x-data="{ hovered: false }"
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false">
                                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">⚡</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Fast Integration</h3>
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Easy integration into your existing systems. Start using with API in minutes
                                    </p>
                                    <div class="mt-6" x-show="hovered" x-transition>
                                        <span class="text-purple-600 dark:text-purple-400 font-semibold">→ API Documentation</span>
                                    </div>
                                </div>

                                <!-- Feature 3 -->
                                <div class="group bg-gradient-to-br from-pink-50 to-white dark:from-gray-700 dark:to-gray-800 p-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 dark:border-gray-600"
                                     x-data="{ hovered: false }"
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false">
                                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">🔐</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Secure & Private</h3>
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Your data is completely safe. Solutions that run on your own infrastructure and protect your privacy
                                    </p>
                                    <div class="mt-6" x-show="hovered" x-transition>
                                        <span class="text-pink-600 dark:text-pink-400 font-semibold">→ Security Details</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Stats Section -->
                    <section class="py-20 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                                <div>
                                    <div class="text-4xl font-bold mb-2" x-data="{ count: 0 }" x-init="setInterval(() => { if(count < 150) count += 3; }, 50)">
                                        <span x-text="count"></span>+
                                    </div>
                                    <p class="text-lg opacity-90">Happy Customers</p>
                                </div>
                                <div>
                                    <div class="text-4xl font-bold mb-2">99.9%</div>
                                    <p class="text-lg opacity-90">Uptime Guarantee</p>
                                </div>
                                <div>
                                    <div class="text-4xl font-bold mb-2">24/7</div>
                                    <p class="text-lg opacity-90">Technical Support</p>
                                </div>
                                <div>
                                    <div class="text-4xl font-bold mb-2">5★</div>
                                    <p class="text-lg opacity-90">Customer Satisfaction</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- CTA Section -->
                    <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
                        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                                🚀 <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Ready to Start?</span>
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 mb-10">
                                Discover AI solutions that will take your business to the next level
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <button class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                                    ✨ Request Free Demo
                                </button>
                                <button class="px-10 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full font-bold text-lg hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-300">
                                    📞 Call Us
                                </button>
                            </div>
                        </div>
                    </section>
                </div>',
                'ar' => '<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                    <!-- Hero Section -->
                    <section class="relative py-16 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-blue-300 dark:bg-blue-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse"></div>
                            <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-purple-300 dark:bg-purple-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
                            <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-pink-300 dark:bg-pink-600 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-pulse animation-delay-4000"></div>
                        </div>

                        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center" x-show="loaded" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 transform translate-y-8" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">

                                <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent animate-pulse">المعلوماتية التركية</span><br>
                                    <span class="text-gray-800 dark:text-gray-200">مؤسسي</span><br>
                                    <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-500 bg-clip-text text-transparent">الذكاء الاصطناعي</span>
                                </h1>

                                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8 leading-relaxed">🚀 عزز، ودرب، و<strong>يعمل خصيصًا لك</strong> ارفع عملك إلى المستوى التالي بحلول الذكاء الاصطناعي المصممة خصيصًا لك</p>

                                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-10">
                                    <button class="group relative px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                        <span class="relative z-10">🎯 ابدأ الآن</span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </button>
                                    <button class="px-6 py-3 border-2 border-purple-600 dark:border-purple-400 text-purple-600 dark:text-purple-400 rounded-lg font-semibold hover:bg-purple-600 dark:hover:bg-purple-500 hover:text-white transition-all duration-300">📊 استعرض الحافظة</button>
                                </div>

                                <div class="flex flex-wrap justify-center gap-8 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>تعليم خاص باللغة التركية</div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>تكامل API</div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2 animate-pulse"></span>دعم على مدار الساعة</div>
                                </div>
                            </div>
                        </div>

                        <!-- Scroll indicator -->
                        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </section>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Türk Bilişim - Kurumunuza Özel Eğitilmiş Yapay Zeka',
            'Custom Trained AI Solutions for Your Business',
            'حلول ذكاء اصطناعي مخصصة لشركتك',
            'Güçlendirilmiş, öğretilmiş ve size özel çalışan yapay zeka çözümleriyle işinizi bir üst seviyeye taşıyın.',
            'Enhanced, trained, and customized AI solutions to take your business to the next level.',
            'حلول ذكاء اصطناعي معززة ومدربة ومخصصة لنقل عملك إلى المستوى التالي.'
        );

        $this->pagesCreated++;
    }

    /**
     * Create about us announcement
     */
    private function createAboutPage(): void
    {
        $this->command->info('  → About Us');

        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Hakkımızda',
                'en' => 'About Us',
                'ar' => 'من نحن'
            ],
            'slug' => [
                'tr' => 'hakkimizda',
                'en' => 'about-us',
                'ar' => 'من-نحن'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">Hakkımızda</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                Kaliteli hizmet ve müşteri memnuniyeti odaklı çalışma prensiplerimizle sektörde öncü olmaya devam ediyoruz.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-16 items-center mb-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Misyonumuz</h2>
                                <p class="text-gray-600 mb-4">Modern teknolojiler kullanarak müşterilerimize en iyi çözümleri sunmak.</p>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Vizyonumuz</h2>
                                <p class="text-gray-600 mb-4">Teknoloji alanında global bir marka olmak.</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">About Us</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                We continue to be pioneers in the sector with our quality service and customer satisfaction-focused working principles.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-16 items-center mb-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Mission</h2>
                                <p class="text-gray-600 mb-4">To provide the best solutions to our customers using modern technologies.</p>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Vision</h2>
                                <p class="text-gray-600 mb-4">To become a global brand in the technology field.</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">من نحن</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                نواصل كوننا رواداً في القطاع بمبادئ عملنا المركزة على الخدمة الجيدة ورضا العملاء.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-16 items-center mb-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">مهمتنا</h2>
                                <p class="text-gray-600 mb-4">تقديم أفضل الحلول لعملائنا باستخدام التقنيات الحديثة.</p>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">رؤيتنا</h2>
                                <p class="text-gray-600 mb-4">أن نصبح علامة تجارية عالمية في مجال التكنولوجيا.</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Hakkımızda - Türk Bilişim',
            'About Us - Türk Bilişim',
            'من نحن - تورك بيليشيم',
            'Kaliteli hizmet ve müşteri memnuniyeti odaklı çalışma prensiplerimizle sektörde öncü olmaya devam ediyoruz.',
            'We continue to be pioneers in the sector with our quality service and customer satisfaction-focused working principles.',
            'نواصل كوننا رواداً في القطاع بمبادئ عملنا المركزة على الخدمة الجيدة ورضا العملاء.'
        );

        $this->pagesCreated++;
    }

    /**
     * Create services announcement
     */
    private function createServicesPage(): void
    {
        $this->command->info('  → Services');

        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Hizmetlerimiz',
                'en' => 'Our Services',
                'ar' => 'خدماتنا'
            ],
            'slug' => [
                'tr' => 'hizmetlerimiz',
                'en' => 'services',
                'ar' => 'خدماتنا'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Hizmetlerimiz</h1>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">Yapay Zeka Çözümleri</h3>
                            <p class="text-gray-600">Size özel eğitilmiş yapay zeka sistemleri geliştiriyoruz.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">Web Tasarım</h3>
                            <p class="text-gray-600">Modern ve responsive web siteleri tasarlıyoruz.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">Dijital Pazarlama</h3>
                            <p class="text-gray-600">Markanızı dijital dünyada büyütüyoruz.</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Our Services</h1>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">AI Solutions</h3>
                            <p class="text-gray-600">We develop custom trained AI systems for you.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">Web Design</h3>
                            <p class="text-gray-600">We design modern and responsive websites.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">Digital Marketing</h3>
                            <p class="text-gray-600">We grow your brand in the digital world.</p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">خدماتنا</h1>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">حلول الذكاء الاصطناعي</h3>
                            <p class="text-gray-600">نطور أنظمة ذكاء اصطناعي مدربة خصيصاً لك.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">تصميم المواقع</h3>
                            <p class="text-gray-600">نصمم مواقع ويب حديثة ومتجاوبة.</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-2xl font-bold mb-4">التسويق الرقمي</h3>
                            <p class="text-gray-600">ننمي علامتك التجارية في العالم الرقمي.</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Hizmetlerimiz - Türk Bilişim',
            'Our Services - Türk Bilişim',
            'خدماتنا - تورك بيليشيم',
            'Yapay zeka çözümleri, web tasarım ve dijital pazarlama hizmetlerimiz hakkında bilgi alın.',
            'Learn about our AI solutions, web design and digital marketing services.',
            'تعرف على خدماتنا في حلول الذكاء الاصطناعي وتصميم المواقع والتسويق الرقمي.'
        );

        $this->pagesCreated++;
    }

    /**
     * Create contact announcement
     */
    private function createContactPage(): void
    {
        $this->command->info('  → Contact');

        $announcement = Announcement::create([
            'title' => [
                'tr' => 'İletişim',
                'en' => 'Contact',
                'ar' => 'اتصل بنا'
            ],
            'slug' => [
                'tr' => 'iletisim',
                'en' => 'contact',
                'ar' => 'اتصل-بنا'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">İletişim</h1>
                    <div class="max-w-4xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">İletişim Bilgileri</h2>
                                <div class="space-y-4">
                                    <p class="flex items-center text-gray-600">
                                        <span class="mr-3">📧</span> info@turkbilisim.com
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <span class="mr-3">📱</span> +90 532 123 45 67
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <span class="mr-3">📍</span> İstanbul, Türkiye
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Mesaj Gönderin</h2>
                                <p class="text-gray-600">Bizimle iletişime geçmek için formu doldurun.</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Contact</h1>
                    <div class="max-w-4xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Contact Information</h2>
                                <div class="space-y-4">
                                    <p class="flex items-center text-gray-600">
                                        <span class="mr-3">📧</span> info@turkbilisim.com
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <span class="mr-3">📱</span> +90 532 123 45 67
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <span class="mr-3">📍</span> Istanbul, Turkey
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Send Message</h2>
                                <p class="text-gray-600">Fill out the form to get in touch with us.</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">اتصل بنا</h1>
                    <div class="max-w-4xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">معلومات الاتصال</h2>
                                <div class="space-y-4">
                                    <p class="flex items-center text-gray-600">
                                        <span class="ml-3">📧</span> info@turkbilisim.com
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <span class="ml-3">📱</span> +90 532 123 45 67
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <span class="ml-3">📍</span> إسطنبول، تركيا
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">أرسل رسالة</h2>
                                <p class="text-gray-600">املأ النموذج للتواصل معنا.</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'İletişim - Türk Bilişim',
            'Contact - Türk Bilişim',
            'اتصل بنا - تورك بيليشيم',
            'Bizimle iletişime geçin. Türk Bilişim iletişim bilgileri ve mesaj formu.',
            'Get in touch with us. Türk Bilişim contact information and message form.',
            'تواصل معنا. معلومات الاتصال ونموذج الرسائل لتورك بيليشيم.'
        );

        $this->pagesCreated++;
    }

    /**
     * Create privacy policy announcement
     */
    private function createPrivacyPage(): void
    {
        $this->command->info('  → Privacy Policy');

        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Gizlilik Politikası',
                'en' => 'Privacy Policy',
                'ar' => 'سياسة الخصوصية'
            ],
            'slug' => [
                'tr' => 'gizlilik-politikasi',
                'en' => 'privacy-policy',
                'ar' => 'سياسة-الخصوصية'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Gizlilik Politikası</h1>
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>Bu gizlilik politikası, kişisel verilerinizin nasıl korunduğunu açıklar.</p>
                        <h2>Veri Toplama</h2>
                        <p>Web sitemizi ziyaret ettiğinizde belirli bilgileri otomatik olarak toplarız.</p>
                        <h2>Veri Kullanımı</h2>
                        <p>Toplanan veriler hizmet kalitemizi artırmak için kullanılır.</p>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Privacy Policy</h1>
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>This privacy policy explains how your personal data is protected.</p>
                        <h2>Data Collection</h2>
                        <p>When you visit our website, we automatically collect certain information.</p>
                        <h2>Data Usage</h2>
                        <p>The collected data is used to improve our service quality.</p>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">سياسة الخصوصية</h1>
                    <div class="max-w-4xl mx-auto prose prose-lg">
                        <p>تشرح سياسة الخصوصية هذه كيفية حماية بياناتك الشخصية.</p>
                        <h2>جمع البيانات</h2>
                        <p>عندما تزور موقعنا الإلكتروني، نقوم تلقائياً بجمع معلومات معينة.</p>
                        <h2>استخدام البيانات</h2>
                        <p>تُستخدم البيانات المجمعة لتحسين جودة خدمتنا.</p>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Gizlilik Politikası - Türk Bilişim',
            'Privacy Policy - Türk Bilişim',
            'سياسة الخصوصية - تورك بيليشيم',
            'Kişisel verilerinizin nasıl korunduğu ve kullanıldığı hakkında bilgi edinin.',
            'Learn about how your personal data is protected and used.',
            'تعرف على كيفية حماية واستخدام بياناتك الشخصية.'
        );

        $this->pagesCreated++;
    }

    /**
     * Create SEO settings for a announcement
     * Supports 3 languages (tr, en, ar)
     */
    private function createSeoSetting($announcement, $titleTr, $titleEn, $titleAr, $descriptionTr, $descriptionEn, $descriptionAr): void
    {
        // Clean existing SEO settings for fresh seed
        if ($announcement->seoSetting()->exists()) {
            $announcement->seoSetting()->delete();
        }

        $announcement->seoSetting()->create([
            'titles' => [
                'tr' => $titleTr,
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'og_titles' => [
                'tr' => $titleTr,
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'og_descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'seo_score' => rand(80, 95),
        ]);
    }

    /**
     * Create main navigation menu
     * Includes all primary pages in 3 languages
     */
    private function createMainMenu(): void
    {
        // Check for existing menu
        $existingMenu = Menu::where('slug', 'ana-menu')->first();

        if ($existingMenu) {
            $this->command->info('  ℹ  Main menu already exists, skipping...');
            return;
        }

        // Create main menu
        $menu = Menu::create([
            'name' => [
                'tr' => 'Ana Menü',
                'en' => 'Main Menu',
                'ar' => 'القائمة الرئيسية'
            ],
            'slug' => 'ana-menu',
            'location' => 'header',
            'is_active' => true,
            'is_default' => true,
        ]);

        // 1. Anasayfa
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Anasayfa',
                'en' => 'Home',
                'ar' => 'الصفحة الرئيسية'
            ],
            'url_type' => 'internal',
            'url_data' => ['url' => 'announcements/anasayfa'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // 2. Hakkımızda
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Hakkımızda',
                'en' => 'About Us',
                'ar' => 'من نحن'
            ],
            'url_type' => 'internal',
            'url_data' => ['url' => 'announcements/hakkimizda'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 3. Hizmetlerimiz
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Hizmetlerimiz',
                'en' => 'Our Services',
                'ar' => 'خدماتنا'
            ],
            'url_type' => 'internal',
            'url_data' => ['url' => 'announcements/hizmetlerimiz'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 4. İletişim
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'İletişim',
                'en' => 'Contact',
                'ar' => 'اتصل بنا'
            ],
            'url_type' => 'internal',
            'url_data' => ['url' => 'announcements/iletisim'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $this->command->info('  ✓ Main menu created with 4 items');
    }

    /**
     * Create additional pages for development/testing
     * Uses factory to generate random content
     */
    private function createDevelopmentPages(): void
    {
        // Create 5 random simple pages
        $count = 5;

        Announcement::factory()
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
        $this->command->info('✅ CENTRAL DATABASE SEEDING COMPLETED');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Pages Created', $this->pagesCreated],
                ['Languages Supported', 'Turkish, English, Arabic'],
                ['Homepage', '1 (AI Solutions themed)'],
                ['Standard Pages', '4 (About, Services, Contact, Privacy)'],
                ['Development Pages', ($this->pagesCreated - 5)],
                ['Menu Items', '4'],
                ['SEO Settings', 'Auto-generated for all pages'],
            ]
        );
        $this->command->newLine();
    }
}
