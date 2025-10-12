<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPL153_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EPL153';
        $titleTr = 'İXTİF EPL153 - 1.5 Ton Li-Ion Elektrikli Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'EPL153, 1500 kg kapasiteli, 600 mm yük merkezi ile kompakt gövdede (l2=400 mm) 120 kg servis ağırlığı sunar. 24V 20Ah çıkarılabilir Li-Ion batarya, 4.5/5.0 km/s sürüş hızları ve 1330 mm dönüş yarıçapı ile vardiya içi hafif-orta yoğunluk operasyonlarda verimli ve güvenlidir.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 2,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode([
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Yürüyen (Pedestrian)',
                'Kapasite' => '1500 kg',
                'Yük Merkezi' => '600 mm',
                'Servis Ağırlığı' => '120 kg',
                'Tekerlek Tipi' => 'Poliüretan',
                'Tahrik Teker Boyutu' => 'Ø210×70 mm',
                'Yük Teker Boyutu' => 'Ø80×60 mm (Ø74×88 mm opsiyon)',
                'Teker Düzeni (ön/arka)' => '1x/4 (1x/2)',
                'Kaldırma Yüksekliği (h3)' => '115 mm',
                'Tiller Yüksekliği min./maks. (h14)' => '750/1190 mm',
                'İnmiş Çatal Yüksekliği (h13)' => '80 mm',
                'Toplam Uzunluk (l1)' => '1540 mm',
                'Yüke Kadar Uzunluk (l2)' => '400 mm',
                'Toplam Genişlik (b1/b2)' => '560 mm (685 mm opsiyon)',
                'Çatal Ölçüleri (s/e/l)' => '50×150×1150 mm',
                'Çatal Arası Mesafe (b5)' => '560 mm (685 mm opsiyon)',
                'Dönüş Yarıçapı (Wa)' => '1330 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2145 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2050 mm',
                'Sürüş Hızı yüklü/boş' => '4.5 / 5.0 km/s',
                'Kaldırma Hızı yüklü/boş' => '0.017 / 0.020 m/s',
                'İndirme Hızı yüklü/boş' => '0.09 / 0.06 m/s',
                'Tırmanma Kabiliyeti yüklü/boş' => '6% / 16%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '0.7 kW',
                'Batarya' => '24V / 20Ah Li-Ion, çıkarılabilir',
                'Batarya Ağırlığı' => '7 kg',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '<74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V 20Ah çıkarılabilir Li-Ion batarya ile harici şarj imkânı'],
                ['icon' => 'bolt', 'text' => '4.5/5.0 km/s sürüş hızları ile akıcı malzeme akışı'],
                ['icon' => 'arrows-alt', 'text' => '1330 mm dönüş yarıçapı ile dar alan manevrası'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ile güvenli duruş'],
                ['icon' => 'cog', 'text' => 'Basit tasarım ve 10 yıl pazarda kanıtlanmış bileşenler'],
                ['icon' => 'cart-shopping', 'text' => '1150 mm çatal standart, farklı çatal uzunluk seçenekleri'],
                ['icon' => 'industry', 'text' => '600 mm yük merkezinde 1500 kg taşıma kapasitesi'],
                ['icon' => 'star', 'text' => 'İncele kontrollü manuel indirme ile hassas yük koruma']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
