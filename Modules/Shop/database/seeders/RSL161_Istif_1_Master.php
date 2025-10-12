<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSL161_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'RSL161';
        $titleTr = 'İXTİF RSL161 - 1.6 Ton Li-Ion İstif Makinesi';
        $shortTr = 'İXTİF RSL161, katlanır platformu, elektronik güç destekli direksiyonu ve 24V 205Ah Li‑Ion bataryasıyla uzun mesafeli istif işlerinde 11 km/saat hız ve 0.26 m/s kaldırma performansı sunar. Harici şarj soketi, oransal hidrolik ve güvenli dönüş hız azaltma standarttır.';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortTr], JSON_UNESCAPED_UNICODE),
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'is_master_product' => true,
            'is_active' => true,
            'base_price' => 0.00,
            'price_on_request' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode(['Üretici' => 'İXTİF', 'Model' => 'RSL161', 'Sürüş' => 'Elektrikli', 'Taşıma Kapasitesi (Q)' => '1600 kg', 'Yük Merkezi Mesafesi (c)' => '600 mm', 'Servis Ağırlığı' => '1200 kg', 'Maks. Kaldırma Yüksekliği' => '3000 mm', 'Mast Yükselmiş Yükseklik (h4)' => '3425 mm', 'Tiller Yüksekliği (min/max)' => '1125 / 1361 mm', 'Şasi Uzunluğu (l2 yüzüne kadar)' => '821 mm', 'Toplam Genişlik' => '850 mm', 'Çatal Ölçüleri (s/e/l)' => '65×170×1150 mm', 'Dönüş Yarıçapı (Wa)' => '1560 mm', 'Sürüş Hızı (yüklü/boş)' => '9 / 11 km/h', 'Kaldırma Hızı (yüklü/boş)' => '0.2 / 0.26 m/s', 'İndirme Hızı (yüklü/boş)' => '0.4 / 0.36 m/s', 'Maks. % Eğim (yüklü/boş)' => '8 / 12 %', 'Akü' => '24V / 205Ah Li-Ion', 'Sürüş Motoru' => '3 kW', 'Kaldırma Motoru (S3 15%)' => '4.5 kW', 'Enerji Tüketimi (EN 16796)' => '1.01 kWh/h', 'VDI 2198 Verimi' => '54.4 t/h (37 t/kWh)', 'Fren' => 'Elektromanyetik', 'Tahrik Kontrol' => 'AC'], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([['icon' => 'battery-full', 'text' => '24V 205Ah Li-Ion batarya ile hızlı ve fırsat şarjı, sıfır bakım'], ['icon' => 'bolt', 'text' => '11 km/saate kadar seyir ve 0.26 m/s kaldırma hızı ile yüksek verim'], ['icon' => 'arrows-alt', 'text' => 'Kompakt şasi ve 850 mm genişlik ile dar koridor manevrası'], ['icon' => 'shield-alt', 'text' => 'Dönüş hız düşürme ve elektromanyetik fren ile güvenlik'], ['icon' => 'cog', 'text' => 'Düşük bakım: fırçasız AC sürüş ve erişilebilir servis noktaları'], ['icon' => 'star', 'text' => 'Yeni renkli ekran ile anlık parametre ve akü takibi'], ['icon' => 'briefcase', 'text' => 'Katlanır operatör platformu ile uzun mesafe konfor'], ['icon' => 'microchip', 'text' => 'Elektronik oransal kaldırma/indirme ile hassas istifleme']], JSON_UNESCAPED_UNICODE)
        ]);
        $this->command->info("✅ Master eklendi: {$sku}");
    }
}
