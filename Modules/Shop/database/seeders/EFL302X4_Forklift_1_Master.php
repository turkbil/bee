<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL302X4_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFL302X4';
        $titleTr = 'İXTİF EFL302X4 - 3.0 Ton Li-Ion Karşı Denge Forklift (Manuel-Değiştirilebilir Modüler Batarya)';
        $shortTr = 'EFL302X4, 3.0 ton kapasite ve 500 mm yük merkeziyle 80V/100Ah Li-Ion modüler batarya sistemi sunar. 11/12 km/s hız, 0.29/0.36 m/s kaldırma, 15% eğim kabiliyeti ve PMSM sürüş ile düşük bakım, yüksek verim sağlar. Geniş görüşlü direk ve ferah kabin güvenliği artırır.';

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
                'Sürücü' => 'Elektrikli (Li-Ion)',
                'Kapasite (Q)' => '3000 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Dingil Mesafesi (y)' => '1760 mm',
                'Servis Ağırlığı' => '4354 kg',
                'Ön/Arka Dingil Yükü (yükle)' => '6537 / 817 kg',
                'Ön/Arka Dingil Yükü (yüksüz)' => '1854 / 2696 kg',
                'Lastik Tipi' => 'Pnömatik',
                'Ön Lastik Ölçüsü' => '28x9-15-14PR',
                'Arka Lastik Ölçüsü' => '6.5F-10-10PR',
                'Tekerlek Adedi (ön/arka)' => '2x / 2',
                'Ön İz Genişliği (b10)' => '989 mm',
                'Arka İz Genişliği (b11)' => '980 mm',
                'Direk Eğimi (α/β)' => '6° / 10°',
                'Direk Kapalı Yüksekliği (h1)' => '2265 mm',
                'Serbest Kaldırma (h2)' => '135 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm (opsiyonlarla 2700–3500 mm)',
                'Direk Açık Yüksekliği (h4)' => '4096 mm',
                'Üst Koruma Yüksekliği (h6)' => '2143 mm',
                'Sürücü Koltuğu Yüksekliği (h7)' => '1141 mm',
                'Çeki Kancası Yüksekliği (h10)' => '468 mm',
                'Toplam Uzunluk (l1)' => '3735 mm',
                'Yüke Kadar Uzunluk (l2)' => '2665 mm',
                'Toplam Genişlik (b1/b2)' => '1228 mm',
                'Çatal Ölçüleri (s/e/l)' => '45 × 122 × 1070 mm',
                'Fork Carriage Sınıfı' => 'Class 3A, genişlik 1100 mm',
                'Şasi Altı Yüksekliği (m1/m2)' => '125 / 137 mm',
                'Koridor Genişliği (Ast 1000×1200 Çapraz)' => '4115 mm',
                'Koridor Genişliği (Ast 800×1200 Boy)' => '4315 mm',
                'Dönüş Yarıçapı (Wa)' => '2428 mm',
                'Sürüş Hızı (yüklü/boş)' => '11 / 12 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.29 / 0.36 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.4 / 0.4 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '15% / 15%',
                'Servis Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60dk)' => '8 kW PMSM',
                'Kaldırma Motoru (S3 15%)' => '16 kW',
                'Akü' => '80V / 100Ah (3 modül × 26 kg, manuel değiştirme)',
                'Akü Ağırlığı' => '220 kg (toplam)',
                'Sürücü Kontrolü' => 'PMSM (Permanent Magnet Synchronous Motor)',
                'Direksiyon' => 'Hidrolik',
                'Operatör Gürültü Seviyesi' => '60 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '80V/100Ah hafif, manuel değiştirilebilir modüler Li-Ion batarya sistemi'],
                ['icon' => 'arrows-alt', 'text' => 'Geniş görüşlü direk ve ferah kabin ile artırılmış görüş'],
                ['icon' => 'bolt', 'text' => 'PMS motor ve akıllı BMS ile yüksek enerji verimliliği'],
                ['icon' => 'plug', 'text' => 'Opsiyonel çoklu batarya şarj istasyonu ile dışarıda şarj'],
                ['icon' => 'shield-alt', 'text' => 'IP seviyesinde suya dayanıklı, zorlu ortamlara uygun tasarım'],
                ['icon' => 'gauge', 'text' => '11/12 km/s hız ve 15% eğim performansı'],
                ['icon' => 'cog', 'text' => 'Düşük bakım ihtiyacı ve kolay servis erişimi'],
                ['icon' => 'star', 'text' => 'Geniş pedallar, yeni koltuk ve büyük LED ekran ile konfor']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
