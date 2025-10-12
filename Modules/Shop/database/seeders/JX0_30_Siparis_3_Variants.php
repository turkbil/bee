<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX0_30_Siparis_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'JX0-30')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı: JX0-30'); return; }
        $variants = json_decode(<<<'JSON'
            [
                {
                    "sku": "JX0-30-LI135",
                    "variant_type": "batarya-tipi",
                    "title": "İXTİF JX0_30 - Li-Ion 24V/135Ah",
                    "short_description": "Bakım gerektirmeyen 24V/135Ah Li-Ion paket ile fırsat şarjı destekli uzun vardiya performansı ve tutarlı hız/kaldırma deneyimi.",
                    "long_description": "<section><h3>Li-Ion Enerji Mimarisinin Avantajı</h3><p>24V/135Ah Li-Ion paket, bellek etkisi ve gaz çıkışı bulunmayan hücre kimyası ile bakım gereksinimini en aza indirir. Entegre 24V-30A şarj cihazı, vardiya aralarında 15-30 dakikalık molalarda kapasiteyi yenileyerek gün içi kullanılabilirliği yükseltir. Düşük iç direnç, 6/6,5 km/saat hız ve 0,22/0,27 m/s kaldırma hızlarında kararlı performans sağlar.</p><p>Güvenlik tarafında, hücre dengeleme ve sıcaklık izleme üniteleri olası aşırı akım/ısı koşullarına karşı koruma sunar. Bu mimari, toplam sahip olma maliyetini düşürürken çevresel etkileri azaltır.</p></section><section><h3>Operasyonel Uyum</h3><p>E-ticaret ve 3PL merkezlerinde yoğun sipariş dalgalarında, fırsat şarjı sayesinde plan dışı duruşlar azalır. Bu yapılandırma, günlük 2-3 vardiyalı düzende dahi enerji sürekliliği sağlar; operatör sensörleri, mavi uyarı ışığı ve buzzer ile birleşerek güvenli ve verimli bir deneyim sunar.</p></section>",
                    "use_cases": [
                        {
                            "icon": "box-open",
                            "text": "Dalgalı siparişlerde fırsat şarjı ile kesintisiz toplama"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Yüksek hacimli 3PL bölgelerinde çok vardiyalı kullanım"
                        },
                        {
                            "icon": "store",
                            "text": "Perakende geri dolum ve sezon içi kampanya hazırlığı"
                        },
                        {
                            "icon": "snowflake",
                            "text": "Soğuk oda giriş-çıkışlarında hızlı geçiş"
                        },
                        {
                            "icon": "pills",
                            "text": "Hassas ürün toplama süreçlerinde stabil hız"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim hücrelerinde WIP taşıma ve istasyon besleme"
                        }
                    ]
                },
                {
                    "sku": "JX0-30-LA120",
                    "variant_type": "batarya-tipi",
                    "title": "İXTİF JX0_30 - Kurşun Asit 24V/120Ah",
                    "short_description": "24V/120Ah kurşun asit paket ile bütçe odaklı başlangıç maliyeti; standart şarj prosedürüyle tek vardiya çözümleri.",
                    "long_description": "<section><h3>Bütçe Dostu Enerji Seçeneği</h3><p>24V/120Ah kurşun asit akü, giriş seviyesi toplam sahip olma maliyeti arayan operasyonlar için uygundur. Planlı şarj molaları ile tek vardiya boyunca 6/6,5 km/saat hız ve 0,22/0,27 m/s kaldırma hızlarına uygun performans sunar. Düzenli bakım ve elektrolit kontrolü ile servis ömrü optimize edilir.</p><section><h3>Kullanım Senaryoları</h3><p>Düşük-orta yoğunluklu sipariş toplama, perakende geri dolum ve dönemsel kampanyalarda ekonomik bir çözümdür. Operatör güvenliği; kapı kilidi, sensörler, blue spot ve buzzer ile desteklenir.</p></section>",
                    "use_cases": [
                        {
                            "icon": "store",
                            "text": "Düşük-orta yoğunluklu perakende geri dolum"
                        },
                        {
                            "icon": "box-open",
                            "text": "Gün içi tek vardiyada kampanya hazırlıkları"
                        },
                        {
                            "icon": "warehouse",
                            "text": "Bölgesel dağıtım merkezlerinde toplama"
                        },
                        {
                            "icon": "car",
                            "text": "Yedek parça rafları ve servis stok alanları"
                        },
                        {
                            "icon": "flask",
                            "text": "Kimyasal sarf depolarında temel toplama"
                        },
                        {
                            "icon": "industry",
                            "text": "Üretim içi yardımcı iş istasyonu geçişleri"
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
            $this->command->info("✅ Variant eklendi/güncellendi: ". $v['sku']);
        }
    }
}
