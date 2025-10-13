<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFX5_301_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFX5-301')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFX5-301');
            return;
        }

        $variants = [

            [
                'sku' => 'EFX5-301-B280',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFX5 301 - 80V 280Ah Tek Modül',
                'short_description' => 'Tek modül 280Ah Li-Ion ile hafif-orta yoğunluklu görevlerde hızlı şarj ve düşük bakım. Kompakt ağırlık ile çevik manevra.',
                'body' => '<section><h3>Enerji ve Performans</h3><p>80V 280Ah tek modül Li-Ion, hafif ve orta yoğunluklu vardiyalarda hızlı fırsat şarjıyla sürekli hazır kalır. AC sürüş ve kaldırma sistemi, dar koridorlarda hassas manevra ve istikrarlı hız sağlar.</p></section>
<section><h3>Operasyon</h3><p>Sevkiyat hazırlığı, raf besleme ve iç hat lojistiğinde ekonomik bir çözüm sunar. Düşük bakım gereksinimi, toplam sahip olma maliyetini azaltır.</p></section>
<section><h3>Uygulama</h3><p>Mevsimsel piklerin olmadığı, tek vardiya düzenindeki depolar için ideal başlangıç konfigürasyonu.</p></section>',
                'use_cases' => json_decode(
                    <<<'JSON'
                    [
                        {
                            "icon": "warehouse",
                            "text": "Tek vardiya depo operasyonları"
                        },
                        {
                            "icon": "store",
                            "text": "Raf besleme ve palet taşıma"
                        },
                        {
                            "icon": "box-open",
                            "text": "Fulfillment çıkış hazırlığı"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "FMCG iç lojistik"
                        },
                        {
                            "icon": "industry",
                            "text": "Hafif üretim WIP taşıma"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimya ambalaj içi hareket"
                        }
                    ]
JSON,
                    true
                )
            ],

            [
                'sku' => 'EFX5-301-B560',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFX5 301 - 80V 560Ah Çift Modül',
                'short_description' => 'Çift modül 560Ah ile uzun vardiya dayanımı, fırsat şarjı ile minimum duruş. Pik sezon ve yüksek hacimli akışlar için.',
                'body' => '<section><h3>Yüksek Dayanım</h3><p>İki adet 80V 280Ah modül, toplam 560Ah kapasite sağlar. Yoğun vardiyalarda dahi akış kesintiye uğramadan devam eder; şarj molaları operasyon planına uyumlu şekilde kısalır.</p></section>
<section><h3>Verimlilik</h3><p>Yüksek seyir ve kaldırma hızları, rampa performansı ve geniş görüş ile hızlı konumlandırma; toplam çevrim sürelerini düşürür.</p></section>
<section><h3>Uygulama</h3><p>Hasat dönemi, kampanya sezonu ve 7×24 çalışan 3PL merkezlerinde önerilen konfigürasyondur.</p></section>',
                'use_cases' => json_decode(
                    <<<'JSON'
                    [
                        {
                            "icon": "warehouse",
                            "text": "3PL ve cross-dock merkezleri"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Gıda depolarında yoğun giriş-çıkış"
                        },
                        {
                            "icon": "car",
                            "text": "Otomotiv hat besleme (çok vardiya)"
                        },
                        {
                            "icon": "building",
                            "text": "Büyük DC’lerde kesintisiz iç taşıma"
                        },
                        {
                            "icon": "bolt",
                            "text": "Pik sezon yüksek talep akışları"
                        },
                        {
                            "icon": "star",
                            "text": "Operatör verimliliği ve süreklilik"
                        }
                    ]
JSON,
                    true
                )
            ],

            [
                'sku' => 'EFX5-301-M4500',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFX5 301 - 4.5 m Direk',
                'short_description' => '4.5 m kaldırma yüksekliğiyle raf yüksekliği artan depolarda hızlı yerleştirme ve net görüşle güvenli operasyon.',
                'body' => '<section><h3>Direk Seçeneği</h3><p>Yaklaşık 4500 mm kaldırma yüksekliği, orta-yüksek raf uygulamalarında daha fazla esneklik sunar. Geliştirilmiş görüş profili, palet hizalama hatalarını azaltır.</p></section>
<section><h3>Stabilite</h3><p>Nominal kapasite grafikleri ve ataşman etkileri dikkate alınarak güvenli çalışma sınırları korunur. Yan kaydırma kullanıldığında nominalden 100 kg düşüm unutulmamalıdır.</p></section>
<section><h3>Uygulama</h3><p>Perakende DC, toptan dağıtım ve üretim sahalarında yüksek lokasyon erişimi gerekirken ideal çözümdür.</p></section>',
                'use_cases' => json_decode(
                    <<<'JSON'
                    [
                        {
                            "icon": "store",
                            "text": "Perakende DC yüksek raflar"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Toplama/yerleştirme hatları"
                        },
                        {
                            "icon": "box-open",
                            "text": "Sipariş konsolidasyonu"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hücresi ara stok"
                        },
                        {
                            "icon": "briefcase",
                            "text": "Toptan dağıtım merkezleri"
                        },
                        {
                            "icon": "cart-shopping",
                            "text": "FMCG karışık paletleme"
                        }
                    ]
JSON,
                    true
                )
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
            $this->command->info("🧩 Varyant kaydedildi: {$v['sku']}");
        }
    }
}
