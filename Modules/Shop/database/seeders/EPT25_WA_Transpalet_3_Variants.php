<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EPT25_WA_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'EPT25-WA')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı'); return; }

        $variants = [
            
            [
                'sku' => 'EPT25-WA-LI',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EPT25-WA Li-Ion',
                'short_description' => '2.5 ton sınıfta Li-Ion ile hızlanan çevrimler ve düşük enerji kesintisi.',
                'long_description' => '<section><h2>Li-Ion Enerji Varyantı: Vardiya Sürekliliği için Enerji Esnekliği</h2><p>EPT25-WA platformu, enerji mimarisini operasyon ritminize uyarlamanız için iki farklı seçenek sunar. Bu varyantta odak, <strong>24V 205 Ah Li-Ion</strong> yapısı ile çevrim sürelerinin dengelenmesi ve arıza dışı beklemelerin azaltılmasıdır. Dar koridorlarda çeviklik, rampalarda kontrollü kalkış ve tekrarlı palet akışlarında stabil hızlar öne çıkar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>AC sürüş sistemi (1.1 kW) ve 0.84 kW kaldırma motoru, 5.0/5.5 km/s hız bandıyla desteklenir. 55×170×1150 mm çatal ve 685 mm çatallar arası genişlik EUR paletlere hizalı giriş sağlar. Poliüretan tekerlek seti, düşük gürültü ve titreşimle zemin dostu hareket sunar. Tiller konumuna bağlı otomatik hız düşürme ve elektromanyetik fren birlikte güvenlik katmanını güçlendirir.</p></section><section><h3>Operasyonel Etki</h3><p>Yoğun vardiyalarda yükleme-rampa geçişleri, cross-dock noktaları ve ara stok alanlarında çevrim sürelerinin tutarlılığı üretkenliği belirler. Bu varyant, enerji değişim ya da şarj senaryolarını basitleştirir; hat besleme ve konsolidasyon adımlarında operatör konforu artırılır.</p></section><section><h3>Sonuç</h3><p>Kurulum ve geçiş süreleri optimize edilerek toplam sahip olma maliyeti düşürülür; filo kararlılığı ve vardiya sürekliliği güçlenir.</p></section>',
                'use_cases' => [['icon' => 'box-open', 'text' => 'E-ticaret konsolidasyon bölgeleri'],
                ['icon' => 'warehouse', 'text' => '3PL cross-dock ve ayırma'],
                ['icon' => 'car', 'text' => 'Ağır yedek parça paletleme'],
                ['icon' => 'print', 'text' => 'Ambalaj ve matbaa sevkiyatı'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek üretiminde hat besleme'],
                ['icon' => 'industry', 'text' => 'Ağır sanayi WIP lojistiği']]
            ],
            
            [
                'sku' => 'EPT25-WA-LA',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EPT25-WA Kurşun-Asit',
                'short_description' => 'Planlı şarj döngüleriyle 2.5 ton sınıfta dayanıklı performans ve kararlılık.',
                'long_description' => '<section><h2>Kurşun-Asit Enerji Varyantı: Vardiya Sürekliliği için Enerji Esnekliği</h2><p>EPT25-WA platformu, enerji mimarisini operasyon ritminize uyarlamanız için iki farklı seçenek sunar. Bu varyantta odak, <strong>24V 210 Ah Kurşun-asit</strong> yapısı ile çevrim sürelerinin dengelenmesi ve arıza dışı beklemelerin azaltılmasıdır. Dar koridorlarda çeviklik, rampalarda kontrollü kalkış ve tekrarlı palet akışlarında stabil hızlar öne çıkar.</p></section><section><h3>Teknik Ayrıntılar</h3><p>AC sürüş sistemi (1.1 kW) ve 0.84 kW kaldırma motoru, 5.0/5.5 km/s hız bandıyla desteklenir. 55×170×1150 mm çatal ve 685 mm çatallar arası genişlik EUR paletlere hizalı giriş sağlar. Poliüretan tekerlek seti, düşük gürültü ve titreşimle zemin dostu hareket sunar. Tiller konumuna bağlı otomatik hız düşürme ve elektromanyetik fren birlikte güvenlik katmanını güçlendirir.</p></section><section><h3>Operasyonel Etki</h3><p>Yoğun vardiyalarda yükleme-rampa geçişleri, cross-dock noktaları ve ara stok alanlarında çevrim sürelerinin tutarlılığı üretkenliği belirler. Bu varyant, enerji değişim ya da şarj senaryolarını basitleştirir; hat besleme ve konsolidasyon adımlarında operatör konforu artırılır.</p></section><section><h3>Sonuç</h3><p>Kurulum ve geçiş süreleri optimize edilerek toplam sahip olma maliyeti düşürülür; filo kararlılığı ve vardiya sürekliliği güçlenir.</p></section>',
                'use_cases' => [['icon' => 'store', 'text' => 'Perakende DC inbound/putaway'],
                ['icon' => 'pills', 'text' => 'Medikal ekipman palet akışı'],
                ['icon' => 'tshirt', 'text' => 'Hazır giyim sevk hazırlığı'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim ürünleri toplama'],
                ['icon' => 'couch', 'text' => 'Hacimli ürün yükleme'],
                ['icon' => 'hammer', 'text' => 'DIY ve yapı market operasyonları']]
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
