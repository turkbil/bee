<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFX4_301_Forklift_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 1; // Forklift
        $brandId = 1; // İXTİF
        $sku = 'EFX4-301';
        $titleTr = 'İXTİF EFX4 301 - 3.0 Ton Li-Ion Karşı Ağırlıklı Forklift (Manuel-Değişimli Modüler Akü)';

        $shortTr = '3.0 ton kapasiteli İXTİF EFX4 301; 80V/100Ah modüler, elle değiştirilebilir Li-Ion akü paketleri, PMS motor sürüş sistemi ve opsiyonel çoklu şarj istasyonuyla kesintisiz vardiya sağlar. 11/12 km/s hız, 2428 mm dönüş yarıçapı ve geniş görüşlü direk ile konfor ve verimliliği birleştirir.';

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
                'Sürücü' => 'Elektrik (Oturmalı)',
                'Kapasite (Q)' => '3000 kg',
                'Yük Merkezi Mesafesi (c)' => '500 mm',
                'Ön/Arka Dingil Yükü (yüklü)' => '6537 / 817 kg',
                'Ön/Arka Dingil Yükü (yüksüz)' => '1854 / 2696 kg',
                'Servis Ağırlığı' => '4354 kg',
                'Tekerlek Tipi' => 'Pnömatik',
                'Ön Lastik' => '28x9-15-14PR',
                'Arka Lastik' => '6.5F-10-10PR',
                'Tekerlek Adedi (x=sürücü)' => '2x / 2',
                'Ön İz Genişliği (b10)' => '989 mm',
                'Arka İz Genişliği (b11)' => '980 mm',
                'Direk Eğimi (α/β)' => '6° / 10°',
                'Alçalmış Direk Yüksekliği (h1)' => '2265 mm',
                'Serbest Kaldırma (h2)' => '135 mm',
                'Kaldırma Yüksekliği (h3)' => '3000 mm',
                'Yükselmiş Direk Yüksekliği (h4)' => '4096 mm',
                'Üst Koruyucu Yüksekliği (h6)' => '2143 mm',
                'Koltuk Yüksekliği (h7)' => '1141 mm',
                'Çeki Kancası Yüksekliği (h10)' => '468 mm',
                'Toplam Uzunluk (l1)' => '3735 mm',
                'Yüze Kadar Uzunluk (l2)' => '2665 mm',
                'Toplam Genişlik (b1/b2)' => '1228 mm',
                'Çatal Ölçüleri (s/e/l)' => '45 × 122 × 1070 mm',
                'Çatal Sınıfı' => '3A, Taşıyıcı Genişliği (b3): 1100 mm',
                'Şasi Altı Açıklık (m1/m2)' => '125 mm / 137 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '4115 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '4315 mm',
                'Dönüş Yarıçapı (Wa)' => '2428 mm',
                'Yürüyüş Hızı (yüklü/boş)' => '11 / 12 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.29 / 0.36 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.4 / 0.4 m/s',
                'Azami Eğim (yüklü/boş)' => '15% / 15%',
                'Servis Freni' => 'Hidrolik',
                'Park Freni' => 'Mekanik',
                'Sürüş Motoru (S2 60dk)' => '8 kW (PMSM)',
                'Kaldırma Motoru (S3 15%)' => '16 kW',
                'Akü' => '80V / 100Ah (3 modül seti), modüler elle değişim',
                'Akü Ağırlığı' => '220 kg',
                'Sürücü Kontrolü' => 'PMSM',
                'Direksiyon' => 'Hidrolik',
                'Sürücü Kulak Seviyesinde Ses' => '60 dB(A)',
                'Mast Opsiyonları' => '2700/3000/3300/3500 mm (ilgili h1/h2/h4 değerleri)'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => 'Elle değiştirilebilir hafif modüler Li-Ion akü paketleri (26 kg modül)'],
                ['icon' => 'bolt', 'text' => 'PMS senkron motor ile verimli güç ve düşük tüketim'],
                ['icon' => 'cog', 'text' => 'Akıllı BMS ile optimize pil ömrü ve kolay bakım'],
                ['icon' => 'plug', 'text' => 'Opsiyonel çoklu şarj istasyonu ile 6 bataryaya kadar eşzamanlı şarj'],
                ['icon' => 'shield-alt', 'text' => 'Sağlam ve suya dayanıklı tasarım ile zorlu sahalara uyum'],
                ['icon' => 'building', 'text' => 'Geniş görüşlü direk ve ferah kabin ile güvenli kullanım'],
                ['icon' => 'arrows-alt', 'text' => 'Modüler yapı ile ölçeklenebilir çalışma süresi (3→6 paket)'],
                ['icon' => 'star', 'text' => 'Uzaktan şarj imkânı ile sahada kesintisiz operasyon']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
