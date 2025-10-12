<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EST122_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'EST122';
        $titleTr = 'İXTİF EST122 - 1.2 Ton Yaya Tipi Elektrikli İstifleyici';
        $shortTr = 'İXTİF EST122; 1.200 kg kapasite, 600 mm yük merkezi ve 24V 85Ah aküyle kompakt alanlarda güvenli istifleme sunar. 1856 mm kapalı direk, 2430 mm kaldırma, 792 mm gövde genişliği ve 1458 mm dönüş yarıçapı ile dar koridorlarda çeviktir; entegre şarj ve kaplumbağa modu ile verimlidir.';

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
                'Model' => 'EST122',
                'Kapasite (Q)' => '1200 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Servis Ağırlığı' => '595 kg',
                'Yük mesafesi (x)' => '798 mm',
                'Dingil mesafesi (y)' => '1212 mm',
                'Teker tipleri' => 'Poliüretan',
                'Ön teker ölçüsü' => 'Ø210×70 mm',
                'Arka teker ölçüsü' => 'Ø74×72 mm',
                'Destek tekeri' => 'Ø130×55 mm',
                'Teker sayısı (x=çekiş)' => '1x 1/4',
                'İz genişliği ön (b10)' => '531 mm',
                'İz genişliği arka (b11)' => '405 mm',
                'Kapalı direk yüksekliği (h1)' => '1856 mm',
                'Kaldırma yüksekliği (h3)' => '2430 mm',
                'Açık direk yüksekliği (h4)' => '3071 mm',
                'Tiller yüksekliği sürüş min./maks. (h14)' => '760 / 1140 mm',
                'Alçaltılmış çatallar (h13)' => '85 mm',
                'Toplam uzunluk (l1)' => '1713 mm',
                'Yüze kadar uzunluk (l2)' => '563 mm',
                'Toplam genişlik (b1/b2)' => '792 mm',
                'Çatal ölçüleri (s/e/l)' => '60 / 170 / 1150 mm',
                'Taşıyıcı genişliği (b3)' => '680 mm',
                'Çatallar arası mesafe (b5)' => '570 mm (opsiyon 685 mm)',
                'Koridor genişliği 1000×1200 (Ast)' => '2290 mm',
                'Koridor genişliği 800×1200 (Ast)' => '2225 mm',
                'Dönüş yarıçapı (Wa)' => '1458 mm',
                'Sürüş hızı yüklü/boş' => '4.2 / 4.5 km/s',
                'Kaldırma hızı yüklü/boş' => '0.10 / 0.14 m/s',
                'İndirme hızı yüklü/boş' => '0.10 / 0.10 m/s',
                'Maks. eğim kabiliyeti yüklü/boş' => '3% / 10%',
                'Fren' => 'Elektromanyetik',
                'Çekiş motoru (S2 60dk)' => '0.75 kW DC',
                'Pompa motoru (S3 15%)' => '2.2 kW DC',
                'Batarya gerilimi/kapasite' => '24V (2×12V) / 85Ah (opsiyon 105Ah)',
                'Batarya ağırlığı' => '2×24 kg',
                'Sürücü kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Ses seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'layer-group', 'text' => 'Kompakt çift kademeli (duplex) direk ile hafif hizmet istif uygulamaları'],
                ['icon' => 'arrows-alt', 'text' => 'Yanal tahrik ve rijit şasi ile yük altında stabilite'],
                ['icon' => 'bolt', 'text' => 'Endüstride kanıtlanmış bileşenlerle sade tasarım ve yüksek güvenilirlik'],
                ['icon' => 'hand', 'text' => 'Uzatılmış ve offset tiller kolu ile dar alanda üstün görüş'],
                ['icon' => 'gauge', 'text' => 'Kaplumbağa (crawl) butonu ile düşük hızda hassas manevra'],
                ['icon' => 'battery-full', 'text' => 'Entegre şarj cihazı sayesinde kolay şarj ve esnek vardiya'],
                ['icon' => 'cog', 'text' => 'Verimli hidrolik pompa ile düşük ses ve hızlı kaldırma'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve sağlam mast kiriş yapısıyla güvenlik']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
