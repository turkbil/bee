<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\PortfolioCategory;

class PortfolioCategorySeeder extends Seeder
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

        // Başarı mesajını göster
        $this->command->info(count($tenantIds) . ' tenant bulundu. Kategori verileri oluşturuluyor...');

        // 50 kategori oluştur
        foreach (range(1, 50) as $index) {
            $title = fake()->sentence(2); // Rastgele 2 kelimelik başlık

            PortfolioCategory::create([
                'tenant_id' => $tenantIds[array_rand($tenantIds)], // Rastgele bir tenant ID
                'title' => $title,
                'slug' => Str::slug($title), // Slug oluştur
                'order' => fake()->numberBetween(1, 100), // 1-100 arası sıralama
                'metakey' => fake()->boolean(50) ? fake()->words(5, true) : null, // Meta anahtar kelimeler
                'metadesc' => fake()->boolean(50) ? fake()->sentence(10) : null, // Meta açıklama
                'is_active' => fake()->boolean(80), // %80 ihtimalle aktif
            ]);
        }

        // İşlem tamamlandı mesajını göster
        $this->command->info('50 kategori başarıyla oluşturuldu.');
    }
}