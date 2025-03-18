<?php
namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Faker\Factory as Faker;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('tr_TR');
        $categories = PortfolioCategory::pluck('portfolio_category_id')->toArray();

        if (empty($categories)) {
            $this->command->error('Önce kategorileri oluşturmanız gerekiyor.');
            return;
        }

        foreach ($categories as $categoryId) {
            for ($i = 0; $i < 5; $i++) {
                $baslik = $faker->company . ' ' . $faker->word . ' Projesi';
                $icerik = '<h1>' . $baslik . '</h1>';
                $icerik .= '<p>' . $faker->text(200) . '</p>';
                $icerik .= '<h2>Proje Detayları</h2>';
                $icerik .= '<p>' . $faker->text(300) . '</p>';
                $icerik .= '<ul>';
                
                for ($j = 0; $j < 4; $j++) {
                    $icerik .= '<li>' . $faker->text(40) . '</li>';
                }
                
                $icerik .= '</ul>';
                $icerik .= '<p>' . $faker->text(150) . '</p>';

                $css = $faker->boolean(30) ? '.proje-' . $faker->word . ' { color: ' . $faker->hexColor . '; }' : null;
                $js = $faker->boolean(20) ? 'console.log("' . $faker->word . '");' : null;

                Portfolio::create([
                    'portfolio_category_id' => $categoryId,
                    'title' => $baslik,
                    'body' => $icerik,
                    'css' => $css,
                    'js' => $js,
                    'metakey' => implode(', ', $faker->words(5)),
                    'metadesc' => $faker->text(100),
                    'is_active' => $faker->boolean(90),
                ]);
            }
        }
    }
}