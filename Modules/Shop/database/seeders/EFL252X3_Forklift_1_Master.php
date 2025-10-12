<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL252X3_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFL252X3';
        $titleTr = 'İXTİF EFL252X3 - 2.5 Ton Li-Ion Karşı Ağırlık Forklift';

        $shortTr = 'İXTİF EFL252X3; 2.5 ton kapasite, 500 mm yük merkezi, 80V/150Ah Li‑Ion akü ve PMSM sürüş ile hem kapalı alanlarda hem dış sahada verimli çalışır. 2250 mm dönüş yarıçapı, yüksek yerden açıklık ve tek faz entegre şarj cihazı ile çok vardiyalı kullanıma uygundur.';

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
                'Sürüş' => 'Elektrikli',
                'Kapasite (Q)' => '2500 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Tahrik Aksı Merkezine Kadar Yük Mesafesi (x)' => '486 mm',
                'Dingil Mesafesi (y)' => '1650 mm',
                'Servis Ağırlığı' => '4255 kg',
                'Dingil Yükü (yüklü, ön/arka)' => '6925 / 530 kg',
                'Dingil Yükü (yüksüz, ön/arka)' => '1805 / 2450 kg',
                'Lastik Tipi' => 'Katı lastik',
                'Ön Lastik Ölçüsü' => '7.00-12',
                'Arka Lastik Ölçüsü' => '18x7-8',
                'Direk Eğimi (ileri/geri, α/β)' => '6° / 10°',
                'Direk Kapalı Yüksekliği (h1)' => '2070 mm',
                'Serbest Kaldırma (h2)' => '135 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Açık Yüksekliği (h4)' => '4095 mm',
                'Üst Koruma (kabin) Yüksekliği (h6)' => '2170 mm',
                'Koltuk Yüksekliği (h7)' => '1110 mm',
                'Çeki Kancası Yüksekliği (h10)' => '295 mm',
                'Toplam Uzunluk (l1)' => '3611 mm',
                'Çatal Ucuna Kadar Uzunluk (l2)' => '2541 mm',
                'Toplam Genişlik (b1/b2)' => '1154 mm',
                'Çatal Ölçüleri (s/e/l)' => '50 × 122 × 1070 mm',
                'Çatal Taşıyıcı Sınıfı' => '2A',
                'Çatal Taşıyıcı Genişliği' => '1040 mm',
                'Yerden Yükseklik (mast altı, m1)' => '120 mm',
                'Yerden Yükseklik (dingil merkezi, m2)' => '150 mm',
                'Koridor Genişliği 1000×1200 enine (Ast)' => '3946 mm',
                'Koridor Genişliği 800×1200 boyuna (Ast)' => '4146 mm',
                'Dönüş Yarıçapı (Wa)' => '2250 mm',
                'Yürüyüş Hızı (yüklü/boş)' => '11 / 12 km/sa',
                'Kaldırma Hızı (yüklü/boş)' => '0.28 / 0.36 m/sn',
                'İndirme Hızı (yüklü/boş)' => '0.40 / 0.43 m/sn',
                'Azami Eğim (yüklü/boş)' => '15% / 15%',
                'Hizmet Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60dk)' => '8 kW',
                'Kaldırma Motoru (S3 15%)' => '16 kW',
                'Batarya Gerilim/Kapasite' => '80V / 150Ah (opsiyon 280Ah)',
                'Sürücü Tarafı Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '80V Li‑Ion akü: bakım gerektirmez, fırsat şarjına uygun'],
                ['icon' => 'bolt', 'text' => 'PMSM teknoloji: %10’a kadar enerji tasarrufu ve daha uzun çalışma süresi'],
                ['icon' => 'arrows-alt', 'text' => '2250 mm dönüş yarıçapı ile dar alanlarda çevik manevra'],
                ['icon' => 'shield-alt', 'text' => 'Suya karşı korumalı yapı; yağmurda dışarıda güvenle çalışma'],
                ['icon' => 'cog', 'text' => 'Yüksek yerden açıklık ve büyük lastikler; engebeli zeminlere uygun'],
                ['icon' => 'check-circle', 'text' => 'Tek faz entegre şarj cihazı (16A fiş) ile kolay enerji erişimi'],
                ['icon' => 'briefcase', 'text' => 'Ergonomik çalışma alanı: ayarlanabilir direksiyon ve konfor pedalı'],
                ['icon' => 'warehouse', 'text' => 'İç ve dış mekanlarda çok yönlü kullanım']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
