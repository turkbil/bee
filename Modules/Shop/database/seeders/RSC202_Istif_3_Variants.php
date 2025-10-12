<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSC202_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'RSC202')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (RSC202)'); return; }

        $variants = [
            [
                'sku' => 'RSC202-3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF RSC202 - 3000 mm Kaldırma',
                'short_description' => '3000 mm kaldırma, 2118 mm kapalı direk yüksekliği ve 3915 mm açık yükseklik ile genel depo uygulamalarında optimum görünürlük ve hız sağlar.',
                'long_description' => '<section><h3>3000 mm: Genel Amaçlı İstiflemede Altın Standart</h3><p>Standart 3000 mm kombinasyonu, RSC202 gövdesinin kompakt yapısıyla birlikte depolama yoğunluğu ve operatör görüşünü dengeler. 2118 mm kapalı direk yüksekliği, alçak kapı eşiklerinde rahat geçiş imkânı verirken, 3915 mm açık yükseklik çok katlı raflarda güvenli konumlandırma sunar. 5.5/6 km/s hız, 0.10/0.16 m/s kaldırma ve 0.19/0.16 m/s indirme değerleriyle çevrim süreleri kısalır. Elektronik direksiyon ve elektromanyetik frenler, dar alan manevralarında yumuşak ve güvenli kontrol sağlar.</p><p>40×122×1070 mm çatal ve 2A taşıyıcı, EUR palet başta olmak üzere yaygın ölçülerle uyumludur. 24V/280Ah enerji altyapısı, tek vardiya boyunca kararlı performans verir; Li‑ion opsiyonu hızlı şarj ve daha uzun çevrim ömrü ile filo verimliliğini artırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Genel depo ve cross-dock istifleme'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf besleme'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret sipariş konsolidasyonu'],
                    ['icon' => 'industry', 'text' => 'Montaj hattı WIP taşıma'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça rafları']
                ]
            ],
            [
                'sku' => 'RSC202-3600',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF RSC202 - 3600 mm Kaldırma',
                'short_description' => '3600 mm kaldırma; daha yüksek raflarda güvenli erişim, yine kompakt 900 mm genişlik ve 1915 mm dönüş yarıçapı ile dar koridor çevikliği sunar.',
                'long_description' => '<section><h3>3600 mm: Yüksekliğe Çıkan Esneklik</h3><p>3600 mm direk, orta-yüksek raflı depolar için ek erişim sağlarken şasi dengesini korur. Operatör, oransal kaldırma ile son raf seviyesinde dahi nazik hamleler yapar; ürün zedelenmesi ve palet deformasyonu azalır. 116 mm yer açıklığı ve PU tekerler, düzensiz zeminlerde takılmaları azaltır. 3.3 kW AC sürüş ve 3.0 kW kaldırma motoru, yük altındaki hız düşümlerini minimuma indirerek çevrimi hızlandırır.</p><p>Elektronik direksiyonun tutarlı tepkisi, kapalı alanlarda tekrarlanabilir manevra sağlar. Li‑ion batarya seçeneği, vardiyalar arası hızlı enerji takviyesi ile ekipman devir teslimini kolaylaştırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Orta-yüksek raflı depo koridorları'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG backstore stok yönetimi'],
                    ['icon' => 'pills', 'text' => 'İlaç raflarında kontrollü erişim'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ürün stok alanları'],
                    ['icon' => 'print', 'text' => 'Ambalaj malzemesi istifi'],
                    ['icon' => 'couch', 'text' => 'Mobilya komponent depoları']
                ]
            ],
            [
                'sku' => 'RSC202-4500-FEM',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF RSC202 - 4500 mm Serbest Direk',
                'short_description' => '4500 mm serbest direk seçeneği, alçak kapı ve tavan kısıtlarında bile kabin ve kapaktan bağımsız kaldırma imkânı sağlar; hassas operasyonlar için idealdir.',
                'long_description' => '<section><h3>4500 mm Serbest Direk: Kısıtlı Yüksekliklerde Maksimum Verim</h3><p>Serbest direk (FEM) ile düşük tavanlı alanlardan geçerken yükü erken kaldırabilir, raf önünde minimum manevra ile zaman kazanırsınız. Kaldırma ve indirme hızlarının dengesi, son seviye hassas yerleştirmede operatöre güven verir. 2A çatal taşıyıcı ve 800 mm genişlik, geniş palet yelpazesi ile uyumluluğu sürdürür. Güvenlik paketi (mavi ışık, uyarı lambası) ile yaya trafiği yoğun alanlarda görünürlük artar.</p><p>Telematik modülü ile kullanım saatleri ve olay kayıtları izlenebilir; filo yönetiminde veri odaklı bakım periyotları planlanır. Li‑ion akü ile birlikte aralıklı hızlı şarj, çok vardiyalı işletmelerde süreklilik sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'building', 'text' => 'Düşük tavanlı depo girişleri'],
                    ['icon' => 'warehouse', 'text' => 'Karma raf sistemleri (Drive-in/Selective)'],
                    ['icon' => 'bolt', 'text' => 'Yoğun vardiya ardışık istifleme'],
                    ['icon' => 'shield-alt', 'text' => 'Yaya trafiği yoğun alanlarda güvenli çalışma'],
                    ['icon' => 'microchip', 'text' => 'Telemetri ile filo takibi gerektiren tesisler'],
                    ['icon' => 'star', 'text' => 'Hasarsız ürün yönetimi hedefli operasyonlar']
                ]
            ],
            [
                'sku' => 'RSC202-5000-FEM',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF RSC202 - 5000 mm Serbest Direk',
                'short_description' => '5000 mm serbest direk, yüksek raflı tesislerde maksimum erişim ve hassas kontrol sağlayarak depolama yoğunluğunu artırır.',
                'long_description' => '<section><h3>5000 mm Serbest Direk: Maksimum Erişim, Minimum Taviz</h3><p>En yüksek direk seçeneği ile 5 metreye kadar erişim, depolama kapasitesini artırır ve kat yüksekliği daha iyi değerlendirilir. Oransal kaldırma, yüksek seviyelerde dahi sarsıntısız hareket sağlar. Elektronik direksiyon ve elektromanyetik fren sinerjisi, maksimum yükseklikte güven duygusunu destekler. 24V/280Ah enerji sistemi ve opsiyonel Li‑ion batarya, bu seviyelerde arka arkaya operasyonlar için sürdürülebilir güç sunar.</p><p>Görüş hatlarının korunması için uygun mast konfigürasyonları ve backrest seçenekleri ile ürün stabilitesi artırılır. Güvenlik aksesuarları ve pin/pad erişim kontrolüyle yetkisiz kullanımın önüne geçilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı depolama alanları'],
                    ['icon' => 'briefcase', 'text' => 'Kontrat lojistiğinde uzun vadeli stoklar'],
                    ['icon' => 'cart-shopping', 'text' => 'Büyük hacimli FMCG dağıtım merkezleri'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek endüstrisi katlı raf sistemleri'],
                    ['icon' => 'book', 'text' => 'Arşiv ve dokümantasyon depoları'],
                    ['icon' => 'seedling', 'text' => 'Bahçe/DIY yüksek raf stok sahaları']
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
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
            $this->command->info("✅ Varyant eklendi: {$v['sku']}");
        }
    }
}
