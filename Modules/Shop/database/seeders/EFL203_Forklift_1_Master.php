<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL203_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1;    // İXTİF
        $sku = 'EFL203';
        $titleTr = 'İXTİF EFL203 - 2.0 Ton 80V Li-Ion Dört Teker Karşı Ağırlıklı Forklift';

        $shortTr = 'İXTİF EFL203; 2000 kg kapasite, 500 mm yük merkezi ve 80V 230Ah Li-Ion güç sistemiyle 
yüksek verim sunan dört teker elektrikli forklift. 14/15 km/s hız, 0.29/0.36 m/s kaldırma, %15/%20 eğim 
kabiliyeti ve 3540 kg servis ağırlığıyla iç-dış sahalarda güven ve konfor sağlar.';

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
                'Yük Kapasitesi (Q)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Tahrik' => 'Elektrikli (AC)',
                'Operatör Tipi' => 'Oturmalı',
                'Teker Tipi' => 'Pnömatik',
                'Ön Teker' => '7.00-12-12PR',
                'Arka Teker' => '18x7-8-14PR',
                'Aks Mesafesi (y)' => '1540 mm',
                'Servis Ağırlığı' => '3540 kg',
                'Direk İleri/Geri Yatma (α/β)' => '6° / 10°',
                'Direk Kapalı Yükseklik (h1)' => '2090 mm',
                'Serbest Kaldırma (h2)' => '120 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Açık Yükseklik (h4)' => '4025 mm',
                'Üst Koruma Yüksekliği' => '2165 mm',
                'Sürüş Ünitesine Çatal Mesafesi (x)' => '495 mm',
                'Toplam Uzunluk (l)' => '3426 mm',
                'Yüke Kadar Uzunluk (l2)' => '2356 mm',
                'Toplam Genişlik (b/b1)' => '1154 mm',
                'Çatal Ölçüsü (s×e×l)' => '40×122×1070 mm',
                'Çatal Taşıyıcı Sınıfı' => '2A',
                'Dönüş Yarıçapı (Wa)' => '2110 mm',
                '1000×1200 enine Koridor (Ast)' => '3805 mm',
                '800×1200 boyuna Koridor (Ast)' => '4005 mm',
                'Sürüş Hızı (yük/yüksüz)' => '14 / 15 km/s',
                'Kaldırma Hızı (yük/yüksüz)' => '0.29 / 0.36 m/s',
                'İndirme Hızı (yük/yüksüz)' => '0.43 / 0.44 m/s',
                'Maks. Tırmanma (yük/yüksüz)' => '%15 / %20',
                'Fren' => 'Hidrolik servis, mekanik park',
                'Sürüş Motoru (S2 60 dk)' => '10 kW',
                'Kaldırma Motoru (S3 15%)' => '16 kW',
                'Akü' => '80V 230Ah (Li-Ion)',
                'Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '80V Li-Ion batarya ile hızlı şarj, uzun çevrim ömrü ve bakım gerektirmeyen enerji sistemi'],
                ['icon' => 'bolt', 'text' => 'IC tarzı şasi ve güçlü yürüyüş ile verimli hız ve ivmelenme'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt gövde ve 2110 mm dönüş yarıçapı ile dar koridor manevrası'],
                ['icon' => 'shield-alt', 'text' => 'Yeni direk ve cıvatalı OHG ile iyileştirilmiş görüş ve stabilite'],
                ['icon' => 'store', 'text' => 'Geniş ayak boşluğu, ayarlanabilir direksiyon ve kol dayamalı koltuk ile ergonomi'],
                ['icon' => 'building', 'text' => 'Büyük LED ekran ve güçlü LED farlar ile güvenli dış saha kullanımı'],
                ['icon' => 'plug', 'text' => 'Entegre şarj soketi ve harici şarj seçeneği ile esnek enerji yönetimi'],
                ['icon' => 'award', 'text' => 'Yağışlı zemine uygun suya dayanıklı tasarım ve yüksek şasi açıklığı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info('✅ Master oluşturuldu: EFL203');
    }
}
