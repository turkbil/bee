<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use Faker\Factory as Faker;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('tr_TR');

        for ($i = 0; $i < 5; $i++) {
            $baslik = $faker->text(50);
            $icerik = '<h1>' . $baslik . '</h1>';
            $icerik .= '<p>' . $faker->text(200) . '</p>';
            $icerik .= '<h2>' . $faker->text(30) . '</h2>';
            $icerik .= '<p>' . $faker->text(300) . '</p>';
            $icerik .= '<ul>';
            
            for ($j = 0; $j < 4; $j++) {
                $icerik .= '<li>' . $faker->text(40) . '</li>';
            }
            
            $icerik .= '</ul>';
            $icerik .= '<p>' . $faker->text(150) . '</p>';

            $css = $faker->boolean(30) ? '.ozel-' . $faker->word . ' { color: ' . $faker->hexColor . '; }' : null;
            $js = $faker->boolean(20) ? 'console.log("' . $faker->word . '");' : null;

            Page::create([
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