<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFXZ_251_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFXZ-251';
        $titleTr = 'İXTİF EFXZ 251 - 2.5 Ton Li-Ion Denge Ağırlıklı Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'EFXZ 251, içten yanmalı gövdeden dönüştürülen ve 80V Li-Ion batarya ile yeniden üretilen 2.5 ton sınıfı denge ağırlıklı forklifte yeni bir ekonomik çağ açar: 3900 kg servis ağırlığı, 3000 mm kaldırma, 11/12 km/s hız ve fırsat şarjı ile düşük işletme maliyeti.'], JSON_UNESCAPED_UNICODE),
            'category_id' => $categoryId,
            'brand_id' => $brandId,
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
                'Model' => 'EFXZ 251',
                'Kapasite' => '2500 kg',
                'Yük Merkez Mesafesi (c)' => '500 mm',
                'Dingil Mesafesi (y)' => '1595 mm',
                'Servis Ağırlığı' => '3900 kg',
                'Aks Yükü (Yüklü Ön/Arka)' => '5520 / 880 kg',
                'Aks Yükü (Boş Ön/Arka)' => '1460 / 2440 kg',
                'Lastik Tipi' => 'Pnömatik',
                'Ön Lastik' => '7.00-12-12PR',
                'Arka Lastik' => '18x7-8-14PR',
                'Direk Eğimi (İleri/Geri)' => '6° / 10°',
                'Direk Yüksekliği (Katlı h1)' => '2090 mm',
                'Serbest Kaldırma (h2)' => '120 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Yüksekliği (Açık h4)' => '4025 mm',
                'Üst Koruma Yüksekliği (h6)' => '2165 mm',
                'Sürücü Koltuğu Yüksekliği (h7)' => '1095 mm',
                'Çeki Kancası Yüksekliği' => '300 mm',
                'Toplam Uzunluk (l1)' => '3662 mm',
                'Forka Kadar Uzunluk (l2)' => '2592 mm',
                'Toplam Genişlik (b1/b2)' => '1154 mm',
                'Çatal Ölçüleri (s×e×l)' => '40 × 122 × 1070 mm',
                'Çatal Taşıyıcı Sınıfı' => '2A / Genişlik 1040 mm',
                'Şasi Altı Açıklık (yüklü, mast altı m1)' => '115 mm',
                'Dingil Orta Açıklığı (m2)' => '150 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '4011 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '4211 mm',
                'Dönüş Yarıçapı (Wa)' => '2316 mm',
                'Seyir Hızı (Yüklü/Boş)' => '11 / 12 km/s',
                'Kaldırma Hızı (Yüklü/Boş)' => '0.29 / 0.36 m/s',
                'İndirme Hızı (Yüklü/Boş)' => '0.45 / 0.50 m/s',
                'Azami Tırmanma (Yüklü/Boş)' => '15% / 20%',
                'Hizmet Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru Anma' => '8 kW (S2 60 dk)',
                'Kaldırma Motoru Anma' => '16 kW (S3 15%)',
                'Batarya' => '80V 150Ah Li-Ion',
                'Tahrik Tipi' => 'PMSM (Senkron)',
                'Direksiyon' => 'Hidrolik',
                'Sürücü Kulağında Ses' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '80V Li-Ion teknoloji ile hızlı ve fırsat şarjı, bakım gerektirmeyen enerji sistemi'],
                ['icon' => 'bolt', 'text' => 'IC gövde dayanımı ile elektrikli tahrikin verimliliğini birleştiren hibrit mimari'],
                ['icon' => 'shield-alt', 'text' => 'Yük testleri, fren ve güvenlik sistem kontrolleri ile sıkı kalite süreçleri'],
                ['icon' => 'star', 'text' => 'Kumlama ve boyama sonrası yenilenmiş görünüm, uzun ömür'],
                ['icon' => 'arrows-alt', 'text' => '2316 mm dönüş yarıçapı ile raf arası manevra kabiliyeti'],
                ['icon' => 'cart-shopping', 'text' => '11/12 km/s sürat ile akıcı iç lojistik akışları'],
                ['icon' => 'award', 'text' => 'Yeni eşdeğer garanti standartları ile güvence'],
                ['icon' => 'plug', 'text' => 'Kolay şarj erişimi ve vardiya içi enerji yönetimi']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master oluşturuldu: ' . $sku);
    }
}
