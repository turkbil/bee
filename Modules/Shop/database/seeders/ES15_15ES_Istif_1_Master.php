<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES15_15ES_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES15-15ES';
        $titleTr = 'İXTİF ES15-15ES - 1.5 Ton Yaya Tipi Elektrikli İstif Makinesi';
        $shortTr = 'İXTİF ES15-15ES; 1500 kg kapasite, 600 mm yük merkezi, 24V 125Ah akü, 5 km/s seyir, 0.13/0.20 m/s kaldırma hızları ve 1500 mm dönüş yarıçapı ile dar koridorlarda çevik ve verimli istifleme sunar.';

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
                'Üretici' => 'İXTİF',
                'Model' => 'ES15-15ES',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Tahrik Aksına Kadar Yük Mesafesi (x)' => '805 mm',
                'Dingil Mesafesi (y)' => '1240 mm',
                'Servis Ağırlığı (batarya dahil)' => '755 kg',
                'Dolu/Boş Aks Yükü (ön/arka)' => '805/1450 kg (dolu), 545/210 kg (boş)',
                'Teker Tipi (Sürüş/Yük)' => 'PU/PU',
                'Sürüş Teker Ölçüsü (ØxW)' => 'Ø230×75 mm',
                'Yük Teker Ölçüsü (ØxW)' => 'Ø80×60 mm',
                'Destek Teker (ØxW)' => 'Ø130×55 mm',
                'Teker Konfigürasyonu (x=tahrik)' => '1x +2/4',
                'Ön İz Genişliği' => '538 mm',
                'Arka İz Genişliği' => '400 mm',
                'Direk Kapalı Yükseklik (h1)' => '2128 mm',
                'Serbest Kaldırma (h2)' => '-',
                'Kaldırma Yüksekliği (h3)' => '3227 mm',
                'Direk Açık Yükseklik (h4)' => '3743 mm',
                'Sürüş Kolu Yüksekliği (min/max) (h14)' => '1150/1480 mm',
                'Alt Yükseklik (h13)' => '88 mm',
                'Toplam Uzunluk (l1)' => '1740 mm',
                'Çatala Kadar Uzunluk (l2)' => '575 mm',
                'Toplam Genişlik (b1/b2)' => '800 mm',
                'Çatal Ölçüsü (s/e/l)' => '60×170×1150 mm',
                'Taşıyıcı Genişliği (b3)' => '680 mm',
                'Dıştan Çatal Genişliği (b5)' => '570 mm',
                'Şase Orta Noktası Yerden Yükseklik (m2)' => '28 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2340 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2260 mm',
                'Dönüş Yarıçapı (Wa)' => '1500 mm',
                'Seyir Hızı (dolu/boş)' => '5/5 km/s',
                'Kaldırma Hızı (dolu/boş)' => '0.13/0.20 m/s',
                'İndirme Hızı (dolu/boş)' => '0.13/0.13 m/s',
                'Tırmanma Kabiliyeti (dolu/boş)' => '8/16 %',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.27 kW',
                'Kaldırma Motoru (S3 15%)' => '3 kW',
                'Maks. Batarya Boyutu' => '330×190×240 mm',
                'Batarya Gerilimi/Kapasite (K20)' => '2x12V / 125Ah (24V 125Ah)',
                'Batarya Ağırlığı' => '60 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Mekanik',
                'Operatör Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V 125Ah enerji paketi ile vardiya boyu kesintisiz istifleme'],
                ['icon' => 'bolt', 'text' => '1.27 kW AC sürüş + 3 kW kaldırma motoru ile güçlü performans'],
                ['icon' => 'arrows-alt', 'text' => '1500 mm dönüş yarıçapı ve 800 mm şasi ile dar alan çevikliği'],
                ['icon' => 'weight-hanging', 'text' => '1500 kg kapasite ve 600 mm yük merkezi ile güvenli taşıma'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve sağlam şasi ile güvenlik'],
                ['icon' => 'cart-shopping', 'text' => '5 km/s sabit hızda dengeli sevkiyat akışı'],
                ['icon' => 'cog', 'text' => 'PU tekerler ve basit mekanik direksiyonla düşük bakım'],
                ['icon' => 'certificate', 'text' => 'Avrupa standartlarına uygun CE belgesi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
