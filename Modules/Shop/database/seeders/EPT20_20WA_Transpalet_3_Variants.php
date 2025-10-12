<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT20_20WA_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPT20-20WA')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı'); return; }

        $variants = [
            
            [
                'sku' => 'EPT20-20WA-LI',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EPT20-20WA Li-Ion',
                'short_description' => 'Li-Ion batarya ile hızlı şarj ve modüler enerji yönetimi; kısa duruş, uzun devriye.',
                'long_description' => '<section><h2>Li-Ion Enerji Varyantı: Vardiya Sürekliliği için Enerji Esnekliği</h2><p>EPT20-20WA platformu, enerji mimarisini operasyon ritminize uyarlamanız için iki farklı seçenek sunar. Bu varyantta odak, <strong>24V 205 Ah Li-Ion</strong> yapısı ile çevrim sürelerinin dengelenmesi ve arıza dışı beklemelerin azaltılmasıdır. Dar koridorlarda çeviklik, rampalarda kontrollü kalkış ve tekrarlı palet akışlarında stabil hızlar öne çıkar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>AC sürüş sistemi (1.1 kW) ve 0.84 kW kaldırma motoru, 5.0/5.5 km/s hız bandıyla desteklenir. 55×170×1150 mm çatal ve 685 mm çatallar arası genişlik EUR paletlere hizalı giriş sağlar. Poliüretan tekerlek seti, düşük gürültü ve titreşimle zemin dostu hareket sunar. Tiller konumuna bağlı otomatik hız düşürme ve elektromanyetik fren birlikte güvenlik katmanını güçlendirir.</p></section><section><h3>Operasyonel Etki</h3><p>Yoğun vardiyalarda yükleme-rampa geçişleri, cross-dock noktaları ve ara stok alanlarında çevrim sürelerinin tutarlılığı üretkenliği belirler. Bu varyant, enerji değişim ya da şarj senaryolarını basitleştirir; hat besleme ve konsolidasyon adımlarında operatör konforu artırılır.</p></section><section><h3>Sonuç</h3><p>Kurulum ve geçiş süreleri optimize edilerek toplam sahip olma maliyeti düşürülür; filo kararlılığı ve vardiya sürekliliği güçlenir.</p></section>',
                'use_cases' => [['icon' => 'box-open', 'text' => 'Fulfillment hatlarında yoğun besleme'],
                ['icon' => 'warehouse', 'text' => 'Cross-dock istasyonlarında hızlı transfer'],
                ['icon' => 'car', 'text' => 'Otomotiv yan sanayi palet akışı'],
                ['icon' => 'flask', 'text' => 'Kimya depolarında güvenli iç lojistik'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış operasyonları'],
                ['icon' => 'industry', 'text' => 'WIP taşıma ve hücre besleme']]
            ],
            
            [
                'sku' => 'EPT20-20WA-LA',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EPT20-20WA Kurşun-Asit',
                'short_description' => 'Kurşun-asit aküyle sağlam ve ekonomik enerji; planlı şarjla kesintisiz vardiya.',
                'long_description' => '<section><h2>Kurşun-Asit Enerji Varyantı: Vardiya Sürekliliği için Enerji Esnekliği</h2><p>EPT20-20WA platformu, enerji mimarisini operasyon ritminize uyarlamanız için iki farklı seçenek sunar. Bu varyantta odak, <strong>24V 210 Ah Kurşun-asit</strong> yapısı ile çevrim sürelerinin dengelenmesi ve arıza dışı beklemelerin azaltılmasıdır. Dar koridorlarda çeviklik, rampalarda kontrollü kalkış ve tekrarlı palet akışlarında stabil hızlar öne çıkar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>AC sürüş sistemi (1.1 kW) ve 0.84 kW kaldırma motoru, 5.0/5.5 km/s hız bandıyla desteklenir. 55×170×1150 mm çatal ve 685 mm çatallar arası genişlik EUR paletlere hizalı giriş sağlar. Poliüretan tekerlek seti, düşük gürültü ve titreşimle zemin dostu hareket sunar. Tiller konumuna bağlı otomatik hız düşürme ve elektromanyetik fren birlikte güvenlik katmanını güçlendirir.</p></section><section><h3>Operasyonel Etki</h3><p>Yoğun vardiyalarda yükleme-rampa geçişleri, cross-dock noktaları ve ara stok alanlarında çevrim sürelerinin tutarlılığı üretkenliği belirler. Bu varyant, enerji değişim ya da şarj senaryolarını basitleştirir; hat besleme ve konsolidasyon adımlarında operatör konforu artırılır.</p></section><section><h3>Sonuç</h3><p>Kurulum ve geçiş süreleri optimize edilerek toplam sahip olma maliyeti düşürülür; filo kararlılığı ve vardiya sürekliliği güçlenir.</p></section>',
                'use_cases' => [['icon' => 'store', 'text' => 'Perakende DC raf arası taşıma'],
                ['icon' => 'pills', 'text' => 'İlaç lojistiğinde hassas akış'],
                ['icon' => 'tshirt', 'text' => 'Tekstil koli-paleti transfer'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG yüksek tempolu toplama'],
                ['icon' => 'couch', 'text' => 'Mobilya depo içi sevk'],
                ['icon' => 'hammer', 'text' => 'Yapı market inbound-outbound']]
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
            $this->command->info('✅ Varyant: ' . $v['sku']);
        }
    }
}
