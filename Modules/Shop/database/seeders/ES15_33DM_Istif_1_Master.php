<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES15_33DM_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES15-33DM';
        $titleTr = 'İXTİF ES15-33DM - 1.5 Ton Geniş Şase Elektrikli İstif Makinesi';
        $shortTr = 'İXTİF ES15-33DM; 1500 kg kapasite, geniş teker kolları (1270–1470 mm), 24V 125Ah akü, 5 km/s hız ve 1400 mm dönüş yarıçapı ile farklı palet genişliklerine uyumlu çok yönlü bir yaya tipi istifleyicidir.';

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
                'Model' => 'ES15-33DM',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Tahrik Aksına Kadar Yük Mesafesi (x)' => '730 mm',
                'Dingil Mesafesi (y)' => '1165 mm',
                'Servis Ağırlığı' => '915 kg',
                'Dolu/Boş Aks Yükü (ön/arka)' => '825/1590 kg (dolu), 560/355 kg (boş)',
                'Teker Tipi' => 'Poliüretan',
                'Ön Teker (ØxW)' => 'Ø230×75 mm',
                'Arka Teker (ØxW)' => 'Ø102×73 mm',
                'Ek Teker (ØxW)' => 'Ø100×50 mm',
                'Teker Sayısı (x=tahrik)' => '1x +1/4',
                'Ön İz (b10)' => '538 mm',
                'Arka İz (b11)' => '1170/1270/1370 mm',
                'Direk Kapalı (h1)' => '2128 mm',
                'Kaldırma (h3)' => '3220 mm',
                'Direk Açık (h4)' => '4210 mm',
                'Sürüş Kolu Yüksekliği (h14)' => '1150/1480 mm',
                'Teker Kolları Yüksekliği (h8)' => '101 mm',
                'Alt Yükseklik (h13)' => '60 mm',
                'Toplam Uzunluk (l1)' => '1650 mm',
                'Çatala Kadar Uzunluk (l2)' => '580 mm',
                'Toplam Genişlik (b1/b2)' => '1270/1370/1470 mm',
                'Çatal Ölçüsü (s/e/l)' => '40/100/1070 mm',
                'Taşıyıcı Genişliği (b3)' => '800 mm',
                'Çatallar Arası Mesafe (b5)' => '200~780 mm',
                'Teker Kolları Arası Mesafe (b4)' => '1070/1170/1270 mm',
                'Direk Altı Boşluk (m1)' => '40 mm',
                'Şase Orta Boşluğu (m2)' => '30 mm',
                'Koridor 1000×1200 (Ast)' => '2250 mm',
                'Koridor 800×1200 (Ast)' => '2200 mm',
                'Dönüş Yarıçapı (Wa)' => '1400 mm',
                'Seyir Hızı (dolu/boş)' => '5/5 km/s',
                'Kaldırma Hızı (dolu/boş)' => '0.14/0.20 m/s',
                'İndirme Hızı (dolu/boş)' => '0.13/0.11 m/s',
                'Tırmanma Kabiliyeti (dolu/boş)' => '8/16 %',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.27 kW',
                'Kaldırma Motoru (S3 15%)' => '3 kW',
                'Batarya Gerilimi/Kapasitesi' => '24V 125Ah',
                'Batarya Ağırlığı' => '60 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V 125Ah batarya ile çok vardiyalı görevlerde süreklilik'],
                ['icon' => 'arrows-alt', 'text' => 'Geniş teker kolları (1270–1470 mm) ile farklı palet tiplerine uyum'],
                ['icon' => 'weight-hanging', 'text' => '1500 kg nominal kapasite, 600 mm yük merkezi'],
                ['icon' => 'bolt', 'text' => '1.27 kW sürüş ve 3 kW kaldırma motoru ile seri hareket'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ile güvenli duruş'],
                ['icon' => 'cart-shopping', 'text' => '5 km/s sabit hızda kararlı sevk akışı'],
                ['icon' => 'cog', 'text' => 'PU teker ve basit mekanik direksiyonla düşük bakım'],
                ['icon' => 'certificate', 'text' => 'CE uygunluk ve Avrupa güvenlik standartları']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
