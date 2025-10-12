<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX1_HD_Siparis_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 4; // Sipariş Toplama
        $brandId = 1;    // İXTİF
        $sku = 'JX1-HD';
        $titleTr = 'İXTİF JX1-HD - 1200 lb Süper Görev Sipariş Toplama Aracı';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'JX1-HD; 48V güç sistemi, 210” operatör yükseltme, 6.5 mph yol hızı ve 3,600 lb çekme kapasitesiyle iç mekân, düz-zemin sipariş toplama için tasarlanmış süper görev destek aracıdır. LED farlar ve elektronik direksiyon ile güvenli, verimli operasyon sunar.'], JSON_UNESCAPED_UNICODE),
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

            // PDF TEKNİK VERİLERİ
            'technical_specs' => json_encode([
                'Toplam Kapasite (Q)' => '1200 lb',
                'Operatör Bölmesi (Q1)' => '300 lb',
                'Ön Çalışma Platformu (Q2)' => '700 lb',
                'Arka Tepsi (Q3)' => '200 lb',
                'Tahrik' => 'Elektrik (AC sürüş kontrol)',
                'Tekerlek Tipi' => 'Poly',
                'Ön Lastik Ölçüsü' => '10.2” x 4.9”',
                'Arka Lastik Ölçüsü' => '8” x 3”',
                'Ek Tekerlekler' => '3” x 2” (caster)',
                'Aks Yükü (yüklü ön/arka)' => '1719.6 / 2689.6 lb',
                'Aks Yükü (yüksüz ön/arka)' => '1190.5 / 2182.6 lb',
                'Dingil Mesafesi (y)' => '51.2 in',
                'Servis Ağırlığı' => '3594 lb',
                'Kapalı Yükseklik (h6/h7)' => '88.6 in',
                'Kaldırma Yüksekliği (h3)' => '248.8 in',
                'Direk Tam Açık Yükseklik (h4)' => '288.8 in',
                'Operatör Stand Yüksekliği (yükseltilmiş, h12)' => '210 in',
                'Toplam Uzunluk (l1)' => '66.3 in',
                'Toplam Genişlik (b1/b2)' => '36 in',
                'Dönüş Yarıçapı (Wa)' => '63 in',
                'Zemin Yüksekliği (orta)' => '2 in',
                'Yol Hızı (yüklü/boş)' => '6.5 mph',
                'Kaldırma Hızı (yüklü/boş)' => '39.4 / 55.1 fpm',
                'İndirme Hızı (yüklü/boş)' => '68.9 / 59.1 fpm',
                'Maks. Eğim Kabiliyeti' => '0% (iç mekân düz zemin)',
                'Servis Freni' => 'Rejeneratif, elektromanyetik park',
                'Sürüş Motoru (S2 60dk)' => '4 kW',
                'Kaldırma Motoru (S3 15%)' => '4 kW',
                'Batarya Seçenekleri' => '48V/360Ah Li-ion veya 48V/210Ah Kurşun-asit',
                'Batarya Ağırlığı' => '700 lb',
                'Şarj Cihazı Akımı' => '30 / 50 / 60 / 160 A',
                'Kullanım' => 'Sadece iç mekân, düz ve pürüzsüz zeminler'
            ], JSON_UNESCAPED_UNICODE),

            // ÖNE ÇIKAN ÖZELLİKLER (8 madde)
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '48V güç sistemi ile artırılmış kaldırma/indirme ve seyir performansı'],
                ['icon' => 'arrows-alt', 'text' => '210” operatör yükseltme ile yüksek raflara güvenli erişim'],
                ['icon' => 'gauge', 'text' => '6.5 mph yol hızıyla hızlı sipariş toplama'],
                ['icon' => 'bolt', 'text' => '3,600 lb çekme kapasitesi ile malzeme besleme ve römork çekimi'],
                ['icon' => 'star', 'text' => 'İleri yön LED farlar ile aydınlık ve güvenli görüş'],
                ['icon' => 'shield-alt', 'text' => 'Rejeneratif sürüş ve elektromanyetik park freni'],
                ['icon' => 'cog', 'text' => 'Elektronik direksiyon ve AC tahrik ile hassas kontrol'],
                ['icon' => 'building', 'text' => 'İç mekân, düz zemin operasyonları için optimize edilmiş şasi']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
