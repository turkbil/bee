<?php
namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Page\App\Models\Page;

class PageSeeder extends Seeder
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
        $this->command->info(count($tenantIds) . ' tenant bulundu. Sayfa verileri oluşturuluyor...');

        // Faker ile rastgele sayfa verileri oluştur
        foreach (range(1, 550) as $index) {
            $title = fake()->sentence(3); // Rastgele 3 kelimelik başlık

            Page::create([
                'tenant_id' => $tenantIds[array_rand($tenantIds)], // Rastgele bir tenant ID
                'title' => $title,
                'slug'      => Str::slug($title),                                                // Slug oluştur
                'body'  => fake()->paragraphs(3, true),                                          // 3 paragraf body
                'is_active' => fake()->boolean(80),                                              // %80 ihtimalle aktif
                'css' => fake()->boolean(50) ? 'custom-style-' . fake()->word() . '.css' : null, // Bazen CSS dosyası ekle
                'js' => fake()->boolean(50) ? 'custom-script-' . fake()->word() . '.js' : null,  // Bazen JS dosyası ekle
                'metakey' => fake()->boolean(50) ? fake()->words(5, true) : null,                // Rastgele meta anahtar kelimeler
                'metadesc' => fake()->boolean(50) ? fake()->sentence(10) : null,                 // Rastgele meta açıklama
            ]);
        }

        // İşlem tamamlandı mesajını göster
        $this->command->info('Sayfa verileri başarıyla oluşturuldu.');
    }
}
