<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F4_201_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'F4-201')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı (F4-201)');
            return;
        }

        $variants = [
            [
                'sku' => 'F4-201-1150-560',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF F4 201 - 1150×560 mm Çatal',
                'short_description' => 'Standart 1150 mm çatal boyu ve 560 mm çatal aralığı ile EUR paletlerde maksimum çeviklik. 48V Li‑Ion enerji ve l2=400 mm gövde sayesinde dar koridorda hızlı akış.',
                'body' => '<section><h3>1150×560 mm: EUR palet standardında hız</h3><p>Bu varyant, 1150 mm çatal boyu ve 560 mm çatal aralığıyla EUR palet ölçülerine birebir uyum sağlar. l2=400 mm kompakt gövde, raf arası manevrada operatöre esneklik verir. 48V mimari, 2.0 tona kadar yükleri verimli biçimde hareket ettirir. Poliüretan teker kombinasyonu sessiz ve iz bırakmayan bir akış sunar.</p></section><section><h3>Enerji ve ergonomi</h3><p>2×24V/20Ah Li‑Ion modüller tak‑çıkar özelliktedir; fırsat şarjıyla vardiya devamlılığı korunur. Elektromanyetik fren ve sağlam kumanda kolu güvenli ve hatasız yönetim sağlar. Platform F mimarisi bakım süreçlerini kısaltır, parça ortaklığıyla işletme maliyetini düşürür.</p></section><section><h3>Uygulamalar</h3><p>Bu ölçü, e‑ticaret hat besleme, perakende sevk hazırlığı ve 3PL çapraz sevkiyat istasyonlarında akışı hızlandırır.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR palet toplama ve sevk hazırlığı'],
                    ['icon' => 'store', 'text' => 'Perakende raf arası replenishment'],
                    ['icon' => 'warehouse', 'text' => '3PL cross‑dock operasyonları'],
                    ['icon' => 'cart-shopping', 'text' => 'Hızlı tüketim ürünleri ayrıştırma'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı WIP akışları'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça ara stok taşıma']
                ]
            ],
            [
                'sku' => 'F4-201-1150-685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF F4 201 - 1150×685 mm Çatal',
                'short_description' => 'Geniş 685 mm çatal aralığı ile farklı palet tabanlarına uyum. 48V güç ve 2.0 ton kapasite, dengesiz yüklerde stabil çalışma için idealdir.',
                'body' => '<section><h3>1150×685 mm: geniş tabanlı paletler için</h3><p>685 mm çatal aralığı, geniş tabanlı paletlerde ek stabilite sağlar. 1150 mm boy, standart palet derinlikleriyle uyumludur. 48V sistem güç kaybını minimize eder, 2 tonluk nominal kapasite işletme hızını korur.</p></section><section><h3>Güvenlik ve dayanıklılık</h3><p>Elektromanyetik fren, sağlam şasi ve opsiyonel stabilizasyon tekerleri düzensiz zeminlerde bile güvenilir idare sunar. BLDC sürüş kontrolü akıcı hızlanma sağlar.</p></section><section><h3>Operasyon senaryoları</h3><p>Gıda‑içecek depolarında soğuk oda giriş‑çıkışı, mobilya ve beyaz eşya dağıtım merkezlerinde geniş ebatlı yüklerde öne çıkar.</p></section>',
                'use_cases' => [
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş‑çıkış transferi'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasası paletleme'],
                    ['icon' => 'couch', 'text' => 'Mobilya yarı mamul taşıma'],
                    ['icon' => 'tv', 'text' => 'Beyaz eşya depolama alanları'],
                    ['icon' => 'flask', 'text' => 'Kimya ürünleri toplama'],
                    ['icon' => 'warehouse', 'text' => '3PL yoğun sevkiyat peronları']
                ]
            ],
            [
                'sku' => 'F4-201-1220-560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F4 201 - 1220 mm Çatal (560 mm aralık)',
                'short_description' => '1220 mm uzatılmış çatal ile uzun palet ve kutularda daha dengeli kaldırma. 560 mm aralıkla EUR palet adaptasyonu korunur, dar koridorda çeviklik sürer.',
                'body' => '<section><h3>1220 mm uzatılmış çatal: daha uzun yükler</h3><p>1220 mm çatal, uzun kasalar ve uzayan yüklerde esneklik kazandırır. 560 mm aralık EUR paletlere uyumu korurken, l2=400 mm gövdeyle çeviklikten ödün verilmez. 2×24V/20Ah Li‑Ion enerji modülleri ile vardiya temposu yakalanır.</p></section><section><h3>Performans</h3><p>4.5/5 km/s hız, 0.016/0.020 m/s kaldırma ve 0.058/0.046 m/s indirme değerleri üretkenliği destekler. Poliüretan tekerler sessiz ve iz bırakmayan operasyon sunar.</p></section><section><h3>Kullanım alanı</h3><p>Ambalaj, matbaa ve yapı market lojistiğinde uzun koliler, rulolar ve geniş kutular için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'print', 'text' => 'Matbaa rulo ve paletli kâğıt'],
                    ['icon' => 'hammer', 'text' => 'Yapı market uzun ürün transferi'],
                    ['icon' => 'box-open', 'text' => 'E‑ticaret uzun kutu sevkiyatı'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil rulo kumaş akışı'],
                    ['icon' => 'industry', 'text' => 'Üretim hücresi besleme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG karma palet hazırlığı']
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
            $this->command->info('✅ Variant: ' . $v['sku'] . ' güncellendi');
        }
    }
}
