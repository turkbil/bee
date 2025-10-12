<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TCL101_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'TCL101';

        $titleTr = 'İXTİF TCL101 - 1.0 Ton 80V Li-Ion Üç Tekerli Forklift';
        $shortTr = 'Kompakt şasi, h6<2000 mm üst koruma yüksekliği ve 1422 mm dönüş yarıçapı ile dar alanlarda çevik manevra sunar. 80V Li-Ion batarya (entegre şarj), PMSM motor, 13 km/sa hıza kadar performans ve otomatik park freni ile güvenli, verimli operasyon sağlar.';

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
                'Sürücü' => 'Elektrikli (80V)',
                'Operatör Tipi' => 'Oturmalı',
                'Kapasite (Q)' => '1000 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Tahrik Aksı Merkezine Yük Mesafesi (x)' => '310 mm',
                'Dingil Mesafesi (y)' => '1200 mm',
                'Servis Ağırlığı' => '1950 kg',
                'Lastik Tipi' => 'Katı (solid)',
                'Ön Lastik Ölçüsü' => '16×6-8',
                'Arka Lastik Ölçüsü' => '3.5-5',
                'Direk Eğimi (İleri/Geri) (α/β)' => '6°/6°',
                'Direk Kapalı Yüksekliği (h1/h2)' => '1990 mm',
                'Serbest Kaldırma (h2/h3)' => '120 mm',
                'Kaldırma Yüksekliği (h3/h4)' => '3000 mm',
                'Direk Açık Yüksekliği (h4/h5)' => '3919 mm',
                'Üst Koruma Yüksekliği (h6)' => '1960 mm (h6<2000 mm varyantı broşürde vurgulu)',
                'Koltuk Yüksekliği (h7)' => '925 mm',
                'Çeki Kancası Yüksekliği (h10)' => '483 mm',
                'Toplam Uzunluk (l1)' => '2604 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '1684 mm',
                'Toplam Genişlik (b1/b2)' => '1020 mm',
                'Çatal Ölçüleri (s/e/l)' => '35×100×920 mm',
                'Fork Kızak Genişliği (b3)' => '960 mm',
                'Şasi Altı Boşluğu (Direk Altı) (m1)' => '93 mm',
                'Tekerlek Merkezi Altı Boşluk (m2)' => '89 mm',
                'Koridor Genişliği 1000×1200 (enlemesine) (Ast)' => '3063 mm',
                'Koridor Genişliği 800×1200 (boyuna) (Ast)' => '3184 mm',
                'Dönüş Yarıçapı (Wa)' => '1422 mm',
                'Sürüş Hızı (Yüklü/Boş)' => '11/13 km/sa',
                'Kaldırma Hızı (Yüklü/Boş)' => '280/350 mm/sn',
                'İndirme Hızı (Yüklü/Boş)' => '350/350 mm/sn',
                'Maks. Eğim Kabiliyeti (Yüklü/Boş)' => '13% / 15%',
                'Servis Freni' => 'Elektromanyetik',
                'Park Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '2.0 kW × 2 (çift tahrik)',
                'Kaldırma Motoru (S3 15%)' => '7 kW',
                'Batarya' => '80V / 50Ah (opsiyon 80V / 100Ah)',
                'Batarya Ağırlığı' => '65 kg',
                'Yönlendirme (Direksiyon) Tasarımı' => 'Hidrolik',
                'Sürücü Kulağında Ses Seviyesi' => '68 dB(A)',
                'Bacak Payı (Diz Mesafesi)' => '464 mm'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '80V Li-Ion batarya ve entegre şarj ile fırsat şarjı, bakım gerektirmez.'],
                ['icon' => 'arrows-alt', 'text' => 'Üç tekerli, çift sürüş motorlu tasarım ile dar alanlarda noktasal dönüş kabiliyeti.'],
                ['icon' => 'bolt', 'text' => 'PMSM teknolojisi ile %10-15 enerji tasarrufu ve uzatılmış çalışma süresi.'],
                ['icon' => 'shield-alt', 'text' => 'Otomatik park freni ve dönüş hız kontrolü ile güvenli kullanım.'],
                ['icon' => 'warehouse', 'text' => 'h6<2000 mm üst koruma yüksekliği sayesinde asma kat ve yük asansörlerinde çalışma.'],
                ['icon' => 'industry', 'text' => 'Kompakt gövde ve 1422 mm dönüş yarıçapı ile yoğun depo senaryoları.'],
                ['icon' => 'cog', 'text' => 'Düşük servis ağırlığı (<2 ton) ile kolay nakliye ve katlar arası kullanım.'],
                ['icon' => 'star', 'text' => '464 mm geniş diz mesafesi ve kolçaklı koltuk ile konfor.']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
