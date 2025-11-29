<?php

namespace Modules\Muzibu\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Muzibu\App\Models\Sector;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            [
                'title' => ['tr' => 'Günün Enerjisi', 'en' => 'Daily Energy'],
                'slug' => ['tr' => 'gunun-enerjisi', 'en' => 'daily-energy'],
                'description' => ['tr' => 'Güne enerji dolu başlamak için', 'en' => 'Start your day with energy'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Çalışma & Odaklanma', 'en' => 'Work & Focus'],
                'slug' => ['tr' => 'calisma-odaklanma', 'en' => 'work-focus'],
                'description' => ['tr' => 'Konsantre olmanıza yardımcı müzikler', 'en' => 'Music to help you concentrate'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Spor & Antrenman', 'en' => 'Sports & Workout'],
                'slug' => ['tr' => 'spor-antrenman', 'en' => 'sports-workout'],
                'description' => ['tr' => 'Egzersiz yaparken dinlenecek müzikler', 'en' => 'Music for your workout'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Rahatlatıcı & Meditasyon', 'en' => 'Relaxation & Meditation'],
                'slug' => ['tr' => 'rahatlatici-meditasyon', 'en' => 'relaxation-meditation'],
                'description' => ['tr' => 'Dinlenmek ve huzur bulmak için', 'en' => 'For relaxation and peace'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Parti & Eğlence', 'en' => 'Party & Fun'],
                'slug' => ['tr' => 'parti-eglence', 'en' => 'party-fun'],
                'description' => ['tr' => 'Parti ve eğlence için enerjik müzikler', 'en' => 'Energetic music for parties'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Romantik Anlar', 'en' => 'Romantic Moments'],
                'slug' => ['tr' => 'romantik-anlar', 'en' => 'romantic-moments'],
                'description' => ['tr' => 'Romantik anlar için müzikler', 'en' => 'Music for romantic moments'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Yolculuk & Seyahat', 'en' => 'Travel & Journey'],
                'slug' => ['tr' => 'yolculuk-seyahat', 'en' => 'travel-journey'],
                'description' => ['tr' => 'Yolculuklarınızda dinlenecek müzikler', 'en' => 'Music for your journeys'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Nostalji', 'en' => 'Nostalgia'],
                'slug' => ['tr' => 'nostalji', 'en' => 'nostalgia'],
                'description' => ['tr' => 'Eski günleri hatırlatan müzikler', 'en' => 'Music that reminds old days'],
                'is_active' => true,
            ],
        ];

        foreach ($sectors as $sector) {
            Sector::create($sector);
        }

        $this->command->info('✅ Sectors (Sektörler) oluşturuldu: ' . count($sectors));
    }
}
