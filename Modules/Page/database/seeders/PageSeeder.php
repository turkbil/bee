<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use App\Models\SeoSetting;
use App\Helpers\TenantHelpers;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Duplicate kontrolü - eğer zaten sayfa varsa atla
        // Context bilgisi ile count kontrolü
        $contextInfo = TenantHelpers::isCentral() ? 'CENTRAL' : 'TENANT';
        $existingCount = Page::count();
        
        if ($existingCount > 0) {
            if (TenantHelpers::isCentral()) {
                $this->command->info("Pages already exist in CENTRAL database ({$existingCount} pages), skipping seeder...");
            } else {
                $this->command->info("Pages already exist in TENANT database ({$existingCount} pages), skipping seeder...");
            }
            return;
        }
        
        $this->command->info("No existing pages found in {$contextInfo} database, proceeding with seeding...");
        
        // Mevcut sayfaları sil (sadece boşsa)
        Page::truncate();
        SeoSetting::where('seoable_type', 'like', '%Page%')->delete();
        
        if (TenantHelpers::isCentral()) {
            // Central veritabanında - tenant 1 (laravel.test)
            $this->command->info('PageSeeder central veritabanında çalışıyor...');
            $currentDomain = 'laravel.test';
            $this->command->info("Creating CENTRAL pages for domain: {$currentDomain}");
            $this->createCentralPages();
        } else {
            // Tenant veritabanında - domain'i tenant ID'den belirle
            $tenantId = tenant('id');
            $this->command->info("PageSeeder tenant veritabanında çalışıyor... Tenant ID: {$tenantId}");
            
            $currentDomain = $this->getDomainFromTenantId($tenantId);
            $this->command->info("Creating TENANT pages for tenant: {$tenantId}, domain: {$currentDomain}");

            // Domain'e göre sayfa oluştur
            switch ($currentDomain) {
                case 'a.test':
                    $this->createDigitalAgencyPages();
                    break;
                case 'b.test':
                    $this->createEcommercePages();
                    break;
                case 'c.test':
                    $this->createTechCompanyPages();
                    break;
                default:
                    $this->createDefaultPages();
                    break;
            }
        }
    }
    
    private function createCentralPages(): void
    {
        $this->command->info('Creating CENTRAL (CMS) pages...');
        
        $page = Page::create([
            'title' => ['tr' => 'Anasayfa', 'en' => 'Homepage', 'ar' => 'الصفحة الرئيسية'],
            'slug' => ['tr' => 'anasayfa', 'en' => 'homepage', 'ar' => 'الصفحة-الرئيسية'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-blue-600 dark:text-blue-400">Turkbil</span> <span class="text-yellow-500 dark:text-yellow-400">CMS</span>
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                            Modern web siteleri için güçlü içerik yönetim sistemi. Laravel\'in gücü, Tailwind\'in esnekliği.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg">
                                🚀 Demo İzle
                            </button>
                            <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-3 rounded-lg">
                                📖 Dokümantasyon
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">⚡</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Hızlı & Verimli</h3>
                            <p class="text-gray-600 dark:text-gray-400">Modern Laravel mimarisi ile optimize edilmiş performans.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Güvenli</h3>
                            <p class="text-gray-600 dark:text-gray-400">En son güvenlik standartları ile korumalı sistem.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🎨</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Esnek Tasarım</h3>
                            <p class="text-gray-600 dark:text-gray-400">Tailwind CSS ile sınırsız özelleştirme imkanı.</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-blue-600 dark:text-blue-400">Turkbil</span> <span class="text-yellow-500 dark:text-yellow-400">CMS</span>
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                            Powerful content management system for modern websites. The power of Laravel, the flexibility of Tailwind.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg">
                                🚀 Watch Demo
                            </button>
                            <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-3 rounded-lg">
                                📖 Documentation
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">⚡</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Fast & Efficient</h3>
                            <p class="text-gray-600 dark:text-gray-400">Optimized performance with modern Laravel architecture.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Secure</h3>
                            <p class="text-gray-600 dark:text-gray-400">Protected system with latest security standards.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🎨</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Flexible Design</h3>
                            <p class="text-gray-600 dark:text-gray-400">Unlimited customization possibilities with Tailwind CSS.</p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-blue-600 dark:text-blue-400">تركبيل</span> <span class="text-yellow-500 dark:text-yellow-400">سي إم إس</span>
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                            نظام إدارة محتوى قوي للمواقع الحديثة. قوة لارافيل، مرونة تيلوند.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg">
                                🚀 مشاهدة العرض
                            </button>
                            <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-3 rounded-lg">
                                📖 التوثيق
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">⚡</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">سريع وفعال</h3>
                            <p class="text-gray-600 dark:text-gray-400">أداء محسن مع هندسة لارافيل الحديثة.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">آمن</h3>  
                            <p class="text-gray-600 dark:text-gray-400">نظام محمي بأحدث معايير الأمان.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🎨</div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">تصميم مرن</h3>
                            <p class="text-gray-600 dark:text-gray-400">إمكانيات تخصيص لا محدودة مع تيلوند سي إس إس.</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => true,
        ]);
        
        $this->createSeoSetting($page, 'Turkbil CMS - Modern İçerik Yönetim Sistemi', 'Laravel tabanlı güçlü CMS çözümü.');

        $page = Page::create([
            'title' => ['tr' => 'Hakkımızda', 'en' => 'About Us', 'ar' => 'من نحن'],
            'slug' => ['tr' => 'hakkimizda', 'en' => 'about-us', 'ar' => 'من-نحن'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-4xl font-bold mb-8">Hakkımızda</h1>
                    <div class="prose max-w-none">
                        <p class="text-lg mb-6">Turkbil CMS, modern web geliştirme ihtiyaçları için tasarlanmış güçlü bir içerik yönetim sistemidir.</p>
                        <p>Laravel framework üzerine inşa edilen sistemimiz, yüksek performans ve güvenlik standartları sunar.</p>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-4xl font-bold mb-8">About Us</h1>
                    <div class="prose max-w-none">
                        <p class="text-lg mb-6">Turkbil CMS is a powerful content management system designed for modern web development needs.</p>
                        <p>Our system built on Laravel framework offers high performance and security standards.</p>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-4xl font-bold mb-8">من نحن</h1>
                    <div class="prose max-w-none">
                        <p class="text-lg mb-6">تركبيل سي إم إس هو نظام إدارة محتوى قوي مصمم لاحتياجات تطوير الويب الحديثة.</p>
                        <p>نظامنا المبني على إطار لارافيل يوفر معايير عالية الأداء والأمان.</p>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => false,
        ]);

        $this->createSeoSetting($page, 'Hakkımızda - Turkbil CMS', 'Turkbil CMS hakkında bilgi edinin.');

        // İletişim sayfası
        $page = Page::create([
            'title' => ['tr' => 'İletişim', 'en' => 'Contact', 'ar' => 'اتصل بنا'],
            'slug' => ['tr' => 'iletisim', 'en' => 'contact', 'ar' => 'اتصل-بنا'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-4xl mx-auto">
                        <div class="text-center mb-12">
                            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 mb-6">İletişim</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">Bizimle iletişime geçin. Size yardımcı olmak için buradayız.</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-12">
                            <div class="space-y-8">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📍</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Adres</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Teknokent Mahallesi<br>İstanbul Üniversitesi Teknoparkı<br>34469 Sarıyer/İstanbul</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📧</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">E-posta</h3>
                                        <p class="text-gray-600 dark:text-gray-400">info@turkbilcms.com<br>destek@turkbilcms.com</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📱</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Telefon</h3>
                                        <p class="text-gray-600 dark:text-gray-400">+90 212 555 0123<br>+90 532 555 0123</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Mesaj Gönderin</h3>
                                <form class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ad Soyad</label>
                                        <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">E-posta</label>
                                        <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mesaj</label>
                                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200"></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                                        Mesaj Gönder
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-4xl mx-auto">
                        <div class="text-center mb-12">
                            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 mb-6">Contact</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">Get in touch with us. We are here to help you.</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-12">
                            <div class="space-y-8">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📍</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Address</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Teknokent District<br>Istanbul University Technopark<br>34469 Sarıyer/Istanbul</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📧</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Email</h3>
                                        <p class="text-gray-600 dark:text-gray-400">info@turkbilcms.com<br>support@turkbilcms.com</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📱</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Phone</h3>
                                        <p class="text-gray-600 dark:text-gray-400">+90 212 555 0123<br>+90 532 555 0123</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Send Message</h3>
                                <form class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                        <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                        <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200"></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                                        Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="max-w-4xl mx-auto">
                        <div class="text-center mb-12">
                            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 mb-6">اتصل بنا</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">تواصل معنا. نحن هنا لمساعدتك.</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-12">
                            <div class="space-y-8">
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📍</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">العنوان</h3>
                                        <p class="text-gray-600 dark:text-gray-400">حي تكنوكنت<br>حديقة جامعة اسطنبول التقنية<br>34469 ساريير/اسطنبول</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📧</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">البريد الإلكتروني</h3>
                                        <p class="text-gray-600 dark:text-gray-400">info@turkbilcms.com<br>support@turkbilcms.com</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📱</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">الهاتف</h3>
                                        <p class="text-gray-600 dark:text-gray-400">+90 212 555 0123<br>+90 532 555 0123</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">إرسال رسالة</h3>
                                <form class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الاسم الكامل</label>
                                        <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
                                        <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الرسالة</label>
                                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200"></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                                        إرسال الرسالة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => false,
        ]);

        $this->createSeoSetting($page, 'İletişim - Turkbil CMS', 'Bizimle iletişime geçin. Size yardımcı olmak için buradayız.');
    }
    
    private function createDigitalAgencyPages(): void
    {
        $this->command->info('Creating DIGITAL AGENCY pages...');
        
        $page = Page::create([
            'title' => ['tr' => 'Anasayfa', 'en' => 'Homepage', 'ar' => 'الصفحة الرئيسية'],
            'slug' => ['tr' => 'anasayfa', 'en' => 'homepage', 'ar' => 'الصفحة-الرئيسية'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-7xl font-bold mb-6 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Dijital Dünyada Fark Yaratan Ajans
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto mb-8">
                            Markanızı dijital alemde öne çıkarıyoruz. Yaratıcı tasarım ve etkili stratejilerle hedef kitlenize ulaşın.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-full text-lg font-semibold">
                                🚀 Projeye Başla
                            </button>
                            <button class="border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-4 rounded-full text-lg font-semibold">
                                📁 Portfolyo İzle
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-yellow-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">🎨</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Web Tasarım</h3>
                            <p class="text-gray-600 dark:text-gray-400">Modern ve kullanıcı dostu arayüzler</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">📱</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Mobil Uygulama</h3>
                            <p class="text-gray-600 dark:text-gray-400">iOS ve Android uygulamaları</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">📈</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Dijital Pazarlama</h3>
                            <p class="text-gray-600 dark:text-gray-400">SEO, SEM ve sosyal medya</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">✨</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Görsel Tasarım</h3>
                            <p class="text-gray-600 dark:text-gray-400">Logo, kimlik ve kurumsal tasarım</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-7xl font-bold mb-6 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Digital Agency That Makes a Difference
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto mb-8">
                            We make your brand stand out in the digital world. Reach your target audience with creative design and effective strategies.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-full text-lg font-semibold">
                                🚀 Start Project
                            </button>
                            <button class="border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-4 rounded-full text-lg font-semibold">
                                📁 View Portfolio
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-yellow-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">🎨</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Web Design</h3>
                            <p class="text-gray-600 dark:text-gray-400">Modern and user-friendly interfaces</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">📱</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Mobile Apps</h3>
                            <p class="text-gray-600 dark:text-gray-400">iOS and Android applications</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">📈</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Digital Marketing</h3>
                            <p class="text-gray-600 dark:text-gray-400">SEO, SEM and social media</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">✨</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">Visual Design</h3>
                            <p class="text-gray-600 dark:text-gray-400">Logo, identity and corporate design</p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="text-center mb-16">
                        <h1 class="text-7xl font-bold mb-6 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            وكالة رقمية تصنع الفارق
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto mb-8">
                            نجعل علامتك التجارية متميزة في العالم الرقمي. وصول إلى جمهورك المستهدف بتصميم إبداعي واستراتيجيات فعالة.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-full text-lg font-semibold">
                                🚀 ابدأ مشروع
                            </button>
                            <button class="border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-4 rounded-full text-lg font-semibold">
                                📁 عرض المحفظة
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-yellow-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">🎨</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">تصميم المواقع</h3>
                            <p class="text-gray-600 dark:text-gray-400">واجهات حديثة وسهلة الاستخدام</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">📱</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">تطبيقات الهاتف</h3>
                            <p class="text-gray-600 dark:text-gray-400">تطبيقات iOS و Android</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">📈</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">التسويق الرقمي</h3>
                            <p class="text-gray-600 dark:text-gray-400">SEO، SEM ووسائل التواصل الاجتماعي</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl">✨</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-200">التصميم البصري</h3>
                            <p class="text-gray-600 dark:text-gray-400">شعار وهوية وتصميم مؤسسي</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => true,
        ]);
        
        $this->createSeoSetting($page, 'Dijital Ajans - Web Tasarım ve Dijital Pazarlama', 'Profesyonel dijital ajans hizmetleri.');

        // Hakkımızda ve İletişim sayfaları da ekle
        $this->addCommonPages();
    }
    
    private function createEcommercePages(): void
    {
        $this->command->info('Creating E-COMMERCE pages...');
        
        $page = Page::create([
            'title' => ['tr' => 'Anasayfa', 'en' => 'Homepage', 'ar' => 'الصفحة الرئيسية'],
            'slug' => ['tr' => 'anasayfa', 'en' => 'homepage', 'ar' => 'الصفحة-الرئيسية'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-green-600 dark:text-green-400">🛒</span> E-Ticaret Mağazanız
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                            Kaliteli ürünler, uygun fiyatlar ve hızlı teslimat. Online alışverişin keyfini çıkarın.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg">
                                🔍 Ürünleri Keşfet
                            </button>
                            <button class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg">
                                🏷️ Kampanyalar
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🚚</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Ücretsiz Kargo</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">150 TL üzeri siparişlerde</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">↩️</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Kolay İade</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">30 gün içinde ücretsiz</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Güvenli Ödeme</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">SSL sertifikası ile korumalı</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🎧</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">7/24 Destek</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Müşteri hizmetleri</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-green-600 dark:text-green-400">🛒</span> Your E-Commerce Store
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                            Quality products, affordable prices and fast delivery. Enjoy online shopping.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg">
                                🔍 Discover Products
                            </button>
                            <button class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg">
                                🏷️ Campaigns
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🚚</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Free Shipping</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">On orders over $50</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">↩️</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Easy Returns</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Free within 30 days</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Secure Payment</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Protected with SSL certificate</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🎧</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">24/7 Support</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Customer service</p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-green-600 dark:text-green-400">🛒</span> متجرك الإلكتروني
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                            منتجات عالية الجودة وأسعار مناسبة وتسليم سريع. استمتع بالتسوق عبر الإنترنت.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg">
                                🔍 اكتشف المنتجات
                            </button>
                            <button class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg">
                                🏷️ الحملات
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🚚</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">شحن مجاني</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">على الطلبات أكثر من 50 دولار</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">↩️</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">إرجاع سهل</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">مجاني خلال 30 يوم</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">دفع آمن</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">محمي بشهادة SSL</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="text-4xl mb-4">🎧</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">دعم 24/7</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">خدمة العملاء</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => true,
        ]);
        
        $this->createSeoSetting($page, 'E-Ticaret Mağazası - Online Alışveriş', 'Güvenli online alışveriş deneyimi.');
        
        // Hakkımızda ve İletişim sayfaları da ekle
        $this->addCommonPages();
        
        // Hakkımızda ve İletişim sayfaları da ekle
        $this->addCommonPages();
    }
    
    private function createTechCompanyPages(): void
    {
        $this->command->info('Creating TECH COMPANY pages...');
        
        $page = Page::create([
            'title' => ['tr' => 'Anasayfa', 'en' => 'Homepage', 'ar' => 'الصفحة الرئيسية'],
            'slug' => ['tr' => 'anasayfa', 'en' => 'homepage', 'ar' => 'الصفحة-الرئيسية'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-indigo-600 dark:text-indigo-400">💻</span> Teknoloji & İnovasyon
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto mb-8">
                            Geleceğin teknolojilerini bugün geliştiriyoruz. Yapay zeka, bulut çözümleri ve yazılım geliştirme.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg">
                                🚀 Çözümlerimiz
                            </button>
                            <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-3 rounded-lg">
                                👥 Ekibimiz
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🧠</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Yapay Zeka</h3>
                            <p class="text-gray-600 dark:text-gray-400">Machine Learning ve Deep Learning çözümleri ile akıllı sistemler.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">☁️</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Bulut Çözümleri</h3>
                            <p class="text-gray-600 dark:text-gray-400">AWS, Azure ve Google Cloud platformlarında ölçeklenebilir altyapı.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">⚡</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Yazılım Geliştirme</h3>
                            <p class="text-gray-600 dark:text-gray-400">Modern teknolojilerle kurumsal yazılım çözümleri.</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-indigo-600 dark:text-indigo-400">💻</span> Technology & Innovation
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto mb-8">
                            We develop tomorrow\'s technologies today. Artificial intelligence, cloud solutions and software development.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg">
                                🚀 Our Solutions
                            </button>
                            <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-3 rounded-lg">
                                👥 Our Team
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🧠</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Artificial Intelligence</h3>
                            <p class="text-gray-600 dark:text-gray-400">Smart systems with Machine Learning and Deep Learning solutions.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">☁️</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Cloud Solutions</h3>
                            <p class="text-gray-600 dark:text-gray-400">Scalable infrastructure on AWS, Azure and Google Cloud platforms.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">⚡</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Software Development</h3>
                            <p class="text-gray-600 dark:text-gray-400">Enterprise software solutions with modern technologies.</p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="text-center mb-16">
                        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                            <span class="text-indigo-600 dark:text-indigo-400">💻</span> التكنولوجيا والابتكار
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto mb-8">
                            نطور تقنيات الغد اليوم. الذكاء الاصطناعي وحلول السحابة وتطوير البرمجيات.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg">
                                🚀 حلولنا
                            </button>
                            <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 px-8 py-3 rounded-lg">
                                👥 فريقنا
                            </button>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🧠</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">الذكاء الاصطناعي</h3>
                            <p class="text-gray-600 dark:text-gray-400">أنظمة ذكية مع حلول التعلم الآلي والتعلم العميق.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">☁️</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">الحلول السحابية</h3>
                            <p class="text-gray-600 dark:text-gray-400">بنية تحتية قابلة للتوسع على منصات AWS و Azure و Google Cloud.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg text-center border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">⚡</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">تطوير البرمجيات</h3>
                            <p class="text-gray-600 dark:text-gray-400">حلول برمجيات المؤسسات بالتقنيات الحديثة.</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => true,
        ]);
        
        $this->createSeoSetting($page, 'Teknoloji Şirketi - Yapay Zeka ve Bulut Çözümleri', 'Geleceğin teknolojileri ile inovasyon.');
        
        // Hakkımızda ve İletişim sayfaları da ekle
        $this->addCommonPages();
    }
    
    private function createDefaultPages(): void
    {
        $this->command->info('Creating DEFAULT pages...');
        
        $page = Page::create([
            'title' => ['tr' => 'Anasayfa', 'en' => 'Homepage', 'ar' => 'الصفحة الرئيسية'],
            'slug' => ['tr' => 'anasayfa', 'en' => 'homepage', 'ar' => 'الصفحة-الرئيسية'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">Hoşgeldiniz</h1>
                        <p class="text-xl text-gray-600">Bu bir varsayılan anasayfa tasarımıdır.</p>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">Welcome</h1>
                        <p class="text-xl text-gray-600">This is a default homepage design.</p>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="text-center">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">أهلاً وسهلاً</h1>
                        <p class="text-xl text-gray-600">هذا تصميم افتراضي للصفحة الرئيسية.</p>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => true,
        ]);
        
        $this->createSeoSetting($page, 'Anasayfa', 'Varsayılan anasayfa.');
        
        // Hakkımızda ve İletişim sayfaları da ekle
        $this->addCommonPages();
    }
    
    private function getDomainFromTenantId($tenantId): string
    {
        if (!$tenantId) {
            return 'laravel.test'; // Central
        }
        
        // Tenant ID'sine göre domain mapping (hem string hem integer desteği)
        $domainMap = [
            1 => 'laravel.test',
            2 => 'a.test',
            3 => 'b.test', 
            4 => 'c.test',
            '1' => 'laravel.test',
            '2' => 'a.test',
            '3' => 'b.test', 
            '4' => 'c.test',
        ];
        
        return $domainMap[$tenantId] ?? 'laravel.test';
    }

    private function createSeoSetting($page, $title, $description): void
    {
        $page->seoSetting()->create([
            'titles' => ['tr' => $title, 'en' => $title, 'ar' => $title],
            'descriptions' => ['tr' => $description, 'en' => $description, 'ar' => $description],
            'keywords' => ['tr' => [], 'en' => [], 'ar' => []],
            'focus_keyword' => '',
            'canonical_url' => '',
            'robots_meta' => ['index' => true, 'follow' => true, 'archive' => true],
            'og_type' => 'website',
            'twitter_card' => 'summary',
            'seo_score' => rand(80, 95),
        ]);
    }
    
    private function addCommonPages(): void
    {
        // Hakkımızda sayfası
        $page = Page::create([
            'title' => ['tr' => 'Hakkımızda', 'en' => 'About Us', 'ar' => 'من نحن'],
            'slug' => ['tr' => 'hakkimizda', 'en' => 'about-us', 'ar' => 'من-نحن'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 dark:text-gray-200 mb-6">Hakkımızda</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                                Kaliteli hizmet ve müşteri memnuniyeti odaklı çalışma prensiplerimizle sektörde öncü olmaya devam ediyoruz.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-16 items-center mb-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6">Misyonumuz</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">
                                    Modern teknolojiler kullanarak müşterilerimize en iyi hizmeti sunmak ve onların dijital dönüşüm süreçlerinde güvenilir bir partner olmak.
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    İnovasyon ve yaratıcılığı harmanlayarak, her projede fark yaratan çözümler üretmekteyiz.
                                </p>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
                                <h3 class="text-2xl font-bold mb-4">Neden Bizi Seçmelisiniz?</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-center"><span class="mr-3">✅</span> 10+ yıl deneyim</li>
                                    <li class="flex items-center"><span class="mr-3">✅</span> 500+ başarılı proje</li>
                                    <li class="flex items-center"><span class="mr-3">✅</span> 7/24 teknik destek</li>
                                    <li class="flex items-center"><span class="mr-3">✅</span> Yenilikçi çözümler</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-12">
                            <div class="text-center mb-12">
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-4">Değerlerimiz</h2>
                                <p class="text-gray-600 dark:text-gray-400">İş yapış şeklimizi şekillendiren temel değerlerimiz</p>
                            </div>
                            <div class="grid md:grid-cols-4 gap-8">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">🎯</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Kalite</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Her projede mükemmeli hedefliyoruz</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">🤝</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Güven</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Şeffaf ve dürüst iletişim</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">💡</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">İnovasyon</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Sürekli gelişim ve yenilik</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">⚡</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Hız</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Zamanında teslimat garantisi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 dark:text-gray-200 mb-6">About Us</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                                We continue to be a pioneer in the industry with our quality service and customer satisfaction focused working principles.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-16 items-center mb-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6">Our Mission</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">
                                    To provide the best service to our customers using modern technologies and to be a reliable partner in their digital transformation processes.
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    By blending innovation and creativity, we produce solutions that make a difference in every project.
                                </p>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
                                <h3 class="text-2xl font-bold mb-4">Why Choose Us?</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-center"><span class="mr-3">✅</span> 10+ years experience</li>
                                    <li class="flex items-center"><span class="mr-3">✅</span> 500+ successful projects</li>
                                    <li class="flex items-center"><span class="mr-3">✅</span> 24/7 technical support</li>
                                    <li class="flex items-center"><span class="mr-3">✅</span> Innovative solutions</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-12">
                            <div class="text-center mb-12">
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-4">Our Values</h2>
                                <p class="text-gray-600 dark:text-gray-400">Core values that shape our way of doing business</p>
                            </div>
                            <div class="grid md:grid-cols-4 gap-8">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">🎯</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Quality</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">We aim for perfection in every project</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">🤝</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Trust</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Transparent and honest communication</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">💡</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Innovation</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Continuous development and innovation</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">⚡</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Speed</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">On-time delivery guarantee</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 dark:text-gray-200 mb-6">من نحن</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                                نواصل كوننا رواد في هذا المجال بمبادئ عملنا التي تركز على الخدمة الجيدة ورضا العملاء.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-16 items-center mb-16">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6">مهمتنا</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">
                                    تقديم أفضل خدمة لعملائنا باستخدام التقنيات الحديثة وأن نكون شريكاً موثوقاً في عمليات التحول الرقمي الخاصة بهم.
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    من خلال مزج الابتكار والإبداع، ننتج حلولاً تصنع الفارق في كل مشروع.
                                </p>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
                                <h3 class="text-2xl font-bold mb-4">لماذا تختارنا؟</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-center space-x-reverse space-x-3"><span class="ml-3">✅</span> أكثر من 10 سنوات خبرة</li>
                                    <li class="flex items-center space-x-reverse space-x-3"><span class="ml-3">✅</span> أكثر من 500 مشروع ناجح</li>
                                    <li class="flex items-center space-x-reverse space-x-3"><span class="ml-3">✅</span> دعم فني 24/7</li>
                                    <li class="flex items-center space-x-reverse space-x-3"><span class="ml-3">✅</span> حلول مبتكرة</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-12">
                            <div class="text-center mb-12">
                                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-4">قيمنا</h2>
                                <p class="text-gray-600 dark:text-gray-400">القيم الأساسية التي تشكل طريقة عملنا</p>
                            </div>
                            <div class="grid md:grid-cols-4 gap-8">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">🎯</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">الجودة</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">نهدف للكمال في كل مشروع</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">🤝</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">الثقة</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">تواصل شفاف وصادق</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">💡</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">الابتكار</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">التطوير والابتكار المستمر</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl">⚡</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">السرعة</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">ضمان التسليم في الوقت المحدد</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => false,
        ]);

        $this->createSeoSetting($page, 'Hakkımızda', 'Şirketimiz hakkında detaylı bilgiler.');

        // İletişim sayfası
        $page = Page::create([
            'title' => ['tr' => 'İletişim', 'en' => 'Contact', 'ar' => 'اتصل بنا'],
            'slug' => ['tr' => 'iletisim', 'en' => 'contact', 'ar' => 'اتصل-بنا'],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 dark:text-gray-200 mb-6">İletişim</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">
                                Bizimle iletişime geçin. Size yardımcı olmak için buradayız.
                            </p>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-16">
                            <div class="space-y-12">
                                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-8">İletişim Bilgileri</h3>
                                    <div class="space-y-8">
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📍</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Adres</h4>
                                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    Teknokent Mahallesi<br>
                                                    İstanbul Üniversitesi Teknoparkı<br>
                                                    34469 Sarıyer/İstanbul
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📧</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">E-posta</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    <a href="mailto:info@example.com" class="hover:text-blue-600 dark:hover:text-blue-400">info@example.com</a><br>
                                                    <a href="mailto:destek@example.com" class="hover:text-blue-600 dark:hover:text-blue-400">destek@example.com</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📱</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Telefon</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    <a href="tel:+902125550123" class="hover:text-blue-600 dark:hover:text-blue-400">+90 212 555 0123</a><br>
                                                    <a href="tel:+905325550123" class="hover:text-blue-600 dark:hover:text-blue-400">+90 532 555 0123</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">🕒</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Çalışma Saatleri</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    Pazartesi - Cuma: 09:00 - 18:00<br>
                                                    Cumartesi: 10:00 - 16:00<br>
                                                    Pazar: Kapalı
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-8">Mesaj Gönderin</h3>
                                <form class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ad Soyad</label>
                                            <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">E-posta</label>
                                            <input type="email" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Konu</label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mesaj</label>
                                        <textarea rows="6" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors resize-none"></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-4 rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105">
                                        Mesaj Gönder 📧
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 dark:text-gray-200 mb-6">Contact</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">
                                Get in touch with us. We are here to help you.
                            </p>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-16">
                            <div class="space-y-12">
                                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-8">Contact Information</h3>
                                    <div class="space-y-8">
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📍</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Address</h4>
                                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    Teknokent District<br>
                                                    Istanbul University Technopark<br>
                                                    34469 Sarıyer/Istanbul
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📧</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Email</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    <a href="mailto:info@example.com" class="hover:text-blue-600 dark:hover:text-blue-400">info@example.com</a><br>
                                                    <a href="mailto:support@example.com" class="hover:text-blue-600 dark:hover:text-blue-400">support@example.com</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📱</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Phone</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    <a href="tel:+902125550123" class="hover:text-blue-600 dark:hover:text-blue-400">+90 212 555 0123</a><br>
                                                    <a href="tel:+905325550123" class="hover:text-blue-600 dark:hover:text-blue-400">+90 532 555 0123</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-14 h-14 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">🕒</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Working Hours</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    Monday - Friday: 09:00 - 18:00<br>
                                                    Saturday: 10:00 - 16:00<br>
                                                    Sunday: Closed
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-8">Send Message</h3>
                                <form class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                            <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                            <input type="email" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Message</label>
                                        <textarea rows="6" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors resize-none"></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-4 rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105">
                                        Send Message 📧
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 dark:text-gray-200 mb-6">اتصل بنا</h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">
                                تواصل معنا. نحن هنا لمساعدتك.
                            </p>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-16">
                            <div class="space-y-12">
                                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-8">معلومات الاتصال</h3>
                                    <div class="space-y-8">
                                        <div class="flex items-start space-x-4 space-x-reverse">
                                            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📍</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">العنوان</h4>
                                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    حي تكنوكنت<br>
                                                    حديقة جامعة اسطنبول التقنية<br>
                                                    34469 ساريير/اسطنبول
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4 space-x-reverse">
                                            <div class="w-14 h-14 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📧</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">البريد الإلكتروني</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    <a href="mailto:info@example.com" class="hover:text-blue-600 dark:hover:text-blue-400">info@example.com</a><br>
                                                    <a href="mailto:support@example.com" class="hover:text-blue-600 dark:hover:text-blue-400">support@example.com</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4 space-x-reverse">
                                            <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">📱</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">الهاتف</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    <a href="tel:+902125550123" class="hover:text-blue-600 dark:hover:text-blue-400">+90 212 555 0123</a><br>
                                                    <a href="tel:+905325550123" class="hover:text-blue-600 dark:hover:text-blue-400">+90 532 555 0123</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-4 space-x-reverse">
                                            <div class="w-14 h-14 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-2xl">🕒</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">ساعات العمل</h4>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    الاثنين - الجمعة: 09:00 - 18:00<br>
                                                    السبت: 10:00 - 16:00<br>
                                                    الأحد: مغلق
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-8">إرسال رسالة</h3>
                                <form class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">الاسم الكامل</label>
                                            <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
                                            <input type="email" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">الموضوع</label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">الرسالة</label>
                                        <textarea rows="6" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200 transition-colors resize-none"></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-4 rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105">
                                        إرسال الرسالة 📧
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
            'is_homepage' => false,
        ]);

        $this->createSeoSetting($page, 'İletişim', 'Bizimle iletişime geçin.');
    }
}