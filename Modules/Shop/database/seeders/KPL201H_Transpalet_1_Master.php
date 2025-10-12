<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KPL201H_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'KPL201H';
        $titleTr = 'İXTİF KPL201H - 2.0 Ton Li-Ion Sürücülü Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2000 kg kapasiteli, 24V/205Ah Li-Ion akü ve güçlü AC tahrik motoru ile yoğun vardiyalarda yüksek hız (yükte 9 km/s, yüksüz 12 km/s), otomatik dönüş hızı azaltma, güç destekli direksiyon ve süspansiyonlu platform sunar. Kompakt şasi ve yarı kapalı operatör bölmesiyle güvenli ve konforlu.'], JSON_UNESCAPED_UNICODE),
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
                'Üretici' => 'İXTİF',
                'Model' => 'KPL201H',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Ayakta sürüş (platformlu)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Servis Ağırlığı' => '705 kg',
                'Tekerlek Tipi' => 'Poliüretan',
                'Ön Tekerlek Ölçüsü' => 'Ø85x70 mm',
                'Arka Tekerlek Ölçüsü' => 'Ø230x75 mm',
                'Ek Tekerlek (Caster)' => 'Ø130x55 mm',
                'Ön/Arka Tekerlek Adedi' => '1x+2 / 4',
                'Tiller Yüksekliği (min/max) h14' => '1154 / 1254 mm',
                'Alçalma Yüksekliği (h13)' => '85 mm',
                'Toplam Uzunluk (l1)' => '2195 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '-184 mm kısa şasi / 1045 mm',
                'Toplam Genişlik (b1/b2)' => '734 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 x 170 x 1150 mm',
                'Çatal Arası (b5)' => '540 / 685 mm',
                'Dönüş Yarıçapı (Wa)' => '2034 mm',
                'Sürüş Hızı (yük/yüksüz)' => '9 / 12 km/s',
                'Azami Eğim (yük/yüksüz)' => '8% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '3.0 kW AC dikey',
                'Kaldırma Motoru (S3 15%)' => '2.2 kW',
                'Batarya (V/Ah)' => '24V / 205Ah Li-Ion',
                'Standart Şarj Cihazı' => 'Harici 100A şarj cihazı',
                'Gürültü Seviyesi' => '74 dB(A)',
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/205Ah Li-Ion akü ile fırsat şarjı ve sıfır bakım'],
                ['icon' => 'bolt', 'text' => '3.0 kW AC dikey tahrik motoru ile güçlü ivmelenme'],
                ['icon' => 'arrows-alt', 'text' => 'Kısa şasi ve düşük ağırlık merkezi ile yüksek stabilite'],
                ['icon' => 'steering-wheel', 'text' => 'Güç destekli elektronik direksiyon ile hassas kontrol'],
                ['icon' => 'shield-alt', 'text' => 'Dönüşte otomatik hız azaltma ve elektromanyetik fren'],
                ['icon' => 'briefcase', 'text' => 'Yarı kapalı operatör bölmesi ve dolgulu sırt dayama'],
                ['icon' => 'layer-group', 'text' => 'Süspansiyonlu platform ile konforlu sürüş'],
                ['icon' => 'gauge', 'text' => 'Yükte 9 km/s, yüksüz 12 km/s seyir hızı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
