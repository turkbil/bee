<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Announcement Seeder for Tenant4 Database
 * Languages: en
 */
class AnnouncementSeederTenant4 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT4 announcements (en)...');
        
        // Duplicate kontrolü
        $existingCount = Announcement::count();
        if ($existingCount > 0) {
            $this->command->info("Announcements already exist in TENANT4 database ({$existingCount} announcements), skipping seeder...");
            return;
        }
        
        // Mevcut duyuruları sil (sadece boşsa)
        Announcement::truncate();
        SeoSetting::where('seoable_type', 'like', '%Announcement%')->delete();
        
        $this->createWelcomeAnnouncement();
        $this->createProductLaunchAnnouncement();
    }
    
    private function createWelcomeAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Welcome to Tenant4 Platform'
            ],
            'slug' => [
                'en' => 'welcome-tenant4'
            ],
            'body' => [
                'en' => '<div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-8 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">🎉 Welcome to Tenant4 Platform!</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        Your trusted partner for innovative business solutions and cutting-edge technology.
                    </p>
                    <div class="grid md:grid-cols-3 gap-6 my-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-emerald-600 mb-3">🚀 Innovation</h3>
                            <p class="text-gray-600">Leading-edge technology solutions</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-teal-600 mb-3">🎯 Reliability</h3>
                            <p class="text-gray-600">Dependable business partnerships</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-blue-600 mb-3">⚡ Performance</h3>
                            <p class="text-gray-600">High-speed, efficient solutions</p>
                        </div>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg mt-6">
                        <p class="text-emerald-800 text-center font-medium">
                            Ready to transform your business? Let\'s get started today!
                        </p>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'en' => 'Your trusted partner for innovative business solutions and cutting-edge technology.'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Welcome to Tenant4 Platform',
            'Your trusted partner for innovative business solutions and cutting-edge technology.'
        );
    }
    
    private function createProductLaunchAnnouncement(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'New Product Suite Launch'
            ],
            'slug' => [
                'en' => 'new-product-suite-launch'
            ],
            'body' => [
                'en' => '<div class="bg-gradient-to-r from-violet-50 to-purple-50 p-8 rounded-lg">
                    <h2 class="text-2xl font-bold text-violet-900 mb-6">🚀 Introducing Our New Product Suite!</h2>
                    <div class="space-y-6">
                        <p class="text-lg text-violet-800">
                            We are excited to announce the launch of our comprehensive business automation suite.
                        </p>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">🔧 Automation Tools</h3>
                                <ul class="space-y-2 text-gray-700 text-sm">
                                    <li>• Workflow automation</li>
                                    <li>• Task scheduling</li>
                                    <li>• Process optimization</li>
                                    <li>• Performance monitoring</li>
                                </ul>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">📊 Analytics Dashboard</h3>
                                <ul class="space-y-2 text-gray-700 text-sm">
                                    <li>• Real-time insights</li>
                                    <li>• Custom reporting</li>
                                    <li>• Data visualization</li>
                                    <li>• Predictive analytics</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-green-100 to-emerald-100 p-4 rounded-lg">
                            <p class="text-green-800 text-center font-semibold">
                                🎁 Early adopters receive 3 months free premium support!
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600 text-sm">
                                Contact our sales team to schedule a personalized demo and learn how our solutions can benefit your business.
                            </p>
                        </div>
                    </div>
                </div>'
            ],
            'excerpt' => [
                'en' => 'We are excited to announce the launch of our comprehensive business automation suite.'
            ],
            'is_active' => true,
            'published_at' => now()->subDays(1),
        ]);

        $this->createSeoSetting(
            $announcement,
            'New Product Suite Launch - Tenant4',
            'We are excited to announce the launch of our comprehensive business automation suite.'
        );
    }

    private function createSeoSetting($announcement, $titleEn, $descriptionEn): void
    {
        // DEBUG: Parametreleri kontrol et
        $this->command->info("DEBUG - Creating SEO for announcement {$announcement->announcement_id}");
        $this->command->info("Title EN: {$titleEn}");
        
        // SEO ayarı varsa sil ve yeniden oluştur (seeder için)
        if ($announcement->seoSetting()->exists()) {
            $this->command->info("DEBUG - Deleting existing SEO setting");
            $announcement->seoSetting()->delete();
        }
        
        $announcement->seoSetting()->create([
            'titles' => [
                'en' => $titleEn
            ],
            'descriptions' => [
                'en' => $descriptionEn
            ],
            'keywords' => [
                'en' => ['announcement', 'news', 'product', 'business']
            ],
            'og_titles' => [
                'en' => $titleEn
            ],
            'og_descriptions' => [
                'en' => $descriptionEn
            ],
            'seo_score' => rand(80, 95),
        ]);
    }
}