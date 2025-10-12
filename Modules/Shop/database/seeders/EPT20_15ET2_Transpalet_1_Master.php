<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_15ET2_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1; // İXTİF
        $sku = 'EPT20-15ET2';
        $titleTr = 'İXTİF EPT20-15ET2 - 1.5 Ton Elektrikli Transpalet';

        $shortTr = 'İXTİF EPT20-15ET2; 1.5 ton kapasiteli, 24V/65Ah Li-ion akülü, DC sürüş kontrollü ve PU tekerlekli bir yaya tipi elektrikli transpalettir. 4.5/5 km/sa hızlar, 1475 mm dönüş yarıçapı ve 30 mm şase açıklığıyla dar alanlarda çevik ve verimli çalışır.';

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
                'Üretici' => 'İXTİF',
                'Model' => 'EPT20-15ET2',
                'Sürüş' => 'Elektrikli (Yaya kumandalı)',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik aksına kadar yük mesafesi (x)' => '883 / 946 mm',
                'Dingil mesafesi (y)' => '1202 / 1261 mm',
                'Servis Ağırlığı' => '180 kg',
                'Dingil yükü, yüklü (ön/arka)' => '567 / 1128 kg',
                'Dingil yükü, yüksüz (ön/arka)' => '155 / 40 kg',
                'Tekerlek tipi' => 'Poliüretan (PU)',
                'Tahrik tekeri ölçüsü (ön)' => 'Ø210×70 mm',
                'Yük tekeri ölçüsü (arka)' => 'Ø80×61 mm',
                'Ek tekerlek (caster)' => '—',
                'Tekerlek adedi (ön/arka)' => '1x / 4',
                'Arka iz genişliği (b11)' => '410 / 535 mm',
                'Kaldırma yüksekliği (h3)' => '115 mm',
                'Sürüş kolu yükseklik aralığı (h14)' => '750 / 1170 mm',
                'Alçaltılmış yükseklik (h13)' => '80 mm',
                'Toplam uzunluk (l1)' => '1638 mm',
                'Forka kadar uzunluk (l2)' => '488 mm',
                'Toplam genişlik (b1/b2)' => '560 / 685 mm',
                'Çatal ölçüsü (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatal açıklığı (b5)' => '560 / 685 mm',
                'Şase altı açıklık (m2)' => '30 mm',
                'Koridor genişliği 1000×1200 (Ast)' => '2280 mm',
                'Koridor genişliği 800×1200 (Ast)' => '2150 mm',
                'Dönüş yarıçapı (Wa)' => '1475 mm',
                'Yürüyüş hızı (yüklü/yüksüz)' => '4.5 / 5 km/sa',
                'Kaldırma hızı (yüklü/yüksüz)' => '0.027 / 0.038 m/sn',
                'İndirme hızı (yüklü/yüksüz)' => '0.059 / 0.039 m/sn',
                'Tırmanma kabiliyeti (yüklü/yüksüz)' => '%%5 / %%16',
                'Servis freni' => 'Elektromanyetik',
                'Sürüş motoru gücü (S2 60 dk)' => '0.75 kW',
                'Kaldırma motoru (S3 15%)' => '0.84 kW',
                'Akü (voltaj/kapasite)' => '24V / 65Ah Li-ion',
                'Akü ağırlığı' => '14.15×2 kg',
                'Sürüş kontrol' => 'DC',
                'Direksiyon tasarımı' => 'Mekanik',
                'Operatör kulak seviyesi' => '74 dB(A)',
                'Standart çatal uzunluğu seçenekleri' => '800–2000 mm (1150 mm standart)',
                'Standart çatal genişliği seçenekleri' => '560 (standart), 685/520/460/420/600 (opsiyon)',
                'Standart teker' => 'Çift yük tekeri, PU malzeme',
                'Şarj cihazı' => '24V-10A dahili',
                'Batarya göstergesi' => 'Zamanlı',
                'Opsiyonlar' => 'Trace PU tahrik, tek yük tekeri, 75 mm kısa sürüş kolu, dikey kol modu, elektrikli tartı, depolama kutusu, kargo sırtlığı, caster teker'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/65Ah Li-ion enerji ile uzun vardiya verimi'],
                ['icon' => 'arrows-alt', 'text' => '1475 mm dönüş yarıçapıyla dar koridor uyumu'],
                ['icon' => 'bolt', 'text' => '0.75 kW sürüş ve 0.84 kW kaldırma ile dengeli güç'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik frenle güvenli duruş'],
                ['icon' => 'industry', 'text' => 'Mekanik direksiyonla düşük bakım maliyeti'],
                ['icon' => 'cog', 'text' => 'DC sürüş kontrolünde sade mimari'],
                ['icon' => 'plug', 'text' => '24V-10A dahili şarj ile ara şarj kolaylığı'],
                ['icon' => 'cart-shopping', 'text' => 'PU tekerlerle sessiz ve akıcı taşıma']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
