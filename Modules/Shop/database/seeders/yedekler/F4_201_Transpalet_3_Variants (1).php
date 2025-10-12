<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_201_Transpalet_3_Variants extends Seeder
{
    public function run()
    {
        $parent = DB::table('shop_products')->where('sku', 'F4-201')->first();
        if (!$parent) return;

        $categoryId = $parent->category_id;
        $brandId = $parent->brand_id;

        $variants = [
            [
                'sku' => 'F4-201-900x560',
                'title' => 'İXTİF F4 201 - 900×560 mm Çatal',
                'slug' => Str::slug('İXTİF F4 201 - 900×560 mm Çatal'),
                'short' => 'Kısa çatal ile dar koridor ve mini palet uygulamalarında üstün manevra ve hız. Market arkası ve hafif endüstriyel görevler için optimize.',
                'fork' => '900×560',
            ],
            [
                'sku' => 'F4-201-1150x560',
                'title' => 'İXTİF F4 201 - 1150×560 mm Çatal (Standart)',
                'slug' => Str::slug('İXTİF F4 201 - 1150×560 mm Çatal (Standart)'),
                'short' => 'Standart 1150 mm çatal boyu ile EUR paletlerde en yaygın kullanım. Denge, erişim ve hız arasında ideal denge.',
                'fork' => '1150×560',
            ],
            [
                'sku' => 'F4-201-1220x685',
                'title' => 'İXTİF F4 201 - 1220×685 mm Çatal (Geniş Palet)',
                'slug' => Str::slug('İXTİF F4 201 - 1220×685 mm Çatal (Geniş Palet)'),
                'short' => 'Geniş 685 mm çatal aralığıyla endüstriyel paletlerde yüksek stabilite. Uzun yüklerde güvenli taşıma.',
                'fork' => '1220×685',
            ],
        ];

        foreach ($variants as $v) {
            $exists = DB::table('shop_products')->where('sku', $v['sku'])->first();
            if ($exists) continue;

            DB::table('shop_products')->insert([
                'sku' => $v['sku'],
                'model_number' => null,
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug'  => json_encode(['tr' => $v['slug']], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short']], JSON_UNESCAPED_UNICODE),

                'product_type' => 'physical',
                'condition' => 'new',
                'currency' => 'TRY',
                'price_on_request' => true,
                'base_price' => 0.00,

                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'is_master_product' => false,
                'parent_product_id' => $parent->product_id,
                'variant_type' => 'catal-uzunlugu',

                'use_cases' => json_encode([
                    "Dar koridor mağaza arkalarında {$v['fork']} çatal ile hızlı raf besleme.",
                    "Kısa dur-kalk operasyonlarında {$v['fork']} boyu ile çevik manevra ve düşük temas riski.",
                    "Araç içi yükleme-boşaltmada {$v['fork']} ile sınırlı alanlarda kontrollü taşıma.",
                    "Kampanya dönemlerinde kiralık havuzla {$v['fork']} konfigürasyonlarını hızla ölçekleme.",
                    "Soğuk oda giriş-çıkışlarında {$v['fork']} ile düşük bekleme süreleri.",
                    "E-ticaret ayıklama hatlarında {$v['fork']} ile paletten palete hızlı aktarım.",
                    "İade süreçlerinde çok noktalı toplamada {$v['fork']} ile çeviklik.",
                    "Proje bazlı kurulumlarda orijinal yedek parça ve 7/24 servis güvencesiyle kesintisiz devreye alma."
                ], JSON_UNESCAPED_UNICODE),

                'long_description' => json_encode(['tr' => <<<HTML
<section class="variant-intro">
  <h2>{$v['title']}</h2>
  <p><strong>Depo hedefinize göre çatalı seçin, verimliliği katlayın.</strong></p>
  <p>{$v['fork']} konfigürasyonu, F4 201’in 48V güç mimarisi ve tak-çıkar Li-Ion batarya esnekliği ile birleştiğinde koridor başına işlem süresini kısaltır, operatör hatasını azaltır.</p>
  <ul>
    <li>Varyanta özel denge ve manevra karakteristiği</li>
    <li>EUR ve endüstriyel paletlerle doğal uyum</li>
    <li>İXTİF hizmetleri: ikinci el alım-satımı, kiralık seçenekler, orijinal yedek parça, 7/24 teknik servis</li>
  </ul>
</section>
<section class="variant-body">
  <h3>Neden {$v['fork']}?</h3>
  <p>{$v['fork']} ölçüsü, çalışma hattının tipine göre yükün ağırlık merkezi ile teker seti arasındaki moment kolunu optimize eder. Kısa ölçüler konjesyonu yüksek alanlarda daha az dönüş hacmi ve çarpma riskini getirir; daha uzun ölçüler palet boyuna tam temas sağlayarak zikzak hareketleri azaltır. 590/695 mm gövde genişliği seçenekleri ile bu ölçü, palet ceplerine girişte takılmayı azaltır, 1360 mm dönüş yarıçapı ile dar alanlarda kesintisiz akış yaratır.</p>
  <h3>Kullanım Avantajları</h3>
  <p>Operasyonel verim, çevrim başına süre ve hata oranı ile ölçülür. {$v['fork']} çatal ile operatör, palet içine hizalamayı daha az manevra ile tamamlar. 48/20 V/Ah Li-Ion sistem ara şarj ile vardiya içinde süreklilik sağlar. Elektromanyetik fren ve BLDC sürüş ince hız dozajı sunar; bu sayede ramak kala olaylar azalır, ürün hasarı düşer. İXTİF’in kiralık seçenekler portföyü, bu varyantla ani hacim artışlarını karşılamanızı sağlar. Orijinal yedek parça ve 7/24 teknik servis, arıza anında MTTR’ı kısaltır. İkinci el alım-satımı ise parkı genç tutarken nakit akışını dengeler.</p>
  <h3>Diğer Varyantlardan Farkı</h3>
  <p>{$v['fork']} seçeneği, aynı modelin diğer çatal boylarına göre yük temas yüzeyini ve dengeyi farklılaştırır. Kısa ölçüler hız ve kıvraklık sunar; uzun ölçüler palet içinde tam oturuş ve uzun yüklerde esneklik sağlar. 560/685 mm aralıkla birlikte bakıldığında, bu varyant belirli palet standartlarında daha az düzeltme manevrası gerektirir. Bu da işlem başına enerji tüketimini düşürür ve batarya çevrim ömrüne olumlu yansır.</p>
  <h3>Hangi Durumlarda?</h3>
  <p>Raf arası mesafenin sınırlı olduğu mağaza arkaları, yüksek sipariş yoğunluklu e-ticaret depoları, yoğun cross-dock hatları ve proje bazlı geçici kurulumlar için idealdir. Eğer operasyonunuz dönemsel olarak artan bir hacimle karşılaşıyorsa, İXTİF kiralık seçenekler ve ikinci el alım-satımı ile bu varyantı hızlıca parkınıza dahil edebilir, orijinal yedek parça ve 7/24 teknik servis ile riskleri kontrol altında tutabilirsiniz.</p>
  <h3>Teknik Detaylar</h3>
  <p>F4 201 temel mimarisi sabittir: 2.0 ton kapasite, 600 mm yük merkezi, 48V BLDC sürüş, elektromanyetik fren ve 1360 mm dönüş yarıçapı. Bu varyant özelinde yalnızca çatal ölçüsü {$v['fork']} olarak yapılandırılmıştır. Diğer tüm teknik değerler ana üründeki ile aynıdır.</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),
                'is_active' => true,
                'published_at' => now(),
            ]);
        }
    }
}
