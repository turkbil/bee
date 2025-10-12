<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESA121_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ESA121';

        $titleTr = 'İXTİF ESA121 - 1.2 Ton Elektrikli Yaya Tipi İstif Makinesi';
        $shortTr = 'ESA121, 1.2 ton kapasiteli, H-kesit direk ve yandan iki kaldırma silindiriyle stabil, 24V/105Ah AGM veya Li-ion seçenekli, entegre şarj cihazlı kompakt istif makinesidir; 4.0/4.5 km/s hız, 0.15/0.24 m/s kaldırma ve 2930 mm kaldırma yüksekliği sunar.';

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
                'Sürüş' => 'Elektrik', // Drive
                'Kapasite' => '1200 kg', // Q
                'Yük Merkezi Mesafesi' => '600 mm', // c
                'Servis Ağırlığı' => '700 kg',
                'Direk Kapalı Yüksekliği (h1)' => '1995 mm',
                'Kaldırma Yüksekliği (h3)' => '2930 mm',
                'Direk Açık Yüksekliği (h4)' => '3460 mm',
                'Toplam Uzunluk (l1)' => '1760 mm',
                'Yüze Kadar Uzunluk (l2)' => '610 mm',
                'Toplam Genişlik (b1/b2)' => '826 mm',
                'Çatal Boyutları (s/e/l)' => '60/170/1150 mm',
                'Dönüş Yarıçapı (Wa)' => '1480 mm',
                'Maks. Eğim (yüklü/boş)' => '3% / 10%',
                'Seyir Hızı (yüklü/boş)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.15 / 0.24 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.21 / 0.20 m/s',
                'Akü' => '24V / 105Ah (AGM veya Li-ion)',
                'Entegre Şarj Cihazı' => 'Standart',
                'Tahrik Motoru (S2 60 dk)' => '0.65 kW',
                'Kaldırma Motoru (S3 15%)' => '3.0 kW',
                'Sürüş Kontrolü' => 'DC',
                'Ses Seviyesi' => '74 dB(A)',
                'Teker Tipi' => 'Poliüretan',
                'Şasi Orta Noktası Yerden Yükseklik (m2)' => '23 mm'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'shield-alt', 'text' => 'H-kesit direk ve iki yan silindir ile titreşimi azaltan rijit yapı'],
                ['icon' => 'bolt', 'text' => '3.0 kW güçlendirilmiş kaldırma motoru ile hızlı kaldırma/indirme'],
                ['icon' => 'battery-full', 'text' => 'AGM ve Li-ion batarya seçenekleri, bakım gerektirmeyen enerji'],
                ['icon' => 'plug', 'text' => 'Standart entegre şarj cihazı ile her prizde kolay şarj'],
                ['icon' => 'layer-group', 'text' => 'Mono ve duplex direk seçenekleriyle farklı istif senaryoları'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve 1480 mm dönüş yarıçapıyla dar alan çevikliği'],
                ['icon' => 'briefcase', 'text' => 'Kullanıcı dostu kapak: evrak, eşya, bardak gözü ve opsiyonel USB'],
                ['icon' => 'cog', 'text' => 'Pazarda kanıtlanmış tahrik ünitesi ile yüksek güvenilirlik']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master: ESA121 (İstif) güncellendi/eklendi');
    }
}
