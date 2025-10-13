<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFLA251S_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFLA251S')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFLA251S');
            return;
        }

        $variants = [
            [
                'sku' => 'EFLA251S-80V460',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFLA251S - 80V 460Ah Li-Ion',
                'short_description' => 'Yoğun vardiyalar için 80V/460Ah yüksek kapasiteli Li-Ion seçenek ile kesintisiz çalışma. Hızlı fırsat şarjı, düşük ısı yönetimi ve stabil performansla iç mekânda verimlilik.',
                'body' => '<section><h2>80V/460Ah: Uzun Vardiya İçin Enerji Sürekliliği</h2><p>Bu varyant, standart 230Ah konfigürasyonuna göre iki katına yakın enerji depolayarak çok vardiyalı operasyonların temposunu yakalamak üzere tasarlanmıştır. Li-Ion kimyanın yüksek enerji yoğunluğu, aynı şasi içinde daha uzun çalışma pencereleri sunar. Pasif ısı yönetimi ve gövde içi konumlanan kontrol cihazı sayesinde ek soğutma fanlarına gerek duyulmaz; gürültü ve enerji tüketimi azalır. Operatör, aralarda fırsat şarjı uygulayarak vardiya planını esnek yönetebilir.</p></section><section><h3>Teknik Etki</h3><p>80V/460Ah paket, 15 kW sürüş ve 26 kW kaldırma motoruna sürekli ve kararlı enerji sağlar. Yüksek debili hidrolik hat ile birleştiğinde 0.61/0.64 m/s seviyesindeki kaldırma hızları korunur. Ağırlık merkezi hesapları, 1485 mm dingil açıklığıyla uyumlu kalır; 1990 mm dönüş yarıçapı değişmeden dar koridor performansı sürer. Batarya yönetimi sistemi hücre dengeleme, sıcaklık izleme ve koruma fonksiyonları ile servis ömrünü maksimize eder.</p></section><section><h3>Operasyonel Sonuç</h3><p>Tek vardiyadan fazla çalışma, yoğun toplama hatları ve yüksek çevrimli yükleme/boşaltma sahaları bu varyantın ideal kullanım alanlarıdır. Enerji altyapısı hazır depolarda gündüz vardiyasında hızlı fırsat şarjı, gece vardiyasında tam şarj stratejisi ile kapasite kaygısı olmaksızın akış sürdürülür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Çok vardiyalı 3PL operasyonlarında kesintisiz akış'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG depolarında yüksek çevrimli yükleme/boşaltma'],
                    ['icon' => 'box-open', 'text' => 'E-ticarette pik saat yönetimi ve yoğun dalga sevkiyat'],
                    ['icon' => 'industry', 'text' => 'Üretim hatlarında vardiya arası malzeme besleme'],
                    ['icon' => 'flask', 'text' => 'Kimya depolarında uzun mesai süreçleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça akışında sürdürülebilir tempo']
                ]
            ],
            [
                'sku' => 'EFLA251S-MAST7134',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFLA251S - 7134 mm Dört Kademeli Direk',
                'short_description' => 'Yüksek raflı depolarda 7.1 m seviyeye erişim. 6.55 m’de 1000 kg residual kapasite ile güvenli istif ve net görüşlü kabinle hassas konumlandırma.',
                'body' => '<section><h2>7134 mm Dördüncü Kademe: Daha Yüksek, Daha Güvenli</h2><p>Yüksek raflı depo yerleşimlerinde erişim limitleri operasyon kapasitesini belirler. 7134 mm’ye ulaşan dört kademeli direk varyantı, taşıyıcı geometrisi ve silindir yerleşimi ile görüş alanını korurken rijitliği artırır. İvmelenme ve duruşlarda yük salınımlarını azaltan hidrolik ayarlarla forklift, üst seviyelerde stabil kalır.</p></section><section><h3>Teknik Etkiler</h3><p>Kaldırma kinematiği, optimize hidrolik valf ve yüksek kesit hortum ile desteklenir; yüksek seviyede bile 1000 kg residual kapasite korunur. 68 dB(A) kabin içi ses seviyesi, hassas yerleştirme anlarında operatörün dikkati dağılmadan çalışmasına yardım eder. 1990 mm dönüş yarıçapı ve 1092 mm genişlik, raf hatları arasında yön değişimi ve düzeltmeleri kolaylaştırır.</p></section><section><h3>Kullanım Sonuçları</h3><p>Dar koridorlu yüksek depolarda, e-ticaret fulfillment merkezlerinde üst katman besleme, içecek ve gıda lojistiğinde üst raf paletleme gibi senaryolarda süreç güvenliğini ve hızını artırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raflı dar koridor depolarda üst katman besleme'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret depolarında üst raf stok yönetimi'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek dağıtım merkezlerinde üst seviye paletleme'],
                    ['icon' => 'snowflake', 'text' => 'Gıda ve soğuk zincir raflarında üst bölme erişimi'],
                    ['icon' => 'store', 'text' => 'Perakende DC’lerinde dalga toplama üst raf erişimi'],
                    ['icon' => 'star', 'text' => 'Hassas konumlandırma gerektiren istasyonlar']
                ]
            ],
            [
                'sku' => 'EFLA251S-MAST4800',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFLA251S - 4800 mm Üç Kademeli Direk',
                'short_description' => 'Standart 4.8 m kaldırma ile düşük tavanlı depolarda optimum denge: hız, görüş ve erişim. Geniş alanlarda seri yükleme/boşaltma için ideal.',
                'body' => '<section><h2>4800 mm Standart Direk: Çevikliğin Altın Noktası</h2><p>Standart üç kademeli direk, iç mekân yükseklik kısıtlarının yoğun olduğu depolarda dengeyi sağlar. 2110 mm kapalı direk yüksekliği tavan altı geçişleri kolaylaştırırken 5838 mm açık yükseklik, çoğu depo raf seviyesine yeterli erişimi sunar. Operatör alanı ve süspansiyon koltuk, uzun vardiyalarda yorgunluğu azaltır.</p></section><section><h3>Teknik Çizgi</h3><p>0.61/0.64 m/s kaldırma hızları ve 17 km/s sürüş hızı, paket elleçleme çevrimlerini hızlandırır. 3664/3864 mm koridor gereksinimleri ve 1990 mm dönüş yarıçapı, karma yerleşim planlarında bile kesintisiz akışa imkân verir. Hız kontrol sistemi ve kaymaz taban, güvenliği artırır.</p></section><section><h3>Uygulama ve Faydalar</h3><p>Inbound–outbound sahalarında sık yön değişimi, rampa yaklaşımı ve kısa mesafe transferleri için ideal yapı sunar. Enerji verimliliği ve düşük gürültü ile paylaşımlı iç mekânlarda konfor sağlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'cart-shopping', 'text' => 'FMCG inbound–outbound sahalarında seri çevrim'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça rampalarında hızlı yaklaşma'],
                    ['icon' => 'industry', 'text' => 'Üretim içi kısa mesafe WIP taşıma'],
                    ['icon' => 'store', 'text' => 'Perakende DC koridorlarında çevik manevra'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında sessiz ve temiz operasyon'],
                    ['icon' => 'flask', 'text' => 'Kimya deposunda kokusuz iç mekân lojistiği']
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
        }

        $this->command->info('✅ Variants eklendi: EFLA251S');
    }
}
