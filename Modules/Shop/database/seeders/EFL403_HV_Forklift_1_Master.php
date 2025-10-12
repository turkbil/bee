<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL403_HV_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFL403-HV';
        $titleTr = 'İXTİF EFL403 HV - 4.0 Ton Yüksek Voltaj Elektrikli Forklift';
        $shortTr = '4.0 ton kapasiteli İXTİF EFL403 HV; 309V/173Ah LFP akü, PMSM tahrik ve çift su soğutma ile 24/25 km/s hız, %25/%30 (yüklü/boş) eğim ve kompakt şasi sunar. Tek ön teker tasarımıyla dar alanlarda çevik, 1C hızlı şarj ile çok vardiyaya uygundur.';

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
            'technical_specs' => json_encode([
                'Model' => 'EFL403-HV',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Oturmalı',
                'Kapasite (Q)' => '4000 kg',
                'Yük Merkezi (c)' => '500 mm',
                'Tahrik Aksına Mesafe (x)' => '545 mm',
                'Dingil Mesafesi (y)' => '2000 mm',
                'Servis Ağırlığı' => '6450 kg',
                'Lastik Tipi' => 'Pnömatik',
                'Ön Lastik' => '8.25-15-14PR',
                'Arka Lastik' => '7.00-12-12PR',
                'Ön/Arka Teker (x=tahrik)' => '2x/2',
                'Ön İz (b10)' => '1176 mm',
                'Arka İz (b11)' => '1190 mm',
                'Direk Eğimi (α/β)' => '6°/12°',
                'Direk Kapalı Yükseklik (h1)' => '2250 mm',
                'Serbest Kaldırma (h2)' => '150 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Açık Yükseklik (h4)' => '4177/3835 mm',
                'Kabin Yüksekliği (h6)' => '2400 mm',
                'Koltuk Yüksekliği (h7)' => '1290 mm',
                'Çeki Kancası Yüksekliği (h10)' => '640 mm',
                'Toplam Uzunluk (l1)' => '4125 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '3055 mm',
                'Toplam Genişlik (b1/b2)' => '1495 mm',
                'Çatal Boyutları (s/e/l)' => '50×150×1070 mm',
                'Fork Carriage Sınıfı' => '3A',
                'Fork Carriage Genişliği (b3)' => '1380 mm',
                'Yerden Yükseklik Mast Altı (m1)' => '150 mm',
                'Dingil Orta Noktası (m2)' => '180 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '4495 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '4495 mm',
                'Dönüş Yarıçapı (Wa)' => '2680 mm',
                'Sürüş Hızı Yüklü/Boş' => '24/25 km/s',
                'Kaldırma Hızı Yüklü/Boş' => '0.46/0.53 m/s',
                'İndirme Hızı Yüklü/Boş' => '0.41/0.42 m/s',
                'Azami Eğim Yüklü/Boş' => '%25/%30',
                'Servis Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60dk)' => '30 kW',
                'Kaldırma Motoru (S3 15%)' => '27.8 kW',
                'Batarya Gerilimi/Kapasitesi' => '309V/173Ah',
                'Batarya Ağırlığı' => '473 kg',
                'Sürüş Kontrolü' => 'PMSM'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '309V/173Ah LFP yüksek voltaj batarya ve BMS'],
                ['icon' => 'bolt', 'text' => 'PMSM tahrik ile yüksek verim ve düşük bakım'],
                ['icon' => 'gauge', 'text' => 'Yüklü/boş 24/25 km/s seyir hızı'],
                ['icon' => 'mountain', 'text' => 'Yüklü %20+ eğim tırmanma (modele göre)'],
                ['icon' => 'water', 'text' => 'Motor ve batarya için çift su soğutma'],
                ['icon' => 'oil-can', 'text' => 'Hidrolik sistem için yağ soğutma'],
                ['icon' => 'shield-alt', 'text' => 'VCU ile dönüş hızı kontrolü ve aşırı hız uyarısı'],
                ['icon' => 'certificate', 'text' => 'IPX4 kasa, IP67 HV bileşen koruması']
            ], JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
