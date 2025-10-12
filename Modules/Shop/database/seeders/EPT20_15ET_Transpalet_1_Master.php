<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_15ET_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // Transpalet
        $brandId = 1; // İXTİF
        $sku = 'EPT20-15ET';
        $titleTr = 'İXTİF EPT20-15ET - 1.5 Ton Elektrikli Transpalet';

        $shortTr = 'İXTİF EPT20-15ET; 1.5 ton kapasiteli, kompakt gövdede DC sürüş kontrollü, PU tekerlekli ve elektromanyetik frenli bir elektrikli transpalettir. 2x12V/85Ah akü ve 24V-15A dahili şarj ile 4/4.5 km/sa hızlara ulaşır, 1485 mm dönüş yarıçapıyla dar koridorlarda çeviktir.';

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
                'Model' => 'EPT20-15ET',
                'Sürüş' => 'Elektrikli (Yaya kumandalı)',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik aksına kadar yük mesafesi (x)' => '883 / 946 mm',
                'Dingil mesafesi (y)' => '1228 / 1268 mm',
                'Servis Ağırlığı' => '210 kg',
                'Dingil yükü, yüklü (ön/arka)' => '574 / 1131 kg',
                'Dingil yükü, yüksüz (ön/arka)' => '165 / 40 kg',
                'Tekerlek tipi' => 'Poliüretan (PU)',
                'Tahrik tekeri ölçüsü (ön)' => 'Ø210×70 mm',
                'Yük tekeri ölçüsü (arka)' => 'Ø80×61 ve Ø74×88 mm (tandem)',
                'Ek tekerlek (caster)' => '—',
                'Tekerlek adedi (ön/arka)' => '1x / 4',
                'Arka iz genişliği (b11)' => '410 / 535 mm',
                'Kaldırma yüksekliği (h3)' => '115 mm',
                'Sürüş kolu yükseklik aralığı (h14)' => '750 / 1170 mm',
                'Alçaltılmış yükseklik (h13)' => '85 / 75 mm',
                'Toplam uzunluk (l1)' => '1632 mm',
                'Forka kadar uzunluk (l2)' => '482 mm',
                'Toplam genişlik (b1/b2)' => '560 / 685 mm',
                'Çatal ölçüsü (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatal açıklığı (b5)' => '560 / 685 mm',
                'Şase altı açıklık (m2)' => '35 (25) mm',
                'Koridor genişliği 1000×1200 (Ast)' => '2290 mm',
                'Koridor genişliği 800×1200 (Ast)' => '2160 mm',
                'Dönüş yarıçapı (Wa)' => '1485 mm',
                'Yürüyüş hızı (yüklü/yüksüz)' => '4 / 4.5 km/sa',
                'Kaldırma hızı (yüklü/yüksüz)' => '0.022 / 0.025 m/sn',
                'İndirme hızı (yüklü/yüksüz)' => '0.034 / 0.023 m/sn',
                'Tırmanma kabiliyeti (yüklü/yüksüz)' => '%%5 / %%16',
                'Servis freni' => 'Elektromanyetik',
                'Sürüş motoru gücü (S2 60 dk)' => '0.65 kW',
                'Kaldırma motoru (S3 15%)' => '0.84 kW',
                'Akü (voltaj/kapasite)' => '2×12V / 85Ah (24V toplam)',
                'Akü ağırlığı' => '25.5×2 kg',
                'Sürüş kontrol' => 'DC',
                'Direksiyon tasarımı' => 'Mekanik',
                'Operatör kulak seviyesi' => '74 dB(A)',
                'Standart çatal uzunluğu seçenekleri' => '1000 / 1150 / 1220 mm',
                'Standart çatal genişliği seçenekleri' => '560 / 685 mm',
                'Standart teker' => 'Çift yük tekeri, PU malzeme',
                'Şarj cihazı' => '24V-15A dahili',
                'Batarya göstergesi' => 'Zaman sayaçsız (opsiyon: zamanlı)',
                'Opsiyonlar' => 'İz bırakmayan tahrik tekeri, yardımcı caster tekeri, dikey taşıma kolu modu, entegre tartı'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'weight-hanging', 'text' => '1.5 ton taşıma kapasitesi ile günlük palet akışında güvenilir performans'],
                ['icon' => 'battery-full', 'text' => '2×12V/85Ah akü ve 24V-15A dahili şarj ile pratik enerji yönetimi'],
                ['icon' => 'gauge', 'text' => '4/4.5 km/sa yürüyüş hızı, vardiya içi hızlı transfer'],
                ['icon' => 'arrows-alt', 'text' => '1485 mm dönüş yarıçapı ile dar koridorlarda çeviklik'],
                ['icon' => 'circle-notch', 'text' => 'PU tahrik ve çift yük tekerleri ile sessiz ve titreşimsiz sürüş'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ile güvenli duruş'],
                ['icon' => 'cog', 'text' => 'Mekanik direksiyon ve DC kontrol ile düşük bakım maliyeti'],
                ['icon' => 'layer-group', 'text' => '560/685 mm çatal genişliği ve 1000/1150/1220 mm uzunluk seçenekleri']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
