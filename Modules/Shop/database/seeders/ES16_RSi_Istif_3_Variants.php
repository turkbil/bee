<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ES16_RSi_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'ES16-RSi')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: ES16-RSi');
            return;
        }

        $variants = [
            [
                'sku' => 'ES16-RSi-3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ES16 RSi - 3000 mm Direk',
                'short_description' => '3000 mm kaldırma yüksekliğiyle standart depo raflarına uyumlu ES16 RSi, 1600 kg mast kapasitesi ve 2000 kg toplam kapasite ile dar koridorlarda çevik bir istif çözümü sunar.',
                'body' => '<section><h2>3000 mm Direk: Çeviklik ve Hızın Dengesi</h2><p>3000 mm direk konfigürasyonu, yaygın raf yükseklikleri için optimize edilmiştir. 1600 kg mast kapasitesi ile güvenli istifleme yaparken, destek kollarının ilk kaldırma fonksiyonu sayesinde 2000 kg toplam taşıma kapasitesi korunur. 5,5/6 km/s sürüş hızları ve AC tahrik, yoğun vardiyalarda akıcı akış sağlar. 1765/2192 mm dönüş yarıçapı ve 2195 mm toplam uzunluk, koridor sonlarında rahat U dönüşleri ve palet hizalamaları sağlar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>Standart 24V 280Ah akü, tek vardiyada yeterli dayanıklılık sunarken, ara şarj uygulamalarıyla gün içi üretkenliği yükseltir. 0,11/0,16 m/s kaldırma hızları, toplama ve yerleştirme döngülerini kısaltır. 60×190×1150 mm çatallar ve 800 mm taşıyıcı genişliği, EUR paletlerde problemsiz kullanım sağlar. Elektromanyetik fren ve viraj yavaşlatma, yük denge merkezini koruyarak salınımları minimize eder.</p></section><section><h3>Operasyonel Faydalar</h3><p>3PL ve perakende operasyonlarında tampon alan boşaltma, inbound/outbound geçişleri ve raf içi konumlama işlerinde optimum denge sunar. Enerji verimli AC sürüş ve elektronik direksiyon, operatör yorgunluğunu azaltır ve hatasız manevraları teşvik eder.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Fulfillment raflarına giriş-çıkışta standart yükseklik istifleme'],
                    ['icon' => 'warehouse', 'text' => '3PL tampon alanlarında hızlı yerleştirme döngüleri'],
                    ['icon' => 'store', 'text' => 'Perakende DC içi konsolidasyon koridorları'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında güvenli raf seviyesi geçişleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça raf arası akış'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında kontrollü istifleme']
                ]
            ],
            [
                'sku' => 'ES16-RSi-3600',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ES16 RSi - 3600 mm Direk',
                'short_description' => '3600 mm kaldırma ile raf yüksekliği artan depolar için ideal; 1200 kg mast kapasitesi, AC tahrik ve elektronik direksiyon ile yüksek kontrol sağlar.',
                'body' => '<section><h2>3600 mm Direk: Yüksek Raflara Güvenli Erişim</h2><p>3600 mm direk seçeneği, raf yüksekliği talebi artan dağıtım merkezleri için ölçeklenebilir bir çözümdür. 1200 kg mast kapasitesi, yük denge kriterlerini karşılayarak güvenli istiflemeye olanak tanır. AC sürüş, 5,5/6 km/s hız değerleri ve elektromanyetik fren ile kontrollü hızlanma-yavaşlama profili sunar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>24V 280Ah batarya ve 3.0 kW kaldırma motoru, yükseltilmiş yüksekliklerde dahi akıcı kaldırma sağlar. Dönüş yarıçapı 1765/2192 mm seviyesinde kalır; 20 mm şasi alt boşluğu, rampa geçişlerinde şasiyi korur. PU tekerlekler, zemin aşınmasını azaltır ve sürüş konforunu artırır.</p></section><section><h3>Operasyonel Faydalar</h3><p>3PL, FMCG ve elektronik dağıtım merkezlerinde raf yükseltme projelerine uyum sağlar. Elektronik direksiyon, dar koridorlarda milimetrik hizalamayı destekler; operatör hatalarını azaltır ve çevrimi hızlandırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yükseltilmiş raf hatlarında istifleme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında toplama sonrası yerleştirme'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret iade alanlarında tampon istif'],
                    ['icon' => 'store', 'text' => 'Perakende split-case konsolidasyon bölgeleri'],
                    ['icon' => 'microchip', 'text' => 'Elektronik ürün stok alanları'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası yarı mamul rafları']
                ]
            ],
            [
                'sku' => 'ES16-RSi-4500',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF ES16 RSi - 4500 mm Direk',
                'short_description' => '4500 mm kaldırma yüksekliği, 700 kg mast kapasitesi ile yüksek raflara erişim sağlar. AC tahrik ve güvenlik sistemleri, hassas ve dengeli operasyon sağlar.',
                'body' => '<section><h2>4500 mm Direk: Yüksekliğe Odaklı Çözüm</h2><p>4500 mm direk konfigürasyonu, alanı maksimuma çıkarma hedefi olan depolar için tasarlanmıştır. 700 kg mast kapasitesi, doğru yük dağılımı ve denge merkezine bağlı olarak güvenli kullanım sağlar. Elektronik direksiyon ve viraj yavaşlatma, yük yüksek konumdayken stabiliteyi korumaya yardımcı olur.</p></section><section><h3>Teknik Ayrıntılar</h3><p>24V 280Ah enerji altyapısı ve 3.0 kW kaldırma motoru, yüksek irtifalarda dahi kontrollü hareket sağlar. 0,14/0,12 m/s indirme hızları, raf çıkışında sarsıntısız ve kontrollü iniş sunar. 850 mm şasi genişliği ve 2195 mm uzunluk, yüksekliğe rağmen manevra kabiliyetini korur.</p></section><section><h3>Operasyonel Faydalar</h3><p>Yüksek raflı 3PL, e-ticaret ve yedek parça depolarında stok yoğunluğunu artırır. Operatör yorgunluğunu azaltan ergonomik kumanda, vardiya boyunca tekrarlı görevlerde tutarlılık sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı depolarda ana stok alanı yönetimi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret ana stoktan sipariş hazırlığa besleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv ana depo üst seviye rafları'],
                    ['icon' => 'pills', 'text' => 'Hassas ürünlerin üst raf güvenli istifi'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası yüksek irtifa stok alanı'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalajların yüksek raf lokasyonları']
                ]
            ]
        ];

        foreach ($variants as $v) {
            DB::table('shop_products')->updateOrInsert(['sku' => $v['sku']], [
                'sku' => $v['sku'],
                'parent_product_id' => $m->product_id,
                'variant_type' => $v['variant_type'],
                'category_id' => $m->category_id,
                'brand_id' => $m->brand_id,
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'body' => json_encode(['tr' => $v['body']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info("✅ Varyant kaydedildi: {$v['sku']}");
        }
    }
}
