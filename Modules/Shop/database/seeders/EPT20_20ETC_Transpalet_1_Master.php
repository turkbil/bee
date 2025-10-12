<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_20ETC_Transpalet_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2;
        $brandId = 1;
        $sku = 'EPT20-20ETC';
        $titleTr = 'İXTİF EPT20-20ETC - 2.0 Ton Soğuk Depo Transpalet';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'EPT20-20ETC, -25℃ ortamlar için tasarlanmış, kompakt ve tam sızdırmaz elektrikli yaya tipi transpalettir. 2000 kg kapasite, 48V 40Ah Li-Ion akü, 1505 mm dönüş yarıçapı ve IP67 korumalı tahrik ile süpermarket, dağıtım ve soğuk zincir transferlerinde verim sağlar.'], JSON_UNESCAPED_UNICODE),
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
                'Model' => 'EPT20-20ETC',
                'Sürüş' => 'Elektrikli',
                'Kullanıcı Tipi' => 'Yaya',
                'Kapasite (Q)' => '2000 kg',
                'Yük Merkezi Mesafesi (c)' => '600 mm',
                'Sürücü Aksı Orta Noktasından Yüke Mesafe (x)' => '946 mm',
                'Dingil Mesafesi (y)' => '1301 mm',
                'Servis Ağırlığı' => '309 kg',
                'Aks Yükü, Yüklü Ön/Arka' => '722 / 1498 kg',
                'Aks Yükü, Yüksüz Ön/Arka' => '180 / 40 kg',
                'Lastik Tipi (Ön/Arka)' => 'Kauçuk/Poliüretan · Kauçuk/Naylon',
                'Ön Lastik Ölçüsü' => 'Ø210×70 mm',
                'Arka Lastik Ölçüsü' => 'Ø80×61 / 105×88 mm',
                'Tekerlek Sayısı (ön/arka)' => '1x, — / 4',
                'Arka İz Genişliği (b11)' => '410 (535) mm',
                'Sürüş Kolu Yüksekliği min./max. (h14)' => '115 mm',
                'Çatal Yükseklik, alçak/yüksek (h13)' => '80 / 105 mm',
                'Toplam Uzunluk (l1)' => '1673 mm',
                'Yük Aslında Uzunluk (l2)' => '522 mm',
                'Toplam Genişlik (b1/b2)' => '560 (685) mm',
                'Çatal Ölçüleri (s/e/l)' => '50 / 150 / 1150 mm',
                'Çatal Aralığı (b5)' => '560 (685) mm',
                'Dingil Mesafesi Orta Noktasında Yerden Yükseklik (m2)' => '30 mm',
                'Koridor Genişliği 1000×1200 (Ast)' => '2307 mm',
                'Koridor Genişliği 800×1200 (Ast)' => '2179 mm',
                'Dönüş Yarıçapı (Wa)' => '1505 mm',
                'Sürüş Hızı, yüklü/yüksüz' => '4 / 4.5 km/h',
                'Kaldırma Hızı, yüklü/yüksüz' => '0.027 / 0.038 m/s',
                'İndirme Hızı, yüklü/yüksüz' => '0.059 / 0.039 m/s',
                'Tırmanma Kabiliyeti, yüklü/yüksüz' => '8 / 20 %',
                'Fren' => 'Elektromanyetik',
                'Sürüş Motoru (S2 60 dk)' => '0.9 kW',
                'Kaldırma Motoru (S3 15%)' => '0.8 kW',
                'Batarya (V/Ah)' => '48 / 40',
                'Batarya Ağırlığı' => '40.8 kg',
                'Sürüş Kontrolü' => 'AC',
                'Direksiyon' => 'Mekanik',
                'Operatör Gürültü Seviyesi' => '74 dB(A)'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'snowflake', 'text' => '-25℃ soğuk depolara uygun tam sızdırmaz yapı'],
                ['icon' => 'battery-full', 'text' => '48V 40Ah Li-Ion batarya ile istikrarlı güç'],
                ['icon' => 'shield-alt', 'text' => 'IP67 korumalı motor ve elektronik bileşenler'],
                ['icon' => 'arrows-alt', 'text' => '1505 mm küçük dönüş yarıçapı, dar alan çevikliği'],
                ['icon' => 'bolt', 'text' => 'Optimize sürüş sistemi, verimli çekiş ve hız'],
                ['icon' => 'circle-notch', 'text' => 'Kauçuk tahrik tekeri ile kaymaya karşı yüksek tutunma'],
                ['icon' => 'warehouse', 'text' => 'Kompakt şasi, koridorlarda rahat manevra'],
                ['icon' => 'cog', 'text' => 'Düşük bakım gerektiren basit mekanik direksiyon']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info('✅ Master oluşturuldu: ' . $sku);
    }
}
