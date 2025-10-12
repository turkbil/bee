<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX2_4_SiparisToplama_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 4; // Sipariş Toplama
        $brandId = 1; // İXTİF
        $sku = 'JX2-4';
        $titleTr = 'İXTİF JX2-4 - 192” Kaldırma, 42” Çatal, 24V Sipariş Toplama';

        $shortTr = 'İXTİF JX2-4, dar koridorlarda güvenli sipariş toplama için tasarlanmış elektrikli platformdur. 192” maksimum çatal yüksekliği, 24V enerji sistemi, 4.5 mph seyir hızı ve 65” dönüş yarıçapıyla verimliliği artırır; AGM, lityum ve kurşun-asit batarya seçenekleri sunar.';

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
                'Model' => 'JX2-4',
                'Sürüş' => 'Elektrik (AC)',
                'Operatör Tipi' => 'Sipariş toplayıcı (ayakta)',
                'Kapasite (Yük/Operatör)' => '2,200 lb çatal, 300 lb operatör',
                'Yük Merkezi' => '24 in',
                'Yük mesafesi (x)' => '4.5 in',
                'Dingil mesafesi (y)' => '53.7 in',
                'Servis Ağırlığı' => '5,594 lb',
                'Lastik Tipi' => 'Poly (sürüş / yük / denge)',
                'Ön Lastik Ölçüsü' => '6.5 x 4.7 in (yük)',
                'Arka Lastik Ölçüsü' => '10.25 x 5 in (sürüş)',
                'Ek Tekerlekler' => '3 x 2 in (caster)',
                'Tekerlek adedi (ön/arka)' => '1x, 2/2',
                'İz genişliği (ön/arka)' => '30.3 in / 22.4 in',
                'Direk Kapalı Yükseklik' => '95.5 in',
                'Serbest Kaldırma' => '0 in',
                'Maks. Çatal Yüksekliği' => '192 in',
                'Mast Tam Yükselik (kabin ile)' => '295 in',
                'Operatör Bölmesi Yüksekliği' => '89.4 in',
                'Ayakta Yükseklik (yükseltilmiş)' => '198 in',
                'Alt Yükseklik' => '2.5 in',
                'Toplam Uzunluk' => '108.3 in',
                'Çatala Kadar Uzunluk' => '66.1 in',
                'Toplam Genişlik' => '36 in',
                'Çatal Ölçüleri (s/e/l)' => '1.6 / 3.9 / 42 in',
                'Çatal Aralığı' => '21.3 in',
                'Şasi Orta Noktası Yerden Yükseklik' => '2 in',
                'Sağ Açılı İstif Koridoru (48” palet)' => '117.5 in',
                'Dönüş Yarıçapı' => '65 in',
                'Seyir Hızı (yük/boş)' => '4.5 / 4.5 mph',
                'Kaldırma Hızı (yük/boş)' => '25.6 / 31.5 fpm',
                'İndirme Hızı (yük/boş)' => '31.5 / 35.4 fpm',
                'Azami Tırmanma (%)' => '0 % (iç mekân)',
                'Fren' => 'Rejeneratif servis, elektromanyetik park',
                'Sürüş Motoru (S2 60 dk)' => '4 kW',
                'Kaldırma Motoru (S3 15%)' => '3 kW',
                'Batarya Voltaj/Kapasite' => '24V / 340Ah (Kurşun-asit), 24V / 224Ah (AGM)',
                'Batarya Ağırlığı' => '700 lb',
                'Direksiyon' => 'Elektrik',
                'Gürültü Seviyesi' => '70 dB(A)',
                'Şarj Cihazı Akımı' => '35A, 40A',
                'Kullanım' => 'Sadece iç mekân; düz ve pürüzsüz zemin'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => '192” maksimum erişimle raf üstü sipariş toplama'],
                ['icon' => 'cart-shopping', 'text' => '42” çatal ve 21.3” aralıkla tipik 48” palete uyum'],
                ['icon' => 'battery-full', 'text' => '24V enerji sistemi; AGM, lityum ve kurşun-asit opsiyonları'],
                ['icon' => 'gauge', 'text' => 'Yükte/boşta 4.5 mph sabit seyir hızı'],
                ['icon' => 'shield-alt', 'text' => 'Rejeneratif servis freni ve elektromanyetik park'],
                ['icon' => 'industry', 'text' => 'Poly tekerlekler ile sessiz, temiz iç mekân operasyonu'],
                ['icon' => 'arrows-turn-right', 'text' => '65” dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'building', 'text' => 'Kabin içi konforlu stand alanı ve 89.4” bölüm yüksekliği']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master: {$sku}");
    }
}
