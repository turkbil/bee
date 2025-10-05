<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;

/**
 * Page Seeder for Tenant 1 (Production)
 *
 * Creates essential pages for production tenant
 * Homepage MUST be ID=1 for frontend routing
 */
class PageSeederTenant1 extends Seeder
{
    public function run(): void
    {
        // Tenant context check
        if (!tenancy()->initialized) {
            $this->command->warn("⚠️  PageSeederTenant1 sadece tenant database'de çalışır. Atlanıyor...");
            return;
        }

        $this->command->info('🚀 Starting Tenant 1 (Production) Page Seeding...');

        // Duplicate check
        if (Page::count() > 0) {
            $this->command->warn("⚠️  Pages already exist, skipping");
            return;
        }

        // 1. HOMEPAGE (id MUST be 1)
        $this->command->info('  → Creating Homepage (id=1)');

        $homepage = Page::create([
            'title' => [
                'tr' => 'Anasayfa',
                'ar' => 'الصفحة الرئيسية'
            ],
            'slug' => [
                'tr' => 'anasayfa',
                'ar' => 'الصفحة-الرئيسية'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <section class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-6">
                            Hoş Geldiniz
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                            Modern ve güçlü Laravel CMS sistemi ile web sitenizi yönetin
                        </p>
                    </section>

                    <section class="grid md:grid-cols-3 gap-8 mt-12">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                            <div class="text-4xl mb-4">🚀</div>
                            <h3 class="text-xl font-bold mb-2">Hızlı</h3>
                            <p class="text-gray-600 dark:text-gray-400">Modern teknolojilerle optimize edilmiş</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                            <div class="text-4xl mb-4">🎨</div>
                            <h3 class="text-xl font-bold mb-2">Esnek</h3>
                            <p class="text-gray-600 dark:text-gray-400">İhtiyacınıza göre özelleştirilebilir</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                            <div class="text-4xl mb-4">🔒</div>
                            <h3 class="text-xl font-bold mb-2">Güvenli</h3>
                            <p class="text-gray-600 dark:text-gray-400">Enterprise seviye güvenlik</p>
                        </div>
                    </section>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <section class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-6">
                            مرحبا بك
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                            قم بإدارة موقع الويب الخاص بك باستخدام نظام Laravel CMS الحديث والقوي
                        </p>
                    </section>
                </div>'
            ],
            'excerpt' => [
                'tr' => 'Modern Laravel CMS ile web sitenizi kolayca yönetin',
                'ar' => 'قم بإدارة موقع الويب الخاص بك بسهولة باستخدام Laravel CMS الحديث'
            ],
            'is_published' => true,
            'is_homepage' => true,
            'published_at' => now(),
        ]);

        // SEO Settings
        $homepage->seoSetting()->create([
            'titles' => [
                'tr' => 'Anasayfa - Modern CMS',
                'ar' => 'الصفحة الرئيسية - نظام إدارة المحتوى الحديث'
            ],
            'descriptions' => [
                'tr' => 'Modern ve güçlü Laravel CMS sistemi ile web sitenizi kolayca yönetin',
                'ar' => 'قم بإدارة موقع الويب الخاص بك بسهولة باستخدام نظام Laravel CMS الحديث والقوي'
            ],
            'keywords' => [
                'tr' => ['cms', 'laravel', 'web yönetimi', 'modern'],
                'ar' => ['نظام إدارة المحتوى', 'لارافيل', 'إدارة الويب', 'حديث']
            ],
        ]);

        $this->command->info('  ✓ Homepage created (id=' . $homepage->id . ')');

        // 2. About Page
        $this->command->info('  → Creating About page');

        $about = Page::create([
            'title' => [
                'tr' => 'Hakkımızda',
                'ar' => 'معلومات عنا'
            ],
            'slug' => [
                'tr' => 'hakkimizda',
                'ar' => 'معلومات-عنا'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-4xl font-bold mb-8">Hakkımızda</h1>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                        Modern web teknolojileri ile güçlendirilmiş CMS sistemimiz,
                        işletmenizin dijital varlığını yönetmenizi kolaylaştırır.
                    </p>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-4xl font-bold mb-8">معلومات عنا</h1>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                        نظام إدارة المحتوى الخاص بنا المدعوم بتقنيات الويب الحديثة
                    </p>
                </div>'
            ],
            'excerpt' => [
                'tr' => 'Kurumsal CMS çözümlerimiz hakkında bilgi edinin',
                'ar' => 'تعرف على حلول نظام إدارة المحتوى المؤسسي لدينا'
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $about->seoSetting()->create([
            'titles' => [
                'tr' => 'Hakkımızda - Modern CMS',
                'ar' => 'معلومات عنا - نظام إدارة المحتوى الحديث'
            ],
            'descriptions' => [
                'tr' => 'Kurumsal web çözümlerimiz ve hizmetlerimiz hakkında detaylı bilgi',
                'ar' => 'معلومات مفصلة عن حلولنا وخدماتنا على الويب المؤسسية'
            ],
        ]);

        $this->command->info('  ✓ About page created');

        // 3. Contact Page
        $this->command->info('  → Creating Contact page');

        $contact = Page::create([
            'title' => [
                'tr' => 'İletişim',
                'ar' => 'اتصل بنا'
            ],
            'slug' => [
                'tr' => 'iletisim',
                'ar' => 'اتصل-بنا'
            ],
            'body' => [
                'tr' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-4xl font-bold mb-8">İletişim</h1>
                    <p class="text-lg mb-6">Bizimle iletişime geçin</p>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-4xl font-bold mb-8">اتصل بنا</h1>
                    <p class="text-lg mb-6">تواصل معنا</p>
                </div>'
            ],
            'excerpt' => [
                'tr' => 'Bizimle iletişime geçin',
                'ar' => 'تواصل معنا'
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $contact->seoSetting()->create([
            'titles' => [
                'tr' => 'İletişim - Modern CMS',
                'ar' => 'اتصل بنا - نظام إدارة المحتوى الحديث'
            ],
            'descriptions' => [
                'tr' => 'Sorularınız için bizimle iletişime geçin',
                'ar' => 'اتصل بنا لأسئلتك'
            ],
        ]);

        $this->command->info('  ✓ Contact page created');

        $this->command->info('✅ Tenant 1 pages seeded successfully');
        $this->command->info('📊 Total pages created: 3');
    }
}
