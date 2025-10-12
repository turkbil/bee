<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL352_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFL352';
        $titleTr = 'İXTİF EFL352 - 3.5 Ton Li-Ion Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '3.5.0 ton kapasiteli, 500 mm yük merkezli ve 1810 mm dingil mesafeli İXTİF EFL352; IPX4 su koruması, büyük lastikler ve yüksek yerden yükseklikle iç/dış saha uyumu sunar. 80V 280Ah Li-Ion (LFP) ile fırsat şarjını destekler, 2645 mm dönüş yarıçapı ve 11/12 km/sa hız değerleriyle verimli ve esnek çalışır.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 1,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode([
                'Kapasite' => '3500 kg (3.5.0 T)',
                'Yük Merkez Mesafesi' => '500 mm',
                'Servis Ağırlığı' => '4560 kg',
                'Sürüş Tipi' => 'Elektrikli, Oturmalı Operatör',
                'Tekerlek Tipi' => 'Katı lastik, büyük ebat',
                'Tekerlek İzi (Ön/Arka)' => '1010 mm / 980 mm',
                'Dingil Mesafesi' => '1810 mm',
                'Toplam Uzunluk' => '3940 mm',
                'Toplam Genişlik' => '1230 mm',
                'Direk Kapalı Yüksekliği' => '2190 mm',
                'Kaldırma Yüksekliği' => '3000 mm',
                'Dönüş Yarıçapı' => '2645 mm',
                'Seyir Hızı (yük/yüksüz)' => '11/12 km/sa',
                'Kaldırma Hızı (yük/yüksüz)' => '0.26/0.34 m/s',
                'Fren Sistemi (servis/park)' => 'Hidrolik / Mekanik',
                'Sürüş Kontrolü' => 'AC',
                'Sürüş Motoru' => '10 kW (S2 60dk)',
                'Kaldırma Motoru' => '16 kW (S3 15%)',
                'Akü' => '80V 280Ah Li-Ion (LFP)',
                'Su Koruma' => 'IPX4',
                'Telematik' => 'Konum, kullanım raporları, akü analitiği, kart erişim güncellemeleri'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => 'LFP Li-Ion akü teknolojisiyle güvenli ve hızlı fırsat şarjı'],
                ['icon' => 'bolt', 'text' => 'Düşük TCO: yağ/filtre gibi motor parçaları bulunmadığından bakım masrafları minimumdur'],
                ['icon' => 'arrows-alt', 'text' => 'Büyük lastikler ve yüksek yerden yükseklik ile açık alanda konforlu sürüş'],
                ['icon' => 'shield-alt', 'text' => 'IPX4 su sıçramalarına karşı korumalı gövde tasarımı'],
                ['icon' => 'star', 'text' => 'IC forklift tasarımının sağladığı sağlam şasi ve dengeli sürüş'],
                ['icon' => 'briefcase', 'text' => 'Telematik ile filo raporları ve uzaktan tanılama'],
                ['icon' => 'cog', 'text' => 'Basit komponent mimarisi ile kolay servis ve bakım'],
                ['icon' => 'battery-full', 'text' => 'Gün boyu esneklik: vardiya aralarında hızlı şarj imkanı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku }");
    }
}
