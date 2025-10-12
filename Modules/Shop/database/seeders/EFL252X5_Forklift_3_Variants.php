<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL252X5_Forklift_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EFL252X5')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: EFL252X5'); return; }

        $variants = json_decode(<<<'JSON'
            [
                {
                    "sku": "EFL252X5-B280",
                    "variant_type": "batarya-tipi",
                    "title": "İXTİF EFL252X5 80V 280Ah (Tek Modül)",
                    "short_description": "Hafif‑orta yoğunluk için optimize 80V 280Ah tek modül Li‑ion; fırsat şarjı ile akıcı çevrim, düşük bakım ve dengeli performans.",
                    "long_description": "<section><h3>Varyant Özeti</h3><p>İXTİF EFL252X5 - Tek Modül 280Ah varyantı, hafif ve orta yoğunlukta vardiyalar için optimize\nedilmiştir. 80V 280Ah Li‑ion batarya, fırsat şarjına uygun kimyasıyla kısa molalarda hızla enerji\nkazanır. Bu sayede depo içindeki dalgalı iş yüklerinde planlı duruşları kısaltır. 16/17 km/s hız ve\n0.38/0.45 m/s kaldırma performansı, kapı geçişlerinden yükleme rampalarına kadar akıcı bir akış\nüretir. 150 mm yerden yükseklik, pnömatik lastiklerle birlikte çukurlu zeminlerde gövdenin\nkorunmasına yardımcı olur. 2270 mm dönüş yarıçapı dar koridorlarda palet yerleştirmelerini\nkolaylaştırır. Operatör tarafında geniş LED ekran, kol dayamalı koltuk ve yeni farlarla güven ve\nkonfor artar. 3000 mm standart kaldırma yüksekliği ve triplex direk yüksek görüş sunar. Bu varyant,\ntek vardiya veya tek vardiya + pik saat kombinasyonu gibi senaryolarda toplam sahip olma maliyetini\ndüşürmek isteyen işletmeler için ideal denge sunar.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "Vardiya içi besleme ve kısa mesafeli transfer"
                        },
                        {
                            "icon": "box-open",
                            "text": "E‑ticaret konsolidasyon ve ayırma alanları"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende DC ayrıştırma‑yükleme hatları"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim WIP akışı ve hücre arası taşıma"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv komponent istasyon besleme"
                        },
                        {
                            "icon": "flask",
                            "text": "Paketli kimyasal ürün stok hareketi"
                        }
                    ]
                },
                {
                    "sku": "EFL252X5-B560",
                    "variant_type": "batarya-tipi",
                    "title": "İXTİF EFL252X5 80V 560Ah (Çift Modül)",
                    "short_description": "Yoğun vardiya ve pik sezonlar için 80V 560Ah çift modül; daha uzun çevrim, daha az duruş ve maksimum süreklilik.",
                    "long_description": "<section><h3>Varyant Özeti</h3><p>İXTİF EFL252X5 - Çift Modül 560Ah varyantı, yoğun operasyonlar ve uzun vardiya akışları için\ntasarlandı. İki batarya modülünün sağladığı yüksek kapasite, ekipmanın gün boyunca enerji rezervini\nkoruyarak dur‑kalk çevrimlerinde performans kaybını önler. 17/25% tırmanma kabiliyeti ve 150 mm\nyerden yükseklik, açık saha ve rampa trafiğinde akıcılık sağlar. Fırsat şarjı desteği, yoğun\nsezonlarda bile esnek planlama imkânı verir. 16 kW sürüş ve 24 kW kaldırma motorları, ağır\npaletlerde bile kararlı hızlanma ve hassas kaldırma sağlar. Operatör koltuğu, geniş ekran ve fren\npedalı gibi iyileştirilmiş bileşenler konforu yükseltir. Yan kaydırıcı gibi ataşmanlarla hassas\nmerkezleme yapılabilir; kapasite hesapları ataşman ağırlığına göre güncellenmelidir. Çift modül\nçözümü, çoklu vardiya ve yoğun kapı‑rampa döngüleri olan 3PL, FMCG ve e‑ticaret merkezlerinde\nkesintisiz akışın anahtarıdır.</p></section>",
                    "use_cases": [
                        {
                            "icon": "warehouse",
                            "text": "3PL’de çok kapılı giriş‑çıkış trafiği"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "FMCG yüksek hacimli rampalar"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Soğuk zincir yükleme koridorları"
                        },
                        {
                            "icon": "box-open",
                            "text": "E‑ticaret pik sezon tam vardiya akışı"
                        },
                        {
                            "icon": "pills",
                            "text": "İlaç merkezlerinde kesintisiz sevkiyat"
                        },
                        {
                            "icon": "industry",
                            "text": "Ağır paletli üretim hat beslemesi"
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
        $this->command->info("✅ Variants eklendi: EFL252X5 (" . count($variants) . " adet)");
    }
}