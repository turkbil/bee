<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RPL201_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'RPL201';
        $titleTr = 'İXTİF RPL201 - 2.0 Ton Li-Ion Platformlu Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2.0 ton kapasiteli, katlanır platform ve yan korumalı, elektrikli sürüşlü Li-Ion transpalet. 24V/205Ah enerji, 7.5/8 km/s hız, otomatik dönüşte hız azaltma ve güçlendirilmiş denge tekerleri ile uzun mesafe kullanıma uygun.'], JSON_UNESCAPED_UNICODE),
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
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Ayakta (platformlu)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Servis Ağırlığı' => '670 kg (Li-Ion) / 755 kg (Kurşun asit)',
                'Tahrik aksına mesafe (x)' => '980 mm',
                'Dingil açıklığı (y)' => '1531 mm',
                'Teker tipleri' => 'Poliüretan (PU)',
                'Tahrik tekeri (ön)' => 'Ø230×75',
                'Yük tekeri (arka)' => 'Ø85×70 / Ø83×115',
                'Denge tekerleri (castor)' => 'Ø130×55',
                'Ön/Arka teker sayısı' => '1x+2/4 / 1x+2/2',
                'Ön iz genişliği (b10)' => '510 mm',
                'Arka iz genişliği (b11)' => '370 / 515 mm',
                'Kaldırma yüksekliği (h3)' => '120 mm',
                'Sürüş kolu min./maks. (h14)' => '1075 / 1288 mm',
                'İndirilmiş yükseklik (h13)' => '85 mm',
                'Toplam uzunluk (l1)' => '1954 mm',
                'Yüze kadar uzunluk (l2)' => '804 mm',
                'Toplam genişlik (b1/b2)' => '734 mm',
                'Çatal ölçüsü (s/e/l)' => '55 × 170 × 1150 mm',
                'Çatallar arası mesafe (b5)' => '540 / 685 mm',
                'Şasi alt boşluk (m2)' => '30 mm',
                'Koridor genişliği 1000×1200 (Ast)' => '2606 mm',
                'Koridor genişliği 800×1200 (Ast)' => '2463 mm',
                'Dönüş yarıçapı (Wa)' => '1806 mm',
                'Yürüyüş hızı (yüklü/boş)' => '7.5 / 8 km/s',
                'Kaldırma hızı (yüklü/boş)' => '0.050 / 0.054 m/s',
                'İndirme hızı (yüklü/boş)' => '0.067 / 0.054 m/s',
                'Maks. %eğim (yüklü/boş)' => '8 / 16 %',
                'Servis freni' => 'Elektromanyetik',
                'Sürüş motoru (S2 60dk)' => '1.6 kW',
                'Kaldırma motoru (S3 15%)' => '2.2 kW',
                'Akü (V/Ah)' => '24 / 205 (Li-Ion) | 24 / 210 (Kurşun asit)',
                'Akü ağırlığı' => '62 kg (Li-Ion) / 255 kg (Kurşun asit)',
                'Sürüş kontrolü' => 'AC',
                'Direksiyon tasarımı' => 'Elektronik',
                'Ses seviyesi (kulakta)' => '74 dB(A)',
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => 'Bakım gerektirmeyen Li-Ion akü ile fırsat şarjı ve sıfır bakım'],
                ['icon' => 'arrows-alt', 'text' => 'Dönüşlerde otomatik hız azaltma ile yük ve operatör güvenliği'],
                ['icon' => 'bolt', 'text' => 'Geliştirilmiş karşı ağırlık ve castor düzeni ile yüksek seyir stabilitesi'],
                ['icon' => 'hand', 'text' => 'Elektronik güç destekli, ergonomik tiller ile zahmetsiz sürüş'],
                ['icon' => 'plug', 'text' => 'Harici şarj soketi ile kapağı açmadan hızlı şarj prosedürü'],
                ['icon' => 'shield-alt', 'text' => 'Genişletilmiş çerçeve ve yan kollarla darbelere karşı koruma'],
                ['icon' => 'star', 'text' => 'Katlanır platform ile uzun mesafelerde konfor ve verimlilik'],
                ['icon' => 'layer-group', 'text' => 'RPL serisi mimarisi ile 2-3 ton arası geniş uygulama aralığı'],
            ], JSON_UNESCAPED_UNICODE)
        ]);
    }
}
