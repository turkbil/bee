<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_18EA_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1;    // İXTİF
        $sku = 'EPT20-18EA';
        $titleTr = 'İXTİF EPT20-18EA - 1.8 Ton Elektrikli Transpalet';
        $shortTr = '1.800 kg kapasiteli, kompakt gövdeye sahip, 24V (2x12V) 85Ah aküyle çalışan yaya kumandalı transpalet. 4.5/5 km/s seyir hızı, 105 mm kaldırma, 85 mm düşük çatal yüksekliği ve 1457 mm dönüş yarıçapı ile dar koridorlarda verimli.';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortTr], JSON_UNESCAPED_UNICODE),
            'base_price' => 0.00,
            'price_on_request' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'is_master_product' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),

            // Teknik Özellikler (PDF'den)
            'technical_specs' => json_encode([
                'Üretici' => 'İXTİF',
                'Model' => 'EPT20-18EA',
                'Sürüş' => 'Elektrikli (Yaya Kumandalı)',
                'Kapasite' => '1800 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik Aksına Merkez Yük Mesafesi (x)' => '945 mm',
                'Dingil Mesafesi (y)' => '1270 mm',
                'Servis Ağırlığı' => '285 kg',
                'Dingil Yükü, Yüklü (ön/arka)' => '770 / 1315 kg',
                'Dingil Yükü, Yüksüz (ön/arka)' => '230 / 55 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Yük Teker(ön) Boyutu' => 'Ø80×60 mm',
                'Tahrik Teker(arka) Boyutu' => 'Ø230×75 mm',
                'Denge Teker(ek teker) Boyutu' => 'Ø85×48 mm',
                'Teker Sayısı (ön/arka)' => '1x+2 / 4',
                'İz Genişliği Ön (b10)' => '435 mm',
                'İz Genişliği Arka (b11)' => '390 / 495 / 535 mm',
                'Kaldırma Yüksekliği (h3)' => '105 mm',
                'Sürüş Kolu Yüksekliği (min/max) (h14)' => '715 / 1200 mm',
                'Çatal Altı Yükseklik (h13)' => '85 mm',
                'Toplam Uzunluk (l1)' => '1625 mm',
                'Yüze Kadar Uzunluk (l2)' => '491 mm',
                'Toplam Genişlik (b1/b2)' => '645 mm',
                'Çatal Ölçüleri (s/e/l)' => '50 × 150 × 1150 mm',
                'Forklar Arası Mesafe (b5)' => '540 / 685 mm',
                'Zemin Boşluğu (m2)' => '35 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2258 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2120 mm',
                'Dönüş Yarıçapı (Wa)' => '1457 mm',
                'Seyir Hızı (yüklü/yüksüz)' => '4.5 / 5 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.051 / 0.060 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.032 / 0.039 m/s',
                'Maks. Eğim Kabiliyeti (yüklü/yüksüz)' => '6% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '1.1 kW',
                'Kaldırma Motoru (S3 15%)' => '0.84 kW',
                'Akü Gerilim/Kapasite' => '24V (2x12V) / 85Ah',
                'Akü Ağırlığı' => '25 kg x 2',
                'Sürüş Kontrol Tipi' => 'AC',
                'Direksiyon Tasarımı' => 'Mekanik',
                'Ses Basınç Seviyesi (kulak)' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            // PDF temelli öne çıkan özellikler (8 madde)
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V 85Ah aküyle verimli enerji ve vardiya içi kullanım'],
                ['icon' => 'bolt', 'text' => 'AC sürüş kontrolü ile akıcı kalkış ve hassas hız yönetimi'],
                ['icon' => 'arrows-alt', 'text' => '1457 mm dönüş yarıçapıyla dar koridorlarda çeviklik'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ile güvenli duruş'],
                ['icon' => 'star', 'text' => 'Polyurethane tekerlekler ile düşük titreşim ve sessiz çalışma'],
                ['icon' => 'cog', 'text' => 'Basit mekanik direksiyon ve düşük bakım maliyeti'],
                ['icon' => 'battery-full', 'text' => 'Harici şarj seçenekleri ve batarya göstergesi (süreli)'],
                ['icon' => 'industry', 'text' => '285 kg hafif şasi ile kolay sevk ve operasyon']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master: ' . $sku);
    }
}
