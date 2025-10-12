<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFS151_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1;    // İXTİF
        $sku = 'EFS151';
        $titleTr = 'İXTİF EFS151 - 1.5 Ton 3 Teker Elektrikli Forklift (48V Li-Ion)';
        $shortTr = 'EFS151, 1500 kg kapasiteli, 48V Li-Ion akü ve entegre 48V/30A şarj cihazı ile tasarlanmış süper kompakt 3 tekerli karşı dengeli forklift. 1995 mm koruyucu tavan yüksekliği, 1535 mm dönüş yarıçapı ve 6 kW AC sürüş motoru ile dar alanlarda çevik ve verimlidir.';

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
                'Sürüş' => 'Elektrikli (AC)',
                'Kapasite' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Servis Ağırlığı' => '2200 kg',
                'Teker Tipi' => 'Katı lastik (solid rubber)',
                'Ön Teker Boyutu' => '330×145',
                'Arka Teker Boyutu' => '16×6-8',
                'Teker Düzeni (ön/arka)' => '2/1x',
                'Ön İz Genişliği' => '905 mm',
                'Direk Eğimi (ileri/geri)' => '3° / 5°',
                'Direk Kapalı Yüksekliği (h1)' => '1980 mm',
                'Serbest Kaldırma (h2)' => '100 mm (standart direk)',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Açık Yüksekliği (h4)' => '4054 mm',
                'Koruyucu Tavan Yüksekliği (h6)' => '1995 mm',
                'Sürücü Koltuk Yüksekliği' => '985 mm',
                'Çeki Kancası Yüksekliği' => '660 mm',
                'Toplam Uzunluk (l1)' => '2717 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '1797 mm',
                'Toplam Genişlik (b1/b2)' => '1060 mm',
                'Çatal Ölçüleri (s/e/l)' => '40×100×920 mm',
                'Çatal Taşıyıcı Sınıfı' => '2A',
                'Çatal Taşıyıcı Genişliği' => '1040 mm',
                'Alt Mast Boşluğu' => '90 mm',
                'Aks Ortası Boşluğu' => '78 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '3000 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '3200 mm',
                'Dönüş Yarıçapı (Wa)' => '1535 mm',
                'Sürüş Hızı (yüklü/boş)' => '8 / 9 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.25 / 0.30 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.44 / 0.425 m/s',
                'Maks. Eğim Kabiliyeti (yüklü/boş)' => '10% / 12%',
                'Servis Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60dk)' => '6 kW',
                'Kaldırma Motoru (S3 15%)' => '5.5 kW',
                'Akü (Li-Ion)' => '48V / 150Ah (entegre 48V/30A şarj)',
                'Akü Opsiyonu (AGM)' => '48V / 180Ah (harici 48V/30A şarj)',
                'Bluetooth Servis Uygulaması' => 'Parametre ayarı ve arıza kodu okuma'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V/150Ah Li-Ion akü ile fırsat şarjı ve yüksek verim'],
                ['icon' => 'arrows-alt', 'text' => '1535 mm dönüş yarıçapı ile dar alanlarda yüksek manevra'],
                ['icon' => 'building', 'text' => '1995 mm koruyucu tavan ile alçak katlara ve kapılardan geçiş'],
                ['icon' => 'microchip', 'text' => 'Güçlü AC sürüş sistemi ve hassas kontrol'],
                ['icon' => 'cog', 'text' => 'Bakım gerektirmeyen elektrik mimarisi ve kolay servis'],
                ['icon' => 'bolt', 'text' => '5.5 kW kaldırma motoru ile seri istifleme'],
                ['icon' => 'shield-alt', 'text' => 'Hidrolik servis freni ve mekanik park freni'],
                ['icon' => 'star', 'text' => 'Ergonomik ayarlı direksiyon ve konforlu koltuk']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
