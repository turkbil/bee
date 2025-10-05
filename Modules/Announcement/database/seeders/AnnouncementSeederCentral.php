<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use Modules\SeoManagement\App\Models\SeoSetting;
use Faker\Factory as Faker;

class AnnouncementSeederCentral extends Seeder
{
    private $fakers = [];
    private $languages = [];

    public function run(): void
    {
        // Bu seeder sadece central context'te çalışmalı
        if (tenancy()->initialized) {
            $this->command->warn("⚠️  AnnouncementSeederCentral sadece central database'de çalışır. Atlanıyor...");
            return;
        }

        if (Announcement::count() > 0) {
            $this->command->warn("⚠️  Announcements exist. Skipping...");
            return;
        }

        // Admin dillerini al (Central için)
        $this->languages = \DB::table('admin_languages')
            ->where('is_active', 1)
            ->pluck('code')
            ->toArray();

        if (empty($this->languages)) {
            $this->languages = ['tr', 'en']; // Fallback
        }

        // Her dil için Faker instance oluştur
        $localeMap = ['tr' => 'tr_TR', 'en' => 'en_US', 'ar' => 'ar_SA'];
        foreach ($this->languages as $lang) {
            $locale = $localeMap[$lang] ?? 'en_US';
            $this->fakers[$lang] = Faker::create($locale);
        }

        $this->command->info("📝 Creating announcements for languages: " . implode(', ', $this->languages));

        for ($i = 1; $i <= 12; $i++) {
            $data = $this->generateAnnouncement($i);
            $seoMeta = $data['seo_meta'];
            unset($data['seo_meta']);

            $announcement = Announcement::create($data);
            $this->createSeoSettings($announcement, $seoMeta);

            $this->command->info("  ✅ {$data['title'][$this->languages[0]]}");
        }

        $this->command->info("🎉 Created 12 announcements!");
    }

    private function generateAnnouncement(int $index): array
    {
        $titles = [];
        $slugs = [];
        $bodies = [];
        $seoMeta = [];

        foreach ($this->languages as $lang) {
            $faker = $this->fakers[$lang];
            $title = $faker->sentence(rand(4, 7));

            $titles[$lang] = $title;
            $slugs[$lang] = \Str::slug($title);

            // Body oluştur
            $body = '<div class="prose dark:prose-invert max-w-none">';
            $body .= '<h2>' . $title . '</h2>';
            for ($i = 0; $i < rand(5, 8); $i++) {
                $body .= '<p>' . $faker->paragraph(rand(10, 20)) . '</p>';
            }
            $body .= '</div>';

            $bodies[$lang] = $body;
            $seoMeta[$lang] = [
                'title' => $title,
                'description' => $faker->sentence(15),
            ];
        }

        return [
            'title' => $titles,
            'slug' => $slugs,
            'body' => $bodies,
            'is_active' => rand(0, 10) > 2,
            'seo_meta' => $seoMeta
        ];
    }

    private function createSeoSettings($announcement, $seoMeta): void
    {
        foreach ($seoMeta as $lang => $data) {
            $announcement->seoSetting()->updateOrCreate(
                ['seoable_id' => $announcement->announcement_id, 'seoable_type' => Announcement::class],
                [
                    'titles' => [$lang => $data['title']],
                    'descriptions' => [$lang => $data['description']],
                    'status' => 'active',
                ]
            );
        }
    }
}
