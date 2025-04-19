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

        $pages = [
            [
                'title' => 'Anasayfa',
                'body' => '<h1>Anasayfa</h1><p>Hoşgeldiniz! Burası anasayfa.</p>',
                'is_homepage' => true,
            ],
            [
                'title' => 'Çerez Politikası',
                'body' => '<h1>Çerez Politikası</h1><p>Çerez politikamız hakkında bilgiler.</p>',
                'is_homepage' => false,
            ],
            [
                'title' => 'Kişisel Verilerin İşlenmesi Politikası',
                'body' => '<h1>Kişisel Verilerin İşlenmesi Politikası</h1><p>Kişisel verilerinizin işlenmesi ile ilgili bilgiler.</p>',
                'is_homepage' => false,
            ],
            [
                'title' => 'Hakkımızda',
                'body' => '<h1>Hakkımızda</h1><p>Biz kimiz? Hakkımızda detaylar.</p>',
                'is_homepage' => false,
            ],
            [
                'title' => 'İletişim',
                'body' => '<h1>İletişim</h1><p>Bize ulaşmak için iletişim bilgilerimiz.</p>',
                'is_homepage' => false,
            ],
        ];

        foreach ($pages as $page) {
            Page::create([
                'title' => $page['title'],
                'body' => $page['body'],
                'css' => null,
                'js' => null,
                'metakey' => null,
                'metadesc' => null,
                'is_active' => true,
                'is_homepage' => $page['is_homepage'],
            ]);
        }
    }
}