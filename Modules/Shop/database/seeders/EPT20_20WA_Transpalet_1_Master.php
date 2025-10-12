<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_20WA_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $sku = 'EPT20-20WA';
        $titleTr = 'İXTİF EPT20-20WA - 2.0 Ton Elektrikli Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2.0 ton kapasiteli, 600 mm yük merkezi ve 1.600 mm dönüş yarıçapı ile kısa mesafeli yoğun uygulamalar için ağır hizmet çözümü. 24V akü, AC tahrik, uzun tiller ve otomatik hız düşürme sayesinde güvenli ve çevik kullanım sunar.'], JSON_UNESCAPED_UNICODE),
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
                'Üretici' => 'İXTİF',
                'Model' => 'EPT20-20WA',
                'Sürüş' => 'Elektrik',
                'Operatör Tipi' => 'Yaya (Pedestrian)',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkez Mesafesi (c)' => '600 mm',
                'Tahrik Aksı - Çatal Ucu Mesafesi (x)' => '916 / 982 mm',
                'Dingil Mesafesi (y)' => '1418 mm',
                'Servis Ağırlığı' => '400 kg (Li-ion) / 530 kg (Kurşun-asit)',
                'Lastik Tipi' => 'Poliüretan',
                'Tekerlek Ölçüsü Ön' => 'Ø85×70 mm',
                'Tekerlek Ölçüsü Arka' => 'Ø230×75 mm',
                'Ek Teker (Caster)' => 'Ø85×48 mm',
                'Tekerlek Sayısı (ön/arka)' => '1x+2 / 4',
                'Ön İz Genişliği (b10)' => '498 mm',
                'Arka İz Genişliği (b11)' => '515 mm',
                'Kaldırma Yüksekliği (h3)' => '120 mm',
                'Sürüş Kolu Yüksekliği (h14)' => '715 / 1200 mm',
                'Alçaltılmış Yükseklik (h13)' => '85 mm',
                'Toplam Uzunluk (l1)' => '1748 mm',
                'Çatal Ucuna Kadar Uzunluk (l2)' => '621 mm',
                'Toplam Genişlik (b1/b2)' => '710 mm',
                'Çatal Ölçüleri (s/e/l)' => '55 × 170 × 1150 mm',
                'Çatallar Arası Mesafe (b5)' => '685 mm',
                'Zemin Açıklığı (m2)' => '30 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2400 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2255 mm',
                'Dönüş Yarıçapı (Wa)' => '1600 mm',
                'Sürüş Hızı (yüklü/boş)' => '5.0 / 5.5 km/s',
                'Kaldırma Hızı (yüklü/boş)' => '0.051 / 0.060 m/s',
                'İndirme Hızı (yüklü/boş)' => '0.032 / 0.039 m/s',
                'Azami % Eğim (yüklü/boş)' => '8 / 16 %',
                'Servis Freni' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60dk)' => '1.1 kW',
                'Kaldırma Motoru (S3 15%)' => '0.84 kW',
                'Akü Voltaj/Kapasite' => '24V / 205 Ah (Li-ion) | 24V / 210 Ah (Kurşun-asit)',
                'Akü Ağırlığı' => '62 kg (Li-ion) | 190 kg (Kurşun-asit)',
                'Sürüş Kontrol' => 'AC',
                'Direksiyon' => 'Mekanik',
                'Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '24V Li-Ion/kurşun-asit seçenekleri ile uzun vardiya uygunluğu'],
                ['icon' => 'bolt', 'text' => 'Dikey AC tahrik motoru ve entegre dişli kutusu'],
                ['icon' => 'arrows-alt', 'text' => '180° üstü tiller dönüş aralığı ile manevra kolaylığı'],
                ['icon' => 'shield-alt', 'text' => 'Tiller konumuna göre otomatik hız düşürme güvenliği'],
                ['icon' => 'weight-hanging', 'text' => '2.0 ton kapasite ile ağır hizmete uygun yapı'],
                ['icon' => 'cog', 'text' => 'Düşük bakım gereksinimi ve erişilebilir komponentler'],
                ['icon' => 'warehouse', 'text' => 'Küçük dönüş yarıçapı ile dar koridor uyumu'],
                ['icon' => 'check-circle', 'text' => 'CE uygunluğu ve elektromanyetik fren sistemi']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master: ' . $sku);
    }
}
