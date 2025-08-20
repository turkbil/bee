<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use App\Models\SeoSetting;

/**
 * Announcement Seeder for Tenant3 Database
 * Languages: en, ar
 */
class AnnouncementSeederTenant3 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT3 announcements (en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = Announcement::count();
        if ($existingCount > 0) {
            $this->command->info("Announcements already exist in TENANT3 database ({$existingCount} announcements), skipping seeder...");
            return;
        }
        
        // Mevcut duyuruları sil (sadece boşsa)
        Announcement::truncate();
        SeoSetting::where('seoable_type', 'like', '%Announcement%')->delete();
        
        $this->createWelcomeAnnouncement();
        $this->createAIAnnouncement();
        $this->createTechUpdateAnnouncement();
    }
    
    private function createWelcomeAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Welcome - Tenant3 Platform',
                'ar' => 'مرحباً بكم - منصة Tenant3'
            ],
            'slug' => [
                'en' => 'welcome-tenant3',
                'ar' => 'مرحبا-بكم-tenant3'
            ],
            'body' => [
                'en' => '<div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">🎉 Welcome to Tenant3 Platform!</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        We provide innovative technology solutions for global businesses.
                    </p>
                    <div class="grid md:grid-cols-2 gap-6 my-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-indigo-600 mb-3">🤖 AI Solutions</h3>
                            <p class="text-gray-600">Advanced artificial intelligence systems</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-purple-600 mb-3">🌐 Global Services</h3>
                            <p class="text-gray-600">International business solutions</p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-lg" dir="rtl">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">🎉 مرحباً بكم في منصة Tenant3!</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        نحن نقدم حلول تكنولوجية مبتكرة للشركات العالمية.
                    </p>
                    <div class="grid md:grid-cols-2 gap-6 my-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-indigo-600 mb-3">🤖 حلول الذكاء الاصطناعي</h3>
                            <p class="text-gray-600">أنظمة ذكاء اصطناعي متقدمة</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-purple-600 mb-3">🌐 الخدمات العالمية</h3>
                            <p class="text-gray-600">حلول الأعمال الدولية</p>
                        </div>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'en' => 'We provide innovative technology solutions for global businesses.',
                'ar' => 'نحن نقدم حلول تكنولوجية مبتكرة للشركات العالمية.'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Welcome - Tenant3 Platform',
            'مرحباً بكم - منصة Tenant3',
            'We provide innovative technology solutions for global businesses.',
            'نحن نقدم حلول تكنولوجية مبتكرة للشركات العالمية.'
        );
    }
    
    private function createAIAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'AI Platform Launch',
                'ar' => 'إطلاق منصة الذكاء الاصطناعي'
            ],
            'slug' => [
                'en' => 'ai-platform-launch',
                'ar' => 'إطلاق-منصة-الذكاء-الاصطناعي'
            ],
            'body' => [
                'en' => '<div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-blue-900 mb-6">🚀 Revolutionary AI Platform Launch!</h2>
                    <div class="space-y-6">
                        <p class="text-lg text-blue-800">
                            Our cutting-edge AI platform is now available for enterprise customers worldwide.
                        </p>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">🌟 Key Features:</h3>
                            <ul class="space-y-2 text-gray-700">
                                <li>• Multi-language AI models</li>
                                <li>• Real-time processing</li>
                                <li>• Custom training capabilities</li>
                                <li>• Enterprise-grade security</li>
                                <li>• Global API access</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-green-800 font-semibold">
                                🎁 Early adopters get 6 months free premium support!
                            </p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-8 rounded-lg" dir="rtl">
                    <h2 class="text-2xl font-bold text-blue-900 mb-6">🚀 إطلاق منصة ذكاء اصطناعي ثورية!</h2>
                    <div class="space-y-6">
                        <p class="text-lg text-blue-800">
                            منصة الذكاء الاصطناعي المتطورة متاحة الآن للعملاء المؤسسيين في جميع أنحاء العالم.
                        </p>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">🌟 الميزات الرئيسية:</h3>
                            <ul class="space-y-2 text-gray-700">
                                <li>• نماذج ذكاء اصطناعي متعددة اللغات</li>
                                <li>• معالجة في الوقت الفعلي</li>
                                <li>• قدرات التدريب المخصصة</li>
                                <li>• أمان على مستوى المؤسسة</li>
                                <li>• وصول API عالمي</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-green-800 font-semibold">
                                🎁 المتبنون الأوائل يحصلون على دعم مجاني لمدة 6 أشهر!
                            </p>
                        </div>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'en' => 'Our cutting-edge AI platform is now available for enterprise customers worldwide.',
                'ar' => 'منصة الذكاء الاصطناعي المتطورة متاحة الآن للعملاء المؤسسيين في جميع أنحاء العالم.'
            ],
            'is_active' => true,
            'published_at' => now()->subDays(2),
        ]);

        $this->createSeoSetting(
            $announcement,
            'AI Platform Launch - Tenant3',
            'إطلاق منصة الذكاء الاصطناعي - Tenant3',
            'Our cutting-edge AI platform is now available for enterprise customers worldwide.',
            'منصة الذكاء الاصطناعي المتطورة متاحة الآن للعملاء المؤسسيين في جميع أنحاء العالم.'
        );
    }
    
    private function createTechUpdateAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Technology Infrastructure Update',
                'ar' => 'تحديث البنية التحتية التكنولوجية'
            ],
            'slug' => [
                'en' => 'tech-infrastructure-update',
                'ar' => 'تحديث-البنية-التحتية-التكنولوجية'
            ],
            'body' => [
                'en' => '<div class="bg-gradient-to-r from-gray-50 to-slate-50 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">⚡ Major Infrastructure Upgrade Complete</h2>
                    <div class="space-y-6">
                        <p class="text-lg text-gray-700">
                            We have successfully upgraded our global infrastructure to provide better performance and reliability.
                        </p>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">📈 Performance Improvements</h3>
                                <ul class="space-y-2 text-gray-600 text-sm">
                                    <li>• 50% faster response times</li>
                                    <li>• 99.9% uptime guarantee</li>
                                    <li>• Enhanced global CDN</li>
                                    <li>• Optimized database queries</li>
                                </ul>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">🔒 Security Enhancements</h3>
                                <ul class="space-y-2 text-gray-600 text-sm">
                                    <li>• Advanced encryption</li>
                                    <li>• Multi-factor authentication</li>
                                    <li>• Real-time threat detection</li>
                                    <li>• Compliance certifications</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-r from-gray-50 to-slate-50 p-8 rounded-lg" dir="rtl">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">⚡ اكتمال ترقية كبرى للبنية التحتية</h2>
                    <div class="space-y-6">
                        <p class="text-lg text-gray-700">
                            لقد نجحنا في ترقية بنيتنا التحتية العالمية لتوفير أداء وموثوقية أفضل.
                        </p>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">📈 تحسينات الأداء</h3>
                                <ul class="space-y-2 text-gray-600 text-sm">
                                    <li>• أوقات استجابة أسرع بنسبة 50%</li>
                                    <li>• ضمان وقت تشغيل 99.9%</li>
                                    <li>• CDN عالمي محسن</li>
                                    <li>• استعلامات قاعدة بيانات محسنة</li>
                                </ul>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">🔒 تحسينات الأمان</h3>
                                <ul class="space-y-2 text-gray-600 text-sm">
                                    <li>• تشفير متقدم</li>
                                    <li>• مصادقة متعددة العوامل</li>
                                    <li>• كشف التهديدات في الوقت الفعلي</li>
                                    <li>• شهادات الامتثال</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'en' => 'We have successfully upgraded our global infrastructure to provide better performance and reliability.',
                'ar' => 'لقد نجحنا في ترقية بنيتنا التحتية العالمية لتوفير أداء وموثوقية أفضل.'
            ],
            'is_active' => true,
            'published_at' => now()->subDays(5),
        ]);

        $this->createSeoSetting(
            $announcement,
            'Technology Infrastructure Update - Tenant3',
            'تحديث البنية التحتية التكنولوجية - Tenant3',
            'We have successfully upgraded our global infrastructure to provide better performance and reliability.',
            'لقد نجحنا في ترقية بنيتنا التحتية العالمية لتوفير أداء وموثوقية أفضل.'
        );
    }

    private function createSeoSetting($announcement, $titleEn, $titleAr, $descriptionEn, $descriptionAr): void
    {
        // DEBUG: Parametreleri kontrol et
        $this->command->info("DEBUG - Creating SEO for announcement {$announcement->announcement_id}");
        $this->command->info("Title EN: {$titleEn}");
        $this->command->info("Title AR: {$titleAr}");
        
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($announcement->seoSetting()->exists()) {
            $this->command->info("DEBUG - Deleting existing SEO setting");
            $announcement->seoSetting()->delete();
        }
        
        $announcement->seoSetting()->create([
            'titles' => [
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'descriptions' => [
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'keywords' => [
                'en' => ['announcement', 'news', 'technology', 'ai'],
                'ar' => ['إعلان', 'أخبار', 'تكنولوجيا', 'ذكاء اصطناعي']
            ],
            'og_titles' => [
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'og_descriptions' => [
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'available_languages' => ['en', 'ar'],
            'default_language' => 'en',
            'seo_score' => rand(80, 95),
        ]);
    }
}