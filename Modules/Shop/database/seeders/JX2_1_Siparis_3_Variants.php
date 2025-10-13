<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX2_1_Siparis_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'JX2-1')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: JX2-1');
            return;
        }

        $variants = [
            [
                'sku' => 'JX2-1-AGM-224',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF JX2-1 AGM 224Ah - Düşük Seviye Sipariş Toplayıcı',
                'short_description' => 'AGM 224Ah batarya ile hızlı şarj, düşük bakım ve temiz kullanım sunan JX2-1 varyantı; 5 mph hız, 72” kaldırma ve 31.5” genişlikle dar koridorlarda yüksek verim sağlar.',
                'body' => '<section><h3>AGM 224Ah: Temiz, Hızlı ve Öngörülebilir Enerji</h3><p>AGM 224Ah konfigürasyonu, sızdırmaz yapı ve düşük bakım gereksinimiyle yoğun vardiyalarda temiz operasyon sağlar. 24V mimaride AC sürüş ile 5 mph hızlara pürüzsüz ulaşılır; rejeneratif frenleme enerji geri kazanımıyla çevrim sürelerini optimize eder. 72” kaldırma yüksekliği ve 42” çatal, alt seviye raflardan sipariş toplarken operatörün çatal önde/arkada duruşlarında doğal bir akış sağlar. 31.5” gövde genişliği, 111.5” sağ açı istif koridor değeri ve 58” dönüş yarıçapı ile dar alanlarda güvenli manevra mümkündür.</p></section><section><h3>Teknik Odak</h3><p>Makine 2.5 kW sürüş ve 3 kW kaldırma motorlarıyla 25.6/31.5 fpm kaldırma hızlarını sağlar. Poly sürüş, yük ve kaster tekerlekleri zeminde sessiz ve titreşimsiz ilerler. Operatör bölmesi 61.4” yükseklik ve 48” yükseltilmiş ayakta durma seviyesi ile görüşü güçlendirir. AGM kimya, düzenli şarj rejimleriyle çevrimiçi kalma süresini artırır ve klasik kurşun-asitlere kıyasla daha temiz bir bakım rutini sunar. 25A veya 40A şarj cihazı seçenekleri, vardiya planınıza göre enerji akışını esnekleştirir.</p></section><section><h3>Operasyonda Katma Değer</h3><p>AGM varyantı, e-ticaret ve perakende dağıtım merkezlerinde hızın kritik olduğu toplama hatlarında öne çıkar. Düşük ses düzeyi (74 dB(A)) ve elektromanyetik park freni, operatör güvenini pekiştirir. İç mekân ve düz zemin kullanımına özel tasarım, standardize edilmiş güvenlik prosedürleriyle uyumludur. Konfigürasyonu; 42” çatal, 22” çatal aralığı ve 2” yerden yükseklik gibi ölçüler tamamlar.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Yoğun e-ticaret hatlarında zemin seviye hızlı toplama'],
                    ['icon' => 'store', 'text' => 'Perakende DC’lerde vardiya arası sipariş hazırlama'],
                    ['icon' => 'warehouse', 'text' => '3PL koridorlarında dar alan manevrası'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG cross-dock ve konsolidasyon'],
                    ['icon' => 'pills', 'text' => 'İlaç depolarında temiz ve kontrollü operasyon'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkışında sessiz akış']
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
        $this->command->info('✅ Variants eklendi: JX2-1 (1 varyant)');
    }
}
