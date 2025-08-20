<?php

namespace Modules\Announcement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use App\Models\SeoSetting;

/**
 * Announcement Seeder for Central Database
 * Languages: tr, en, ar
 * Pattern: Same as PageSeederCentral
 */
class AnnouncementSeederCentral extends Seeder
{
    public function run(): void
    {
        $this->command->info('📢 Creating CENTRAL announcements (tr, en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = Announcement::count();
        if ($existingCount > 0) {
            $this->command->info("Announcements already exist in CENTRAL database ({$existingCount} announcements), skipping seeder...");
            return;
        }
        
        // Mevcut duyuruları sil (sadece boşsa)
        Announcement::truncate();
        SeoSetting::where('seoable_type', 'like', '%Announcement%')->delete();
        
        $this->createWelcomeAnnouncement();
        $this->createNewProjectsAnnouncement();
        $this->createTechUpdatesAnnouncement();
        $this->createAILaunchAnnouncement();
        $this->createMaintenanceAnnouncement();
        
        $this->command->info('✅ Central announcements created: 5 announcements (tr, en, ar)');
    }
    
    private function createWelcomeAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Hoş Geldiniz! - Türk Bilişim Platformu',
                'en' => 'Welcome! - Turkish Tech Platform',
                'ar' => 'مرحباً بكم! - منصة تورك بيليشيم'
            ],
            'slug' => [
                'tr' => 'hos-geldiniz-turk-bilisim',
                'en' => 'welcome-turkish-tech',
                'ar' => 'مرحبا-بكم-تورك-بيليشيم'
            ],
            'body' => [
                'tr' => '<div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🎉 Sitemize Hoş Geldiniz!</h2>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                            <strong>Türk Bilişim</strong> olarak teknoloji alanında yenilikçi çözümler sunuyoruz. 
                            Kurumsal yapay zeka sistemleri, web tasarım, mobil uygulama ve e-ticaret projelerinizde yanınızdayız.
                        </p>
                        
                        <div class="grid md:grid-cols-3 gap-6 my-8">
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400 mb-3">🧠 Yapay Zeka</h3>
                                <p class="text-gray-600 dark:text-gray-300">Size özel eğitilmiş AI sistemleri</p>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-purple-600 dark:text-purple-400 mb-3">💻 Web Tasarım</h3>
                                <p class="text-gray-600 dark:text-gray-300">Modern ve responsive çözümler</p>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-pink-600 dark:text-pink-400 mb-3">📱 Mobil App</h3>
                                <p class="text-gray-600 dark:text-gray-300">Cross-platform uygulamalar</p>
                            </div>
                        </div>
                        
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            <em>🚀 2025 yılında teknolojinin gücünü işinize entegre edin. Başarı hikayenizi birlikte yazalım!</em>
                        </p>
                    </div>
                </div>',
                'en' => '<div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🎉 Welcome to Our Platform!</h2>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                            As <strong>Turkish Tech</strong>, we offer innovative solutions in the field of technology. 
                            We are with you in enterprise AI systems, web design, mobile application and e-commerce projects.
                        </p>
                        
                        <div class="grid md:grid-cols-3 gap-6 my-8">
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400 mb-3">🧠 Artificial Intelligence</h3>
                                <p class="text-gray-600 dark:text-gray-300">Custom trained AI systems for you</p>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-purple-600 dark:text-purple-400 mb-3">💻 Web Design</h3>
                                <p class="text-gray-600 dark:text-gray-300">Modern and responsive solutions</p>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-pink-600 dark:text-pink-400 mb-3">📱 Mobile Apps</h3>
                                <p class="text-gray-600 dark:text-gray-300">Cross-platform applications</p>
                            </div>
                        </div>
                        
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            <em>🚀 Integrate the power of technology into your business in 2025. Let\'s write your success story together!</em>
                        </p>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 p-8 rounded-lg" dir="rtl">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🎉 مرحباً بكم في منصتنا!</h2>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                            نحن في <strong>تورك بيليشيم</strong> نقدم حلولاً مبتكرة في مجال التكنولوجيا. 
                            نحن معكم في أنظمة الذكاء الاصطناعي المؤسسية وتصميم الويب وتطبيقات الهاتف المحمول ومشاريع التجارة الإلكترونية.
                        </p>
                        
                        <div class="grid md:grid-cols-3 gap-6 my-8">
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400 mb-3">🧠 الذكاء الاصطناعي</h3>
                                <p class="text-gray-600 dark:text-gray-300">أنظمة ذكاء اصطناعي مدربة خصيصاً لك</p>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-purple-600 dark:text-purple-400 mb-3">💻 تصميم الويب</h3>
                                <p class="text-gray-600 dark:text-gray-300">حلول حديثة ومتجاوبة</p>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-pink-600 dark:text-pink-400 mb-3">📱 تطبيقات الهاتف</h3>
                                <p class="text-gray-600 dark:text-gray-300">تطبيقات متعددة المنصات</p>
                            </div>
                        </div>
                        
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            <em>🚀 دمج قوة التكنولوجيا في عملك في عام 2025. دعونا نكتب قصة نجاحكم معاً!</em>
                        </p>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Hoş Geldiniz - Türk Bilişim Platformu',
            'Welcome - Turkish Tech Platform',
            'مرحباً بكم - منصة تورك بيليشيم',
            'Türk Bilişim olarak teknoloji alanında yenilikçi çözümler sunuyoruz. Kurumsal yapay zeka sistemleri, web tasarım, mobil uygulama ve e-ticaret projelerinizde yanınızdayız.',
            'As Turkish Tech, we offer innovative solutions in the field of technology. We are with you in enterprise AI systems, web design, mobile application and e-commerce projects.',
            'نحن في تورك بيليشيم نقدم حلولاً مبتكرة في مجال التكنولوجيا. نحن معكم في أنظمة الذكاء الاصطناعي المؤسسية وتصميم الويب وتطبيقات الهاتف المحمول ومشاريع التجارة الإلكترونية.'
        );
        
