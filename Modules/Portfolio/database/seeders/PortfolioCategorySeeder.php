<?php
namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Faker\Factory as Faker;

class PortfolioCategorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('tr_TR');

        $categories = [
            'Web Tasarım',
            'Mobil Uygulama',
            'Grafik Tasarım',
            'UI/UX Tasarım',
            'E-Ticaret',
            'Kurumsal Kimlik',
            'SEO ve SEM',
            '3D Animasyon',
            'Logo Tasarım',
            'Sosyal Medya'
        ];

        foreach ($categories as $index => $category) {
            $icerik = '<h2>' . $category . ' Projelerimiz</h2>';
            $icerik .= '<p>' . $faker->text(300) . '</p>';
            $icerik .= '<ul>';
            
            for ($j = 0; $j < 3; $j++) {
                $icerik .= '<li>' . $faker->text(40) . '</li>';
            }
            
            $icerik .= '</ul>';
            $icerik .= '<p>' . $faker->text(150) . '</p>';

            PortfolioCategory::create([
                'title' => $category,
                'body' => $icerik,
                'order' => $index,
                'metakey' => implode(', ', $faker->words(5)),
                'metadesc' => $faker->text(100),
                'is_active' => $faker->boolean(90),
            ]);
        }
    }
}