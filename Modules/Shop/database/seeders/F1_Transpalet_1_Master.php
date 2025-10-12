<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F1_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1; // İXTİF
        $sku = 'F1';

        $titleTr = 'İXTİF F1 - 1.5 Ton Akülü Transpalet (AGM 24V/65Ah)';
        $shortTr = 'F1; endüstriyel kullanımlar için dayanıklı, platform tabanlı mimarisiyle bakım ve filo yönetimini kolaylaştıran 1.5 ton sınıfı elektrikli transpalettir. 24V/65Ah AGM akü ve 24V/10A entegre şarj ile 5–6 saat gerçek çalışma sunar.';

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

            // Teknik özellikler (PDF verisine göre)
            'technical_specs' => json_encode([
                'Model' => 'F1',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya Kumandalı',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Tahrik Aksına Merkezden Yüke (x)' => '883/946 mm',
                'Dingil Mesafesi (y)' => '1157/1220 mm',
                'Lastik Türü' => 'Poliüretan',
                'Tahrik Tekerleği' => '210×70 mm',
                'Yük Tekerleri' => 'Φ80×60 (Φ74×88) mm',
                'Destek Tekerleri' => 'Φ74×30 mm (opsiyonel)',
                'Tekerlek Dizilimi' => '1× 2/4 — 1× 2/2',
                'Kaldırma Yüksekliği (h3)' => '105 mm',
                'Kumanda Kolu Yüksekliği (h14) min/max' => '750/1170 mm',
                'Fork Altı Yükseklik (h13)' => '82 mm',
                'Toplam Uzunluk (l1)' => '1604 mm',
                'Yüze Kadar Uzunluk (l2)' => '450 mm',
                'Toplam Genişlik (b1/b2)' => '695/620 mm',
                'Çatal Ölçüleri (s/e/l)' => '55/150/1150 mm',
                'Çatal Aralığı (b5)' => '685/560 mm',
                'Yer Yüksekliği (m2)' => '25 mm',
                '1000×1200 Palet Koridor (Ast)' => '2187 mm',
                '800×1200 Palet Koridor (Ast)' => '2244 mm',
                'Dönüş Yarıçapı (Wa)' => '1426 mm',
                'Yürüyüş Hızı (yüklü/boş)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.020 / 0.026 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.069 / 0.055 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '5% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru' => '0.75 kW (S2 60dk)',
                'Kaldırma Motoru' => '0.5 kW (S3 15%)',
                'Akü' => '24V / 65Ah AGM (12V×2)',
                'Akü Ağırlığı' => '≈ 30 kg (15 kg ×2)',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '< 74 dB(A)',
                'Tahmini Gerçek Çalışma Süresi' => '5–6 saat',
                'Şarj Cihazı' => '24V / 10A — Entegre'
            ], JSON_UNESCAPED_UNICODE),

            // PDF mesajlarına dayalı özellikler (8 adet)
            'features' => json_encode([
                ['icon' => 'layer-group', 'text' => 'Platform tabanlı F mimarisi ile konfigürasyon ve bakım kolaylığı'],
                ['icon' => 'battery-full', 'text' => '24V/65Ah AGM akü ile dengeli enerji ve dayanıklılık'],
                ['icon' => 'plug', 'text' => '24V/10A entegre şarj cihazı — fişe tak & şarj et'],
                ['icon' => 'shipping-fast', 'text' => '3 ünite/kolİ sevkiyat ile tedarik ve navlunda maliyet avantajı'],
                ['icon' => 'shield-alt', 'text' => 'Endüstriyel uygulamalara uygun sağlam şasi ve koruma'],
                ['icon' => 'cog', 'text' => 'Pazarda kanıtlanmış tahrik ünitesi ile düşük arıza oranı'],
                ['icon' => 'gauge', 'text' => 'Gerçek kullanımda 5–6 saat çalışma ile vardiya verimliliği'],
                ['icon' => 'check-circle', 'text' => 'Günlük kullanım için kolay yönetim ve hızlı bakım erişimi']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
