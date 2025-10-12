<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WSA161_Istif_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'WSA161')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: WSA161'); return; }
        $variants = json_decode(<<<'JSON'
            [
                {
                    "sku": "WSA161-3000",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF WSA161 - 3000 mm Direk",
                    "short_description": "WSA161 3000 mm kaldırma yüksekliğiyle dar koridorlarda etkin raf erişimi sağlar. Kısa l2, dar dönüş yarıçapı ve oransal kaldırma sayesinde üst seviye raflarda hassas, hızlı ve güvenli palet konumlama sunar.",
                    "long_description": "\n<section><h2>İXTİF WSA161 - 3000 mm Direk</h2>\n<p>WSA161 3000 mm direk konfigürasyonu, çok seviyeli raf hatlarında hızlı ve sarsıntısız istif operasyonları için\ntasarlanmıştır. Kısa gövde ve yüksek görüş sayesinde raf yaklaşmalarında kontrollü hızlanma ve hassas yavaşlama\nmümkün olur. Li‑Ion enerji mimarisi, yoğun vardiyalarda ara şarj ile sürekliliği destekler.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>Oransal kaldırma sistemi paletin raf seviyesinde milimetrik ayar yapmasına izin verir; elektromanyetik fren ve\nkontrollü indirme güvenliği artırır. Kompakt şasi ve kısa l2 ölçüsü ile dar alanlarda dönüş manevraları akıcıdır.\nOperatör evrak cebi ve USB çıkışı gibi pratik detaylarla gün boyu konfor yaşar.</p></section>\n<section><h3>Sonuç</h3>\n<p>Raf yüksekliği 3000 mm olan hatlarda performansı maksimize eden bu konfigürasyon, depolama yoğunluğu artarken\nçevrim sürelerini düşürür ve hata riskini azaltır.</p></section>\n",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Çok seviyeli raflarda üst katman palet yerleştirmesi"
                        },
                        {
                            "icon": "box-open",
                            "text": "E-ticaret iade ve yeniden istiflemede üst seviye erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de mevsimsel yoğunlukta ek yükseklik ihtiyacı"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim sonrasında yüksek raf stoklaması"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya depolarında kontrollü üst raf yerleşimi"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv komponent raflarında üst seviye besleme"
                        }
                    ]
                },
                {
                    "sku": "WSA161-3900",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF WSA161 - 3900 mm Direk",
                    "short_description": "WSA161 3900 mm kaldırma yüksekliğiyle dar koridorlarda etkin raf erişimi sağlar. Kısa l2, dar dönüş yarıçapı ve oransal kaldırma sayesinde üst seviye raflarda hassas, hızlı ve güvenli palet konumlama sunar.",
                    "long_description": "\n<section><h2>İXTİF WSA161 - 3900 mm Direk</h2>\n<p>WSA161 3900 mm direk konfigürasyonu, çok seviyeli raf hatlarında hızlı ve sarsıntısız istif operasyonları için\ntasarlanmıştır. Kısa gövde ve yüksek görüş sayesinde raf yaklaşmalarında kontrollü hızlanma ve hassas yavaşlama\nmümkün olur. Li‑Ion enerji mimarisi, yoğun vardiyalarda ara şarj ile sürekliliği destekler.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>Oransal kaldırma sistemi paletin raf seviyesinde milimetrik ayar yapmasına izin verir; elektromanyetik fren ve\nkontrollü indirme güvenliği artırır. Kompakt şasi ve kısa l2 ölçüsü ile dar alanlarda dönüş manevraları akıcıdır.\nOperatör evrak cebi ve USB çıkışı gibi pratik detaylarla gün boyu konfor yaşar.</p></section>\n<section><h3>Sonuç</h3>\n<p>Raf yüksekliği 3900 mm olan hatlarda performansı maksimize eden bu konfigürasyon, depolama yoğunluğu artarken\nçevrim sürelerini düşürür ve hata riskini azaltır.</p></section>\n",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Çok seviyeli raflarda üst katman palet yerleştirmesi"
                        },
                        {
                            "icon": "box-open",
                            "text": "E-ticaret iade ve yeniden istiflemede üst seviye erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de mevsimsel yoğunlukta ek yükseklik ihtiyacı"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim sonrasında yüksek raf stoklaması"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya depolarında kontrollü üst raf yerleşimi"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv komponent raflarında üst seviye besleme"
                        }
                    ]
                },
                {
                    "sku": "WSA161-4800",
                    "variant_type": "direk-yuksekligi",
                    "title": "İXTİF WSA161 - 4800 mm Direk",
                    "short_description": "WSA161 4800 mm kaldırma yüksekliğiyle dar koridorlarda etkin raf erişimi sağlar. Kısa l2, dar dönüş yarıçapı ve oransal kaldırma sayesinde üst seviye raflarda hassas, hızlı ve güvenli palet konumlama sunar.",
                    "long_description": "\n<section><h2>İXTİF WSA161 - 4800 mm Direk</h2>\n<p>WSA161 4800 mm direk konfigürasyonu, çok seviyeli raf hatlarında hızlı ve sarsıntısız istif operasyonları için\ntasarlanmıştır. Kısa gövde ve yüksek görüş sayesinde raf yaklaşmalarında kontrollü hızlanma ve hassas yavaşlama\nmümkün olur. Li‑Ion enerji mimarisi, yoğun vardiyalarda ara şarj ile sürekliliği destekler.</p></section>\n<section><h3>Teknik Odak</h3>\n<p>Oransal kaldırma sistemi paletin raf seviyesinde milimetrik ayar yapmasına izin verir; elektromanyetik fren ve\nkontrollü indirme güvenliği artırır. Kompakt şasi ve kısa l2 ölçüsü ile dar alanlarda dönüş manevraları akıcıdır.\nOperatör evrak cebi ve USB çıkışı gibi pratik detaylarla gün boyu konfor yaşar.</p></section>\n<section><h3>Sonuç</h3>\n<p>Raf yüksekliği 4800 mm olan hatlarda performansı maksimize eden bu konfigürasyon, depolama yoğunluğu artarken\nçevrim sürelerini düşürür ve hata riskini azaltır.</p></section>\n",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Çok seviyeli raflarda üst katman palet yerleştirmesi"
                        },
                        {
                            "icon": "box-open",
                            "text": "E-ticaret iade ve yeniden istiflemede üst seviye erişim"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC’de mevsimsel yoğunlukta ek yükseklik ihtiyacı"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim sonrasında yüksek raf stoklaması"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya depolarında kontrollü üst raf yerleşimi"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv komponent raflarında üst seviye besleme"
                        }
                    ]
                }
            ]
JSON
        , true);
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
        }
        $this->command->info('✅ Variants: WSA161 varyantları işlendi');
    }
}
