<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EXP15_Otonom_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 5; // Otonom/AGV
        $brandId = 1; // İXTİF
        $sku = 'EXP15';
        $titleTr = 'İXTİF EXP15 - 1.5 Ton Otomatik Palet Transpaleti';
        $shortTr = 'EXP15, 1500 kg kapasiteli, 24V/60Ah Li-İon tak-çıkar bataryalı, 180° lidar ve 2D görsel navigasyonlu otomatik palet transpaletidir. 1.1/1.25 m/s hız, 1400 mm dönüş yarıçapı ve WiFi gerektirmeyen basit kurulum ile 10 göreve kadar öğrenir.';

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
                'Model' => 'EXP15',
                'Sürüş' => 'Elektrik (AC)',
                'Operatör Tipi' => 'Yaya',
                'Kapasite' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik Aksı - Çatal Ucu Mesafesi (x)' => '950 mm',
                'Dingil Mesafesi (y)' => '1130 mm',
                'Servis Ağırlığı' => '230 kg',
                'Aks Yükü (Yüklü, Ön/Arka)' => '1100/700 kg',
                'Aks Yükü (Yüksüz, Ön/Arka)' => '30/200 kg',
                'Toplam Yükseklik (h1)' => '1420 mm',
                'Kaldırma Yüksekliği (h3)' => '90 mm',
                'Alçak Konum Yüksekliği (h13)' => '85 mm',
                'Toplam Uzunluk (l1)' => '1620 mm',
                'Çatala Kadar Uzunluk (l2)' => '450 mm',
                'Toplam Genişlik (b1/b2)' => '636 (650) (700) mm',
                'Çatal Ölçüleri (s/e/l)' => '55/150/1150 mm',
                'Çatallar Arası Genişlik (b5)' => '540/600/685 mm',
                'Tekerlek Tabanı Altı Yerden Yükseklik' => '20 mm',
                '1000×1200 (enine) Koridor Genişliği (Ast)' => '1860 mm',
                'Dönüş Yarıçapı (Wa)' => '1400 mm',
                'Sürüş Hızı (Yüklü/Yüksüz)' => '1.1 / 1.25 m/s',
                'Kaldırma Hızı (Yüklü/Yüksüz)' => '0.020 / 0.035 m/s',
                'İndirme Hızı (Yüklü/Yüksüz)' => '0.058 / 0.046 m/s',
                'Maks. Eğimi (Yüklü/Yüksüz)' => '5% / 5%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.9 kW',
                'Kaldırma Motoru (S3 15%)' => '0.8 kW',
                'Batarya Voltaj/Kapasite' => '24V / 60Ah (Li-İon, tak-çıkar)',
                'Batarya Ağırlığı' => '14 kg',
                'Direksiyon' => 'Elektrik',
                'Ses Basınç Seviyesi' => '<74 dB(A)',
                'Kullanım Ortamı' => 'İç Mekân',
                'Güvenlik' => '180° Lidar, acil durdurma butonu, ikili lazer kapsama',
                'Konumlandırma' => '2D görsel navigasyon',
                'Park Hassasiyeti' => '±20 mm',
                'Navigasyon Hassasiyeti' => '±20 mm',
                'Görev Hafızası' => '10 adede kadar çoklu görev/rota',
                'Kurulum' => 'WiFi gerekmez; 50 m’ye kadar reflektörlerle hızlı kurulum'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/60Ah Li-İon tak-çıkar batarya ile hızlı değişim ve minimum duruş'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve 1400 mm dönüş yarıçapı ile dar alan çevikliği'],
                ['icon' => 'bolt', 'text' => 'AC sürüş motoru ve elektromanyetik fren ile güvenli performans'],
                ['icon' => 'shield-alt', 'text' => '180° lidar ve çift seviye lazer ile çarpışma önleyici kapsama'],
                ['icon' => 'microchip', 'text' => '2D görsel navigasyonla hassas konumlandırma ve ±20 mm park'],
                ['icon' => 'plug', 'text' => 'WiFi’siz, dokun-çalış kurulum: reflektörle 50 m’ye kadar hat'],
                ['icon' => 'cart-shopping', 'text' => '10 farklı görev/rota hafızası ile görev seçimi (+/− tuşları)'],
                ['icon' => 'cog', 'text' => 'Elektrikli transpalet kadar basit bakım ve servis gereksinimi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
