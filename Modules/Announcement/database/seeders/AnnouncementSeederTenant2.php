<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use App\Models\SeoSetting;

/**
 * Announcement Seeder for Tenant2 Database
 * Languages: tr, en
 */
class AnnouncementSeederTenant2 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT2 announcements (tr, en)...');
        
        // Duplicate kontrolü
        $existingCount = Announcement::count();
        if ($existingCount > 0) {
            $this->command->info("Announcements already exist in TENANT2 database ({$existingCount} announcements), skipping seeder...");
            return;
        }
        
        // Mevcut duyuruları sil (sadece boşsa)
        Announcement::truncate();
        SeoSetting::where('seoable_type', 'like', '%Announcement%')->delete();
        
        $this->createWelcomeAnnouncement();
        $this->createUpdateAnnouncement();
        $this->createServiceAnnouncement();
    }
    
    private function createWelcomeAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Hoş Geldiniz - Tenant2 Platformu',
                'en' => 'Welcome - Tenant2 Platform'
            ],
            'slug' => [
                'tr' => 'hos-geldiniz-tenant2',
                'en' => 'welcome-tenant2'
            ],
            'body' => [
                'tr' => '<div class="bg-gradient-to-br from-blue-50 to-green-50 p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">🎉 Tenant2 Platformuna Hoş Geldiniz!</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        İş süreçlerinizi dijitalleştirmek için modern çözümler sunuyoruz.
                    </p>
                    <div class="grid md:grid-cols-2 gap-6 my-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-blue-600 mb-3">💻 Web Geliştirme</h3>
                            <p class="text-gray-600">Modern ve responsive web siteleri</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-green-600 mb-3">📱 Mobil Uygulamalar</h3>
                            <p class="text-gray-600">iOS ve Android uygulamaları</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="bg-gradient-to-br from-blue-50 to-green-50 p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">🎉 Welcome to Tenant2 Platform!</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        We offer modern solutions to digitize your business processes.
                    </p>
                    <div class="grid md:grid-cols-2 gap-6 my-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-blue-600 mb-3">💻 Web Development</h3>
                            <p class="text-gray-600">Modern and responsive websites</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-green-600 mb-3">📱 Mobile Applications</h3>
                            <p class="text-gray-600">iOS and Android applications</p>
                        </div>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'tr' => 'İş süreçlerinizi dijitalleştirmek için modern çözümler sunuyoruz.',
                'en' => 'We offer modern solutions to digitize your business processes.'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Hoş Geldiniz - Tenant2 Platformu',
            'Welcome - Tenant2 Platform',
            'İş süreçlerinizi dijitalleştirmek için modern çözümler sunuyoruz.',
            'We offer modern solutions to digitize your business processes.'
        );
    }
    
    private function createUpdateAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Platform Güncellemesi v1.2.0',
                'en' => 'Platform Update v1.2.0'
            ],
            'slug' => [
                'tr' => 'platform-guncellemesi-v1-2-0',
                'en' => 'platform-update-v1-2-0'
            ],
            'body' => [
                'tr' => '<div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">🔄 Platform Güncellemesi v1.2.0</h2>
                    <h3 class="text-xl font-semibold mb-4">✨ Yeni Özellikler:</h3>
                    <ul class="space-y-2 mb-6">
                        <li>• Gelişmiş kullanıcı arayüzü</li>
                        <li>• Yeni dashboard tasarımı</li>
                        <li>• Performans iyileştirmeleri</li>
                        <li>• Mobil uyumluluk artırıldı</li>
                    </ul>
                    <h3 class="text-xl font-semibold mb-4">🐛 Düzeltilen Hatalar:</h3>
                    <ul class="space-y-2">
                        <li>• Form gönderimi sorunu çözüldü</li>
                        <li>• Sayfa yükleme hızı artırıldı</li>
                        <li>• Güvenlik güncellemeleri yapıldı</li>
                    </ul>
                </div>',
                'en' => '<div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">🔄 Platform Update v1.2.0</h2>
                    <h3 class="text-xl font-semibold mb-4">✨ New Features:</h3>
                    <ul class="space-y-2 mb-6">
                        <li>• Enhanced user interface</li>
                        <li>• New dashboard design</li>
                        <li>• Performance improvements</li>
                        <li>• Increased mobile compatibility</li>
                    </ul>
                    <h3 class="text-xl font-semibold mb-4">🐛 Bug Fixes:</h3>
                    <ul class="space-y-2">
                        <li>• Form submission issue resolved</li>
                        <li>• Page loading speed increased</li>
                        <li>• Security updates made</li>
                    </ul>
                </div>'
            ],
            'excerpt' => [
                'tr' => 'Platform güncellemesi ile yeni özellikler ve iyileştirmeler eklendi.',
                'en' => 'New features and improvements have been added with the platform update.'
            ],
            'is_active' => true,
            'published_at' => now()->subDays(3),
        ]);

        $this->createSeoSetting(
            $announcement,
            'Platform Güncellemesi v1.2.0 - Tenant2',
            'Platform Update v1.2.0 - Tenant2',
            'Platform güncellemesi ile yeni özellikler ve iyileştirmeler eklendi.',
            'New features and improvements have been added with the platform update.'
        );
    }
    
    private function createServiceAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'tr' => 'Yeni Hizmetlerimiz Hakkında',
                'en' => 'About Our New Services'
            ],
            'slug' => [
                'tr' => 'yeni-hizmetlerimiz',
                'en' => 'our-new-services'
            ],
            'body' => [
                'tr' => '<div class="bg-gradient-to-r from-purple-50 to-pink-50 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-purple-900 mb-6">🚀 Yeni Hizmetlerimizi Keşfedin!</h2>
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">📊 İş Analitiği</h3>
                            <p class="text-gray-700">Verilerinizi analiz ederek işinizi büyütün</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">🔒 Güvenlik Çözümleri</h3>
                            <p class="text-gray-700">İş verilerinizi güvende tutun</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">☁️ Bulut Hizmetleri</h3>
                            <p class="text-gray-700">Verilerinize her yerden erişin</p>
                        </div>
                    </div>
                </div>',
                'en' => '<div class="bg-gradient-to-r from-purple-50 to-pink-50 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-purple-900 mb-6">🚀 Discover Our New Services!</h2>
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">📊 Business Analytics</h3>
                            <p class="text-gray-700">Analyze your data to grow your business</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">🔒 Security Solutions</h3>
                            <p class="text-gray-700">Keep your business data safe</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">☁️ Cloud Services</h3>
                            <p class="text-gray-700">Access your data from anywhere</p>
                        </div>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'tr' => 'İş analitiği, güvenlik çözümleri ve bulut hizmetleri ile işinizi geliştirin.',
                'en' => 'Improve your business with business analytics, security solutions and cloud services.'
            ],
            'is_active' => true,
            'published_at' => now()->subDays(7),
        ]);

        $this->createSeoSetting(
            $announcement,
            'Yeni Hizmetlerimiz Hakkında - Tenant2',
            'About Our New Services - Tenant2',
            'İş analitiği, güvenlik çözümleri ve bulut hizmetleri ile işinizi geliştirin.',
            'Improve your business with business analytics, security solutions and cloud services.'
        );
    }

    private function createSeoSetting($announcement, $titleTr, $titleEn, $descriptionTr, $descriptionEn): void
    {
        // DEBUG: Parametreleri kontrol et
        $this->command->info("DEBUG - Creating SEO for announcement {$announcement->announcement_id}");
        $this->command->info("Title TR: {$titleTr}");
        $this->command->info("Title EN: {$titleEn}");
        
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($announcement->seoSetting()->exists()) {
            $this->command->info("DEBUG - Deleting existing SEO setting");
            $announcement->seoSetting()->delete();
        }
        
        $announcement->seoSetting()->create([
            'titles' => [
                'tr' => $titleTr,
                'en' => $titleEn
            ],
            'descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn
            ],
            'keywords' => [
                'tr' => ['duyuru', 'haber', 'güncelleme', 'hizmet'],
                'en' => ['announcement', 'news', 'update', 'service']
            ],
            'og_titles' => [
                'tr' => $titleTr,
                'en' => $titleEn
            ],
            'og_descriptions' => [
                'tr' => $descriptionTr,
                'en' => $descriptionEn
            ],
            'available_languages' => ['tr', 'en'],
            'default_language' => 'tr',
            'seo_score' => rand(80, 95),
        ]);
    }
}