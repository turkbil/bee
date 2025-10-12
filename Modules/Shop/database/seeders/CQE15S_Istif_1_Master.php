<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CQE15S_Istif_1_Master extends Seeder {
    public function run(): void {
        $sku = 'CQE15S';
        $titleTr = 'İXTİF CQE15S - 3000 lb Walkie Reach İstif Makinesi';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'Pantograf kollu erişim ile çift derin paletleri alabilen, standart güç direksiyonu ve yanak kaydırma donanımıyla gelen, 189 inçe kadar kaldırma sunan 3000 lb kapasiteli yürüyen tip reach istif makinesi. 24V Li-ion, AGM ve sulu akü seçenekleri mevcuttur.'], JSON_UNESCAPED_UNICODE),
            'category_id' => 6,
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
                'Sürüş' => 'Elektrik',
                'Kapasite' => '3000 lb (1360 kg)',
                'Yük Merkezi Mesafesi (c)' => '24 inç',
                'Servis Ağırlığı' => '4090 / 4530 / 5220 lb (direğe bağlı)',
                'Maks. Çatal Yüksekliği (h3)' => '126 / 157 / 189 inç',
                'Direk Yükseldiğinde Yükseklik (h4)' => '162.8 / 195.25 / 227 inç',
                'Yük Teker Merkezine Mesafe (x)' => '16.1 inç',
                'Dingil Mesafesi (y)' => '54.9 inç',
                'Yana Açıklık (b1/b2)' => '42 / 50 inç',
                'Çatal Ölçüsü (s/e/l)' => '1.5 x 4 x 42 inç',
                'Erişim Mesafesi (l4)' => '23 inç',
                'Dönüş Yarıçapı (Wa)' => '62.6 inç',
                'Seyir Hızı (yüklü/boş)' => '3.1 / 3.4 mph',
                'Kaldırma Hızı (yüklü/boş)' => '20 / 26 fpm',
                'İndirme Hızı (yüklü/boş)' => '52 / 33 fpm',
                'Erişim Hızı (yüklü/boş)' => '15.75 fpm',
                'Maks. Eğim (yüklü/boş)' => '%%6 / %%10',
                'Sürüş Motoru (S2 60 dk)' => '4.4 HP',
                'Kaldırma Motoru (S3 15%%)' => '5.4 kW',
                'Akü Seçenekleri' => '24V/170Ah Li-ion, 24V/205Ah Li-ion; 24V/224Ah AGM; 24V/255Ah ve 24V/510Ah kurşun-asit',
                'Direksiyon' => 'Elektrik, ofset tiller kolu',
                'Frenler' => 'Elektromanyetik (servis ve park)',
                'Teker Tipi' => 'Poliüretan',
                'Serbest Kaldırma (h2)' => '6 / 39 / 41 inç',
                'Şasi Uzunluğu (l)' => '88.6 inç',
                'Yük Yüzüne Kadar Uzunluk (l2)' => '46.6 inç',
                'Minimum Koridor Genişliği (Ast)' => '138.7 / 105.9 / 105.9 inç'
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([
                ['icon' => 'arrows-alt', 'text' => 'Pantograf erişim ile çift derin paletlere uzanma'],
                ['icon' => 'cog', 'text' => 'Güç direksiyonu ve ergonomik kontrol kolu'],
                ['icon' => 'arrows-alt', 'text' => 'Standart yanak kaydırma ve tilt fonksiyonları'],
                ['icon' => 'battery-full', 'text' => '24V Li-ion dahil geniş akü seçenekleri'],
                ['icon' => 'industry', 'text' => 'Ofset tiller ile artırılmış görüş ve kontrol'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik servis ve park freni'],
                ['icon' => 'star', 'text' => '3.4 mph’a kadar seyir hızı ile verimlilik'],
                ['icon' => 'cart-shopping', 'text' => '189 inçe kadar kaldırma ile yüksek raf erişimi']
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $this->command->info("✅ Master oluşturuldu: {$sku}");
    }
}
