<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX2_1_Siparis_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 4; // Sipariş Toplama
        $brandId = 1; // İXTİF
        $sku = 'JX2-1';
        $titleTr = 'İXTİF JX2-1 - Düşük Seviye Sipariş Toplayıcı (72” Kaldırma, 42” Çatal)';

        $shortTr = 'İXTİF JX2-1, 72” kaldırma yüksekliği ve 42” çatalı ile dar koridorlarda güvenli, hızlı ve hassas sipariş toplama sağlar. 24V kurşun-asit 340Ah veya AGM 224Ah batarya seçenekleri, 5 mph sürüş ve 2.5 kW AC sürüş motoru ile verimli indoor operasyonlar sunar.';

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
                'Model' => 'JX2-1',
                'Sürüş' => 'Elektrik (AC)',
                'Operatör Tipi' => 'Sipariş Toplayıcı - Düşük Seviye',
                'Kapasite (Q)' => '2,200 lb (mini mast, 300 lb operatör)', // fileciteturn0file0
                'Yük Merkezi Mesafesi (c)' => '24 in',
                'Tahrik Aksı - Yük Merkezine Mesafe (x)' => '5.5 in',
                'Dingil Mesafesi (y)' => '50.4 in',
                'Servis Ağırlığı' => '3571.5 lb',
                'Lastik Tipi' => 'Poly (sürüş/taşıyıcı/kaster)',
                'Ön Lastik Ölçüsü' => '5.9 x 3.5 in (yük tekeri)',
                'Arka Lastik Ölçüsü' => '9 x 3 in (sürüş tekeri)',
                'Kaster Teker Ölçüsü' => '3 x 2 in',
                'Teker Sayısı (ön/arka)' => '1x, 2/2',
                'Ön İz Genişliği' => '28 in',
                'Arka İz Genişliği' => '22.4 in',
                'Katlanmış Yükseklik (h)' => '60 in',
                'Serbest Kaldırma (h)' => '32 in',
                'Maks. Çatal Yüksekliği (h3)' => '72 in',
                'Direk Yüksekliği (uzatılmış, h4)' => '101 in',
                'Operatör Bölmesi Yüksekliği (h6)' => '61.4 in',
                'Kaldırılmış Durakta Ayakta Yükseklik (h12)' => '48 in',
                'Alçaltılmış Yükseklik' => '2.5 in',
                'Toplam Uzunluk (l1)' => '99.2 in',
                'Yüze Kadar Uzunluk (l2)' => '63.2 in',
                'Toplam Genişlik (b1/b2)' => '31.5 in',
                'Çatal Ölçüleri (s/e/l)' => '1.4 / 3.9 / 42 in',
                'Çatal Aralığı (b5)' => '22 in',
                'Şasi Ortasında Yerden Yükseklik (m2)' => '2 in',
                'Sağ Açı İstif Koridor Genişliği (Ast)' => '111.5 in (x+Wa+48” palet)',
                'Dönüş Yarıçapı (Wa)' => '58 in',
                'Sürüş Hızı (yüklü/boş)' => '5 / 5 mph',
                'Kaldırma Hızı (yüklü/boş)' => '25.6 / 31.5 fpm',
                'İndirme Hızı (yüklü/boş)' => '31.5 / 35.4 fpm',
                'En Büyük Eğimi Tırmanma' => '0 % (iç mekân ve düz zemin)',
                'Servis Freni' => 'Rejeneratif, elektromanyetik park',
                'Sürüş Motoru (S2 60dk)' => '2.5 kW',
                'Kaldırma Motoru (S3 15%)' => '3 kW',
                'Batarya Seçenekleri' => '24V/340Ah Kurşun-asit, 24V/224Ah AGM; ayrıca Li-Ion opsiyonu',
                'Batarya Ağırlığı' => '700 lb',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Elektrik',
                'Gürültü Seviyesi' => '74 dB(A) operatör kulağı',
                'Şarj Cihazı Akımı' => '25A veya 40A',
                'Kullanım' => 'Sadece iç mekân; düz ve pürüzsüz zemin'
            ], JSON_UNESCAPED_UNICODE),

            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'Çift yönlü sürüş kumandası: çatal önde/arkada emniyetli kullanım'],
                ['icon' => 'arrow-up', 'text' => '72” maksimum kaldırma yüksekliği ile alt raflara güvenli erişim'],
                ['icon' => 'ruler-horizontal', 'text' => '42” çatal uzunluğu, 22” çatal aralığı ile palet uyumu'],
                ['icon' => 'battery-full', 'text' => '24V 340Ah kurşun-asit veya 224Ah AGM batarya seçenekleri'],
                ['icon' => 'gauge', 'text' => '5 mph sabit sürüş hızı; rejeneratif fren ile kontrollü duruş'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik park freni ve AC sürüş ile güvenli operasyon'],
                ['icon' => 'warehouse', 'text' => '31.5” gövde genişliği ve 58” dönüş yarıçapı ile dar koridor çevikliği'],
                ['icon' => 'cog', 'text' => 'Poly tekerlek seti ile düşük titreşim ve düşük bakım ihtiyacı']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