        $this->command->info('✅ Announcement created: Hoş Geldiniz');
    }
    
    private function createNewProjectsAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => '2025 Yılı Yeni Projelerimiz ve Hedeflerimiz',
                'en' => 'Our New Projects and Goals for 2025',
                'ar' => 'مشاريعنا وأهدافنا الجديدة لعام 2025'
            ],
            'slug' => [
                'tr' => 'yeni-projelerimiz-2025',
                'en' => 'our-new-projects-2025',
                'ar' => 'مشاريعنا-الجديدة-2025'
            ],
            'body' => [
                'tr' => '<div class="bg-white dark:bg-gray-800 p-8 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">🚀 2025 Yılı Projelerimiz</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Bu yıl birçok yeni projeyi hayata geçiriyoruz. Modern teknolojiler kullanarak müşterilerimize en iyi hizmeti sunmaya devam ediyoruz.
                        </p>
                        
                        <div class="bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📈 Hedeflerimiz:</h3>
                            <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                <li>✅ 50+ yeni müşteri kazanımı</li>
                                <li>✅ AI çözümlerinde %300 büyüme</li>
                                <li>✅ Uluslararası pazara giriş</li>
                                <li>✅ 10+ yeni teknoloji ortaklığı</li>
                            </ul>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 dark:text-green-300 mb-2">🎯 Q1 Projeler</h4>
                                <p class="text-sm text-green-700 dark:text-green-400">E-ticaret AI asistanı ve çoklu dil desteği</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">🎯 Q2 Projeler</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-400">Mobil AI uygulaması ve API genişletmesi</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="bg-white dark:bg-gray-800 p-8 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">🚀 Our 2025 Projects</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            We are implementing many new projects this year. We continue to provide the best service to our customers using modern technologies.
                        </p>
                        
                        <div class="bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📈 Our Goals:</h3>
                            <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                <li>✅ 50+ new customer acquisition</li>
                                <li>✅ 300% growth in AI solutions</li>
                                <li>✅ International market entry</li>
                                <li>✅ 10+ new technology partnerships</li>
                            </ul>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 dark:text-green-300 mb-2">🎯 Q1 Projects</h4>
                                <p class="text-sm text-green-700 dark:text-green-400">E-commerce AI assistant and multilingual support</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">🎯 Q2 Projects</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-400">Mobile AI application and API expansion</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-white dark:bg-gray-800 p-8 rounded-lg border border-gray-200 dark:border-gray-700" dir="rtl">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">🚀 مشاريعنا لعام 2025</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            نحن ننفذ العديد من المشاريع الجديدة هذا العام. نواصل تقديم أفضل خدمة لعملائنا باستخدام التقنيات الحديثة.
                        </p>
                        
                        <div class="bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📈 أهدافنا:</h3>
                            <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                <li>✅ اكتساب 50+ عميل جديد</li>
                                <li>✅ نمو 300% في حلول الذكاء الاصطناعي</li>
                                <li>✅ دخول الأسواق الدولية</li>
                                <li>✅ 10+ شراكات تقنية جديدة</li>
                            </ul>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 dark:text-green-300 mb-2">🎯 مشاريع الربع الأول</h4>
                                <p class="text-sm text-green-700 dark:text-green-400">مساعد ذكي للتجارة الإلكترونية ودعم متعدد اللغات</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">🎯 مشاريع الربع الثاني</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-400">تطبيق ذكي للهاتف المحمول وتوسيع API</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            '2025 Yılı Yeni Projelerimiz ve Hedeflerimiz',
            'Our New Projects and Goals for 2025',
            'مشاريعنا وأهدافنا الجديدة لعام 2025',
            'Bu yıl birçok yeni projeyi hayata geçiriyoruz. Modern teknolojiler kullanarak müşterilerimize en iyi hizmeti sunmaya devam ediyoruz.',
            'We are implementing many new projects this year. We continue to provide the best service to our customers using modern technologies.',
            'نحن ننفذ العديد من المشاريع الجديدة هذا العام. نواصل تقديم أفضل خدمة لعملائنا باستخدام التقنيات الحديثة.'
        );
        
        $this->command->info('✅ Announcement created: Yeni Projelerimiz');
    }
    
    private function createTechUpdatesAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Teknoloji Güncellemeleri ve Yenilikler',
                'en' => 'Technology Updates and Innovations',
                'ar' => 'تحديثات وابتكارات التكنولوجيا'
            ],
            'slug' => [
                'tr' => 'teknoloji-guncellemeleri',
                'en' => 'technology-updates',
                'ar' => 'تحديثات-التكنولوجيا'
            ],
            'body' => [
                'tr' => '<div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-300 mb-6">⚡ Son Teknoloji Trendleri</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-purple-800 dark:text-purple-200">
                            Teknoloji dünyasındaki gelişmeleri takip ediyor ve projelerimizde en güncel yöntemleri kullanıyoruz.
                        </p>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-purple-500">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🤖 AI & Machine Learning</h3>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>• GPT-4 Turbo entegrasyonu</li>
                                    <li>• Claude 3.5 Sonnet desteği</li>
                                    <li>• Custom model eğitimi</li>
                                    <li>• Çoklu dil AI çevirisi</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🌐 Web Technologies</h3>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>• Laravel 11 framework</li>
                                    <li>• Alpine.js & Livewire</li>
                                    <li>• Tailwind CSS v4</li>
                                    <li>• PWA uygulamaları</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-yellow-800 dark:text-yellow-300 text-sm">
                                <strong>💡 İpucu:</strong> Bu teknolojilerin hepsini projelerinizde kullanabilirsiniz. 
                                Detaylı bilgi için bizimle iletişime geçin!
                            </p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-300 mb-6">⚡ Latest Technology Trends</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-purple-800 dark:text-purple-200">
                            We follow the developments in the technology world and use the most current methods in our projects.
                        </p>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-purple-500">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🤖 AI & Machine Learning</h3>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>• GPT-4 Turbo integration</li>
                                    <li>• Claude 3.5 Sonnet support</li>
                                    <li>• Custom model training</li>
                                    <li>• Multilingual AI translation</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🌐 Web Technologies</h3>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>• Laravel 11 framework</li>
                                    <li>• Alpine.js & Livewire</li>
                                    <li>• Tailwind CSS v4</li>
                                    <li>• PWA applications</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-yellow-800 dark:text-yellow-300 text-sm">
                                <strong>💡 Tip:</strong> You can use all of these technologies in your projects. 
                                Contact us for detailed information!
                            </p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-8 rounded-lg" dir="rtl">
                    <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-300 mb-6">⚡ أحدث اتجاهات التكنولوجيا</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-purple-800 dark:text-purple-200">
                            نتابع التطورات في عالم التكنولوجيا ونستخدم أحدث الطرق في مشاريعنا.
                        </p>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-r-4 border-purple-500">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🤖 الذكاء الاصطناعي والتعلم الآلي</h3>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>• تكامل GPT-4 Turbo</li>
                                    <li>• دعم Claude 3.5 Sonnet</li>
                                    <li>• تدريب النماذج المخصصة</li>
                                    <li>• ترجمة ذكية متعددة اللغات</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-r-4 border-blue-500">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🌐 تقنيات الويب</h3>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                    <li>• إطار عمل Laravel 11</li>
                                    <li>• Alpine.js و Livewire</li>
                                    <li>• Tailwind CSS v4</li>
                                    <li>• تطبيقات PWA</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-yellow-800 dark:text-yellow-300 text-sm">
                                <strong>💡 نصيحة:</strong> يمكنك استخدام كل هذه التقنيات في مشاريعك. 
                                اتصل بنا للحصول على معلومات مفصلة!
                            </p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Teknoloji Güncellemeleri ve Yenilikler',
            'Technology Updates and Innovations',
            'تحديثات وابتكارات التكنولوجيا',
            'Teknoloji dünyasındaki gelişmeleri takip ediyor ve projelerimizde en güncel yöntemleri kullanıyoruz.',
            'We follow the developments in the technology world and use the most current methods in our projects.',
            'نتابع التطورات في عالم التكنولوجيا ونستخدم أحدث الطرق في مشاريعنا.'
        );
        
        $this->command->info('✅ Announcement created: Teknoloji Güncellemeleri');
    }
    
    private function createAILaunchAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Yapay Zeka Platformumuz Yayında!',
                'en' => 'Our AI Platform is Live!',
                'ar' => 'منصة الذكاء الاصطناعي الخاصة بنا مباشرة!'
            ],
            'slug' => [
                'tr' => 'yapay-zeka-platformu-yayinda',
                'en' => 'ai-platform-live',
                'ar' => 'منصة-الذكاء-الاصطناعي-مباشرة'
            ],
            'body' => [
                'tr' => '<div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 p-8 rounded-lg border-2 border-green-200 dark:border-green-800">
                    <h2 class="text-2xl font-bold text-green-900 dark:text-green-300 mb-6">🎊 Büyük Duyuru: AI Platform Lansmanı!</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-green-800 dark:text-green-200">
                            Aylar süren geliştirme sürecinin ardından, kurumsal yapay zeka platformumuz artık kullanıma hazır!
                        </p>
                        
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">🚀 Platform Özellikleri:</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Türkçe optimized AI models
                                    </div>
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Gerçek zamanlı çeviri desteği
                                    </div>
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Custom training options
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        RESTful API integration
                                    </div>
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        Advanced analytics dashboard
                                    </div>
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        Enterprise-grade security
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/20 dark:to-red-900/20 p-4 rounded-lg">
                            <p class="text-center text-orange-800 dark:text-orange-300 font-semibold">
                                🎁 İlk 100 kullanıcıya özel %50 indirim! Fırsatı kaçırmayın.
                            </p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 p-8 rounded-lg border-2 border-green-200 dark:border-green-800">
                    <h2 class="text-2xl font-bold text-green-900 dark:text-green-300 mb-6">🎊 Big Announcement: AI Platform Launch!</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-green-800 dark:text-green-200">
                            After months of development process, our enterprise AI platform is now ready for use!
                        </p>
                        
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">🚀 Platform Features:</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Turkish optimized AI models
                                    </div>
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Real-time translation support
                                    </div>
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Custom training options
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        RESTful API integration
                                    </div>
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        Advanced analytics dashboard
                                    </div>
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        Enterprise-grade security
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/20 dark:to-red-900/20 p-4 rounded-lg">
                            <p class="text-center text-orange-800 dark:text-orange-300 font-semibold">
                                🎁 Special 50% discount for the first 100 users! Don\'t miss the opportunity.
                            </p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 p-8 rounded-lg border-2 border-green-200 dark:border-green-800" dir="rtl">
                    <h2 class="text-2xl font-bold text-green-900 dark:text-green-300 mb-6">🎊 إعلان كبير: إطلاق منصة الذكاء الاصطناعي!</h2>
                    
                    <div class="space-y-6">
                        <p class="text-lg text-green-800 dark:text-green-200">
                            بعد أشهر من عملية التطوير، منصة الذكاء الاصطناعي المؤسسية الخاصة بنا جاهزة الآن للاستخدام!
                        </p>
                        
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">🚀 ميزات المنصة:</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full ml-3"></span>
                                        نماذج ذكاء اصطناعي محسنة للتركية
                                    </div>
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full ml-3"></span>
                                        دعم الترجمة في الوقت الفعلي
                                    </div>
                                    <div class="flex items-center text-green-700 dark:text-green-300">
                                        <span class="w-2 h-2 bg-green-500 rounded-full ml-3"></span>
                                        خيارات التدريب المخصصة
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full ml-3"></span>
                                        تكامل RESTful API
                                    </div>
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full ml-3"></span>
                                        لوحة تحليلات متقدمة
                                    </div>
                                    <div class="flex items-center text-blue-700 dark:text-blue-300">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full ml-3"></span>
                                        أمان على مستوى المؤسسة
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/20 dark:to-red-900/20 p-4 rounded-lg">
                            <p class="text-center text-orange-800 dark:text-orange-300 font-semibold">
                                🎁 خصم خاص 50% لأول 100 مستخدم! لا تفوت الفرصة.
                            </p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Yapay Zeka Platformumuz Yayında!',
            'Our AI Platform is Live!',
            'منصة الذكاء الاصطناعي الخاصة بنا مباشرة!',
            'Aylar süren geliştirme sürecinin ardından, kurumsal yapay zeka platformumuz artık kullanıma hazır!',
            'After months of development process, our enterprise AI platform is now ready for use!',
            'بعد أشهر من عملية التطوير، منصة الذكاء الاصطناعي المؤسسية الخاصة بنا جاهزة الآن للاستخدام!'
        );
        
        $this->command->info('✅ Announcement created: Yapay Zeka Platformu');
    }
    
    private function createMaintenanceAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Sistem Bakım ve Güncelleme Duyurusu',
                'en' => 'System Maintenance and Update Notice',
                'ar' => 'إشعار صيانة وتحديث النظام'
            ],
            'slug' => [
                'tr' => 'sistem-bakim-duyurusu',
                'en' => 'system-maintenance-notice',
                'ar' => 'إشعار-صيانة-النظام'
            ],
            'body' => [
                'tr' => '<div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 p-8 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <h2 class="text-2xl font-bold text-yellow-900 dark:text-yellow-300 mb-6">⚠️ Planlı Sistem Bakımı</h2>
                    
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📅 Bakım Detayları:</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">🕐 Tarih ve Saat:</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">25 Ağustos 2025, Pazar</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">02:00 - 06:00 (GMT+3)</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">⏱️ Tahmini Süre:</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">4 saat</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">(En geç 06:00\'da tamamlanacak)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">🔧 Yapılacak İşlemler:</h3>
                            <ul class="space-y-2 text-blue-800 dark:text-blue-200 text-sm">
                                <li>• Veritabanı optimizasyonu ve güvenlik güncellemeleri</li>
                                <li>• AI model performans iyileştirmeleri</li>
                                <li>• Yeni API endpoint eklentileri</li>
                                <li>• Sunucu altyapısı güçlendirme</li>
                                <li>• Backup ve recovery testleri</li>
                            </ul>
                        </div>
                        
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                            <p class="text-red-800 dark:text-red-300 text-sm font-medium">
                                ⚠️ <strong>Önemli:</strong> Bu süre zarfında platform geçici olarak kullanılamayacaktır. 
                                Devam eden işlemlerinizi önceden tamamlamanızı öneriyoruz.
                            </p>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <p class="text-green-800 dark:text-green-300 text-sm">
                                💚 Anlayışınız için teşekkür ederiz. Bakım sonrası daha hızlı ve güvenli bir platform deneyimi yaşayacaksınız!
                            </p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 p-8 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <h2 class="text-2xl font-bold text-yellow-900 dark:text-yellow-300 mb-6">⚠️ Scheduled System Maintenance</h2>
                    
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📅 Maintenance Details:</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">🕐 Date and Time:</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">August 25, 2025, Sunday</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">02:00 - 06:00 (GMT+3)</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">⏱️ Estimated Duration:</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">4 hours</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">(Will be completed by 06:00 at the latest)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">🔧 Tasks to be Performed:</h3>
                            <ul class="space-y-2 text-blue-800 dark:text-blue-200 text-sm">
                                <li>• Database optimization and security updates</li>
                                <li>• AI model performance improvements</li>
                                <li>• New API endpoint additions</li>
                                <li>• Server infrastructure strengthening</li>
                                <li>• Backup and recovery tests</li>
                            </ul>
                        </div>
                        
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                            <p class="text-red-800 dark:text-red-300 text-sm font-medium">
                                ⚠️ <strong>Important:</strong> The platform will be temporarily unavailable during this time. 
                                We recommend completing your ongoing tasks in advance.
                            </p>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <p class="text-green-800 dark:text-green-300 text-sm">
                                💚 Thank you for your understanding. You will experience a faster and more secure platform after maintenance!
                            </p>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 p-8 rounded-lg border border-yellow-200 dark:border-yellow-800" dir="rtl">
                    <h2 class="text-2xl font-bold text-yellow-900 dark:text-yellow-300 mb-6">⚠️ صيانة مجدولة للنظام</h2>
                    
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📅 تفاصيل الصيانة:</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">🕐 التاريخ والوقت:</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">25 أغسطس 2025، الأحد</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">02:00 - 06:00 (GMT+3)</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">⏱️ المدة المقدرة:</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">4 ساعات</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">(سيتم الانتهاء بحلول الساعة 06:00 على أقصى تقدير)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">🔧 المهام التي سيتم تنفيذها:</h3>
                            <ul class="space-y-2 text-blue-800 dark:text-blue-200 text-sm">
                                <li>• تحسين قاعدة البيانات وتحديثات الأمان</li>
                                <li>• تحسينات أداء نموذج الذكاء الاصطناعي</li>
                                <li>• إضافات نقطة نهاية API جديدة</li>
                                <li>• تعزيز البنية التحتية للخادم</li>
                                <li>• اختبارات النسخ الاحتياطي والاستعادة</li>
                            </ul>
                        </div>
                        
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                            <p class="text-red-800 dark:text-red-300 text-sm font-medium">
                                ⚠️ <strong>مهم:</strong> ستكون المنصة غير متاحة مؤقتاً خلال هذا الوقت. 
                                نوصي بإكمال مهامك الجارية مسبقاً.
                            </p>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <p class="text-green-800 dark:text-green-300 text-sm">
                                💚 شكراً لتفهمكم. ستحصلون على تجربة منصة أسرع وأكثر أماناً بعد الصيانة!
                            </p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Sistem Bakım ve Güncelleme Duyurusu',
            'System Maintenance and Update Notice',
            'إشعار صيانة وتحديث النظام',
            'Planlı sistem bakımı hakkında önemli duyuru. Bu süre zarfında platform geçici olarak kullanılamayacaktır.',
            'Important announcement about scheduled system maintenance. The platform will be temporarily unavailable during this time.',
            'إعلان مهم حول صيانة النظام المجدولة. ستكون المنصة غير متاحة مؤقتاً خلال هذا الوقت.'
        );
        
        $this->command->info('✅ Announcement created: Sistem Bakım Duyurusu');
    }

    /**
     * Create SEO settings for announcement
     */
    private function createSeoSetting($announcement, $titleTr, $titleEn, $titleAr, $descTr, $descEn, $descAr): void
    {
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
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
                'tr' => $descTr,
                'en' => $descEn,
                'ar' => $descAr
            ],
            'keywords' => [
                'tr' => ['duyuru', 'haber', 'teknoloji', 'bilişim', 'yapay zeka'],
                'en' => ['announcement', 'news', 'technology', 'informatics', 'artificial intelligence'],
                'ar' => ['إعلان', 'أخبار', 'تكنولوجيا', 'معلوماتية', 'ذكاء اصطناعي']
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