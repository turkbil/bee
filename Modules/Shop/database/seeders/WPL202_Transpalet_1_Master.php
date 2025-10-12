<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WPL202_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'WPL202';
        $titleTr = 'İXTİF WPL202 - 2.0 Ton Li-Ion Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'WPL202; 470 mm kısa şasi, 1320 mm dönüş yarıçapı ve 320 kg servis ağırlığı ile dar alanlarda çevik çalışır. 24V/100Ah Li‑Ion batarya ve 24V/30A entegre şarj ile hızlı, bakımsız ve çok vardiyalı kullanıma uygundur.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 2,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'base_price' => 0.00,
            'price_on_request' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode([
                'Model' => 'WPL202',
                'Sürüş' => 'Elektrik (Yaya kumandalı)',
                'Kapasite' => '2000 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Servis Ağırlığı' => '320 kg',
                'Tahrik Aksına Uzaklık (x)' => '982 mm',
                'Dingil Mesafesi (y)' => '1300 mm',
                'Ön/Ara Dingil Yükü (Yüklü)' => '825 / 1495 kg',
                'Ön/Ara Dingil Yükü (Yüksüz)' => '255 / 65 kg',
                'Teker Tipi' => 'PU',
                'Tahrik Teker (ön)' => 'Ø230×75 mm',
                'Yük Teker (arka)' => 'Ø80×85 mm (çift)',
                'Ek (kastor) Teker' => 'Ø74×30 mm',
                'Teker Sayısı (ön/arka/kastor)' => '1 / 2 / 4',
                'Ön İz Genişliği (b10)' => '483 mm',
                'Arka İz Genişliği (b11)' => '370 mm',
                'Kaldırma Yüksekliği (h3)' => '125 mm',
                'Kumanda Kolu Yüksekliği (min/max) (h14)' => '900 / 1230 mm',
                'Asgari Çatal Yüksekliği (h13)' => '85 mm',
                'Toplam Uzunluk (l1)' => '1620 mm',
                'Yüke Kadar Uzunluk (l2)' => '470 mm',
                'Toplam Genişlik (b1/b2)' => '714 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 / 170 / 1150 mm',
                'Çatal Aralığı (b5)' => '540 / 685 mm',
                'Şasi Orta Noktası Yerden Yükseklik (m2)' => '27 mm',
                'Koridor Genişliği (1000×1200 yanlamasına) Ast' => '2153 mm',
                'Koridor Genişliği (800×1200 boylamasına) Ast' => '2080 mm',
                'Dönüş Yarıçapı (Wa)' => '1320 mm',
                'Sürüş Hızı (yüklü/yüksüz)' => '5.5 / 6 km/s',
                'Kaldırma Hızı (yüklü/yüksüz)' => '0.022 m/s',
                'İndirme Hızı (yüklü/yüksüz)' => '0.039 m/s',
                'Tırmanma Kabiliyeti (yüklü/yüksüz)' => '10% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.6 kW (AC)',
                'Kaldırma Motoru (S3 15%)' => '0.8 kW',
                'Akü (V/Ah)' => '24V / 100Ah (Li‑Ion)',
                'Akü Ağırlığı' => '44 kg',
                'Şarj Cihazı Çıkış Akımı' => '30 A (entegre)',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon Tasarımı' => 'Mekanik',
                'Ses Seviyesi (kulak)' => '74 dB(A)',
                'Soğuk Depo Seçeneği' => '0 ~ -20°C (ısıtmalı Li‑Ion, su geçirmez kumanda başı)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/100Ah Li‑Ion batarya, fırsat şarjı ve bakım gerektirmeyen yapı'],
                ['icon' => 'plug', 'text' => '24V/30A entegre şarj cihazı ile hızlı ve esnek şarj'],
                ['icon' => 'arrows-alt', 'text' => '470 mm kısa şasi ve 1320 mm dönüş yarıçapı ile dar alan çevikliği'],
                ['icon' => 'cog', 'text' => 'Dikey AC tahrik motoru; olgun, dayanıklı komponentlerle yüksek güvenilirlik'],
                ['icon' => 'shield-alt', 'text' => 'Suya dayanıklı kumanda başı ve elektronikler ile güvenli çalışma'],
                ['icon' => 'layer-group', 'text' => 'Senkron kastor teker sistemi ile maksimum stabilite ve çekiş'],
                ['icon' => 'snowflake', 'text' => 'Soğuk depo seçeneği: ısıtmalı batarya, kauçuk teker, düşük ısı yağ'],
                ['icon' => 'star', 'text' => 'Ergonomik kol ve kaplumbağa modu ile dar koridorda hassas sürüş']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
