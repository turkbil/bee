<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\Portfolio;

class PortfolioSeeder extends Seeder
{
    public function run()
    {
        // `tenants` tablosundaki mevcut ID'leri al
        $tenantIds = \DB::table('tenants')->pluck('id')->toArray();

        // Eğer `tenants` tablosunda veri yoksa hata mesajı göster
        if (empty($tenantIds)) {
            $this->command->error('Tenants tablosunda hiçbir kayıt bulunamadı. Lütfen önce tenant verilerini oluşturun.');
            return;
        }

        // `portfolio_categories` tablosundaki mevcut ID'leri al
        $categoryIds = \DB::table('portfolio_categories')->pluck('portfolio_category_id')->toArray();

        // Eğer `portfolio_categories` tablosunda veri yoksa hata mesajı göster
        if (empty($categoryIds)) {
            $this->command->error('PortfolioCategories tablosunda hiçbir kayıt bulunamadı. Lütfen önce kategori verilerini oluşturun.');
            return;
        }

        // Başarı mesajını göster
        $this->command->info('Portfolio verileri oluşturuluyor...');

        // 500 portfolio oluştur
        foreach (range(1, 1500) as $index) {
            $title = fake()->sentence(3); // Rastgele 3 kelimelik başlık

            Portfolio::create([
                'tenant_id' => $tenantIds[array_rand($tenantIds)], // Rastgele bir tenant ID
                'portfolio_category_id' => $categoryIds[array_rand($categoryIds)], // Rastgele bir kategori ID
                'title' => $title,
                'slug' => Str::slug($title), // Slug oluştur
                'body' => fake()->paragraphs(3, true), // 3 paragraf body
                'image' => fake()->boolean(70) ? 'portfolio-' . fake()->word() . '.jpg' : null, // %70 ihtimalle resim
                'css' => fake()->boolean(30) ? 'custom-style-' . fake()->word() . '.css' : null, // %30 ihtimalle CSS dosyası
                'js' => fake()->boolean(30) ? 'custom-script-' . fake()->word() . '.js' : null, // %30 ihtimalle JS dosyası
                'metakey' => fake()->boolean(50) ? fake()->words(5, true) : null, // Rastgele meta anahtar kelimeler
                'metadesc' => fake()->boolean(50) ? fake()->sentence(10) : null, // Rastgele meta açıklama
                'is_active' => fake()->boolean(80), // %80 ihtimalle aktif
            ]);
        }

        // İşlem tamamlandı mesajını göster
        $this->command->info('500 portfolio başarıyla oluşturuldu.');
    }
}