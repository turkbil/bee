<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL603_HV_6_Forklift_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EFL603-HV-6';
        $titleTr = 'İXTİF EFL603 HV - 6.0 Ton Yüksek Voltaj Li-Ion Forklift';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '309V 228Ah LFP batarya ve PMSM tahrik ile 6.0 ton kapasite, 600 mm yük merkezi, 25/26 km/s seyir hızı ve %30/%34 eğim performansı. Su/yağ soğutmalı sistemler, IPX4/ IP67 koruma ve hızlı şarj (1C, ~1-1.2 saat) ile çok vardiyalı ağır hizmete hazır.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 1,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'base_price' => 0.00,
            'price_on_request' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode([
                'Kapasite (Q)' => '6000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Sürüş' => 'Elektrik (Li-ion, yüksek voltaj)',
                'Operatör Tipi' => 'Oturmalı',
                'Servis Ağırlığı' => '9250 kg',
                'Tahrik Aksına Uzaklık (x)' => '603.5 mm',
                'Dingil Mesafesi (y)' => '2300 mm',
                'Lastik Tipi' => 'Pnömatik',
                'Ön Lastik Ölçüsü' => '8.25-15-14PR',
                'Arka Lastik Ölçüsü' => '8.25-15-14PR',
                'Ön/Arka Teker Sayısı' => '4x / 2',
                'Ön İz Genişliği (b10)' => '1498 mm',
                'Arka İz Genişliği (b11)' => '1718 mm',
                'Direk İleri/Geri Eğimi (α/β)' => '6° / 12°',
                'Kapalı Direk Yüksekliği (h1)' => '2480 mm',
                'Serbest Kaldırma (h2)' => '160 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Direk Açık Yüksekliği (h4)' => '4470/3965 mm',
                'Üst Koruma (h6)' => '2590 mm',
                'Koltuk Yüksekliği (h7)' => '1490 mm',
                'Çeki Kancası Yüksekliği (h10)' => '600 mm',
                'Toplam Uzunluk (l1)' => '4720 mm',
                'Yüke Kadar Uzunluk (l2)' => '3500 mm',
                'Toplam Genişlik (b1/b2)' => '2028 mm',
                'Çatal Ölçüsü (s/e/l)' => '60 × 150 × 1220 mm',
                'Çatal Taşıyıcı Sınıfı' => '4A',
                'Çatal Taşıyıcı Genişliği (b3)' => '1845 mm',
                'Yer Açıklığı Mast Altı (m1)' => '160 mm',
                'Dingil Merkezinde Yer Açıklığı (m2)' => '265 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '5260 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '5260 mm',
                'Dönüş Yarıçapı (Wa)' => '3235 mm',
                'Seyir Hızı (Yüklü/Boş)' => '25 / 26 km/s',
                'Kaldırma Hızı (Yüklü/Boş)' => '0.51 / 0.53 m/s',
                'İndirme Hızı (Yüklü/Boş)' => '0.48 / 0.42 m/s',
                'Maks. Tırmanma (Yüklü/Boş)' => '%30 / %34',
                'Fren (Servis / Park)' => 'Hidrolik / Mekanik',
                'Sürüş Motoru' => '60 kW PMSM',
                'Kaldırma Motoru' => '2 × 27.8 kW',
                'Akü Voltaj / Kapasite' => '309 V / 228 Ah (LFP)',
                'Akü Ağırlığı' => '693 kg',
                'Sürücü Kontrolü' => 'PMSM + VCU',
                'Koruma Sınıfı' => 'Araç genel IPX4, HV komponent IP67',
                'Hızlı Şarj' => '1C, tam dolum ~1–1.2 saat'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '309V 228Ah LFP yüksek voltajlı Li-ion batarya ile uzun çalışma süresi'],
                ['icon' => 'microchip', 'text' => '60 kW PMSM sürüş + VCU ile hassas kontrol ve hızlı tepki'],
                ['icon' => 'cog', 'text' => 'Motor ve batarya için çift su soğutma, hidrolik için yağ soğutma'],
                ['icon' => 'bolt', 'text' => '1C hızlı şarj; çok vardiyada minimum duruş süresi'],
                ['icon' => 'shield-alt', 'text' => 'Aşırı ısınma/kısa devre/overcharge korumaları ve hız uyarısı'],
                ['icon' => 'star', 'text' => 'Mastta hidrolik tamponlama ile sarsıntısız kaldırma/indirme'],
                ['icon' => 'arrows-alt', 'text' => '30–34 km/s aralıklarına kadar seyir – yüksek verimlilik'],
                ['icon' => 'plug', 'text' => 'Araç tipi şarj istasyonları ile uyumlu elektrik altyapısı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
