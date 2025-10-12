<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESR151_Istif_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 3; // İstif Makinesi
        $brandId = 1; // İXTİF
        $sku = 'ESR151';
        $titleTr = 'İXTİF ESR151 - 1.5 Ton Sürücülü İstif Makinesi';

        $shortTr = 'İXTİF ESR151; 1.5 ton kapasiteli, 500 mm yük merkezi ile dar koridorlarda çevik çalışmayı sağlayan sürücülü istif makinesidir. 24V/15A entegre şarj cihazı, 2x12/105Ah akü, 4.0/4.5 km/s hız, 2.2 kW kaldırma motoru ve katlanır platform ile güvenli, konforlu ve verimli kullanım sunar.';

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
                'Model' => 'ESR151',
                'Sürüş' => 'Elektrikli',
                'Operatör Tipi' => 'Ayakta / Sürüş platformlu',
                'Kapasite (Q)' => '1500 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Tahrik Aksına Uzaklık (x)' => '798 mm',
                'Dingil Mesafesi (y)' => '1215 mm',
                'Servis Ağırlığı' => '670 kg',
                'Aks Yükü (yükle, ön/arka)' => '870 / 1300 kg',
                'Aks Yükü (yüksüz, ön/arka)' => '510 / 160 kg',
                'Lastik Tipi' => 'Poliüretan',
                'Tahrik Teker Boyutu (ön)' => 'Ø210×70 mm',
                'Yük Teker Boyutu (arka)' => 'Ø74×72 mm',
                'Kastor Teker' => 'Ø130×55 mm',
                'Tekerlek Adedi (ön/arka)' => '1x 2 / 4',
                'Ön İz Genişliği (b10)' => '644 mm',
                'Arka İz Genişliği (b11)' => '400 mm',
                'Maks. Kaldırma Yüksekliği (H)' => '3016 mm',
                'Direk Kapalı Yüksekliği (h1)' => '2106 mm',
                'Serbest Kaldırma (h2)' => '—',
                'Kaldırma Yüksekliği (h3)' => '2930 mm',
                'Direk Açık Yüksekliği (h4)' => '3571 mm',
                'Kumanda Kolu Yüksekliği (h14)' => '1120 / 1315 mm',
                'Alçaltılmış Çatal Yüksekliği (h13)' => '90 mm',
                'Toplam Uzunluk (l1)' => '1832 mm',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '682 mm',
                'Toplam Genişlik (b1/b2)' => '850 mm',
                'Çatal Ölçüleri (s/e/l)' => '60 / 170 / 1150 mm',
                'Fork Carriage Genişliği (b3)' => '680 mm',
                'Forklar Arası Mesafe (b5)' => '570 mm',
                'Şasi Altı Yerden Yükseklik (m2)' => '25 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2328 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2262 mm',
                'Dönüş Yarıçapı (Wa)' => '1488 mm',
                'Sürüş Hızı (yükle/yüksüz)' => '4.0 / 4.5 km/s',
                'Kaldırma Hızı (yükle/yüksüz)' => '0.10 / 0.14 m/s',
                'İndirme Hızı (yükle/yüksüz)' => '0.10 / 0.10 m/s',
                'Tırmanma Kabiliyeti (yükle/yüksüz)' => '3% / 10%',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '0.75 kW',
                'Kaldırma Motoru (S3 15%)' => '2.2 kW',
                'Batarya' => '2×12V / 105Ah (24V toplam)',
                'Batarya Ağırlığı' => '2×30.5 kg',
                'Sürüş Kontrolü' => 'DC',
                'Direksiyon' => 'Mekanik',
                'Ses Seviyesi' => '74 dB(A)',
                'Entegre Şarj Cihazı' => '24V / 15A',
                'Direk Seçenekleri' => '2516 / 2716 / 3016 / 3316 mm (standart direk)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V/15A entegre şarj cihazı ile priz olan her yerde şarj kolaylığı'],
                ['icon' => 'shield-alt', 'text' => '720 mm üstünde otomatik düşük hız modu ve mast koruma filesi ile güvenlik'],
                ['icon' => 'star', 'text' => 'GB/T26949.1–2012 stabilite doğrulamasını geçen güvenilir şasi'],
                ['icon' => 'briefcase', 'text' => 'Katlanır sürüş pedalı ile uzun mesafede konfor, dar alanda kapatılabilir kullanım'],
                ['icon' => 'arrows-alt', 'text' => '1488 mm dönüş yarıçapıyla dar koridorlarda çevik manevra'],
                ['icon' => 'cart-shopping', 'text' => 'PU tekerler ile sessiz ve zemine zarar vermeyen çalışma'],
                ['icon' => 'bolt', 'text' => '2.2 kW kaldırma motoru ile dengeli hız ve verimlilik'],
                ['icon' => 'battery-full', 'text' => '2×12V/105Ah akü ile vardiya içi operasyonlarda süreklilik']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
