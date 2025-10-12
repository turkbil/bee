<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES16_RSi_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ES16-RSi';

        $titleTr = 'İXTİF ES16 RSi - 2.0 Ton Elektrikli İstif Makinesi (Ayakta Kullanım)';
        $shortTr = 'İXTİF ES16 RSi, 2000 kg toplam yük kapasitesi, 1600 kg mast kaldırma kapasitesi, 600 mm yük merkezi ve 24V 280Ah akü ile dar koridorlarda hızlı ve güvenli istifleme sunar. 5,5/6 km/s seyir hızı, elektromanyetik fren ve elektronik yönlendirme ile operatör verimliliğini artırır.';

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
                'Model' => 'ES16 RSi',
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Ayakta kullanım (platformlu)',
                'Kapasite (Q)' => '2000 kg',
                'Mast ile kaldırma kapasitesi' => '1600 kg',
                'Destek kolları ile kaldırma kapasitesi' => '2000 kg',
                'Yük Merkezi (c)' => '600 mm',
                'Tahrik aksına mesafe (x)' => '710 mm',
                'Aks Mesafesi (y)' => '1460 mm',
                'Servis Ağırlığı' => '1335 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Tahrik Tekerlek Ölçüsü (ön)' => 'Ø230×75 mm',
                'Yük Tekerlek Ölçüsü (arka)' => 'Ø85×70 mm',
                'Denge Tekerlekleri' => 'Ø130×55 mm',
                'Tekerlek Dizilimi (ön/arka)' => '1x+1/4',
                'Ön İz Genişliği (b10)' => '574 mm',
                'Arka İz Genişliği (b11)' => '366 mm',
                'Maks. Kaldırma Yüksekliği (H3)' => '3000 mm (standart varyant)',
                'Direk Kapalı Yüksekliği (h1)' => '2020 mm',
                'Serbest Kaldırma (h2)' => '100 mm',
                'Direk Açık Yüksekliği (h4)' => '3460 mm',
                'İlk Kaldırma (h13)' => '120 mm',
                'Tiller Yükseklik (min/max)' => '1150 / 1480 mm',
                'Alt Yükseklik (h13 taban)' => '93 mm',
                'Toplam Uzunluk (l1)' => '2195 mm',
                'Çatal Ucuna Kadar Uzunluk (l2)' => '957 mm',
                'Toplam Genişlik (b1/b2)' => '850 mm',
                'Çatal Ölçüsü (s×e×l)' => '60 × 190 × 1150 mm',
                'Fork Taşıyıcı Genişliği' => '800 mm',
                'Çatallar Arası Mesafe' => '560 mm',
                'Şasi Altı Yer Açıklığı (mast/orta)' => '20 / 20 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2665 / 3092 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2597 / 3024 mm',
                'Dönüş Yarıçapı (Wa)' => '1765 / 2192 mm',
                'Seyir Hızı (yüklü/boş)' => '5.5 / 6 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.11 / 0.16 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.14 / 0.12 m/s',
                'Tırmanma Kabiliyeti (yüklü/boş)' => '8% / 16%',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru' => '2.5 kW (S2 60 dk)',
                'Kaldırma Motoru' => '3.0 kW (S3 15%)',
                'Batarya' => '24V 280Ah kurşun-asit (opsiyon 24V 205Ah Li-ion)',
                'Batarya Ağırlığı' => '270 kg',
                'Tahrik Kontrolü' => 'AC',
                'Direksiyon' => 'Elektronik',
                'Ses Seviyesi (operatör kulağı)' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V 280Ah akü (Li-ion 205Ah opsiyon) ile uzun vardiya kullanılabilirlik'],
                ['icon' => 'bolt', 'text' => 'AC tahrik kontrolü ile akıcı hızlanma ve hassas manevra'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis freni ve viraj yavaşlatma fonksiyonu'],
                ['icon' => 'arrows-alt', 'text' => '1765 mm dönüş yarıçapı ile dar koridorlarda yüksek çeviklik'],
                ['icon' => 'cart-shopping', 'text' => 'Destek kolu ilk kaldırma (120 mm) ile çift kat taşıma'],
                ['icon' => 'cog', 'text' => 'Elektronik direksiyon ile düşük efor, yüksek kontrol'],
                ['icon' => 'warehouse', 'text' => '3000 mm standart kaldırma yüksekliği, farklı direk seçenekleri'],
                ['icon' => 'star', 'text' => 'PU tekerleklerle sessiz ve titreşimsiz sürüş']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
