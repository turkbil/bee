<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * F4-202 VARIANTS SEEDER
 *
 * Sorumluluğu: Varyant ürünleri ekler
 * - Her varyant için UNIQUE CONTENT:
 *   * title (İXTİF + Varyant adı)
 *   * slug (Türkçe karakterli)
 *   * short_description (30-50 kelime)
 *   * body (HTML içerik)
 *   * use_cases (6 senaryo)
 *
 * ÖNEMLİ NOTLAR:
 * - Short description: 30-50 kelime, AÇIKLAYICI olmalı
 * - Long description: Bu varyantın ÖZEL avantajlarını anlatan unique HTML
 * - Use cases: Bu varyanta ÖZEL 6 kullanım senaryosu
 * - features, faq_data, technical_specs master'dan inherit edilir
 */
class F4_202_Transpalet_Variants extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 1. MASTER PRODUCT KONTROLÜ
        // ========================================

        $masterProduct = DB::table('shop_products')->where('sku', 'F4-202')->first();

        if (!$masterProduct) {
            echo "❌ Master ürün bulunamadı! Önce Master seeder'ı çalıştırın.\n";
            return;
        }

        // ========================================
        // 2. VARYANT TANIMLARI
        // ========================================

        $variants = [
            // ✅ VARYANT 1: 1150mm Çatal (Standart EUR Palet)
            [
                'sku' => 'F4-202-1150',
                'variant_type' => 'fork-length',
                'title' => 'İXTİF F4 202 - 1150mm Çatal',
                'short_description' => 'Standart 1150mm çatal uzunluğu ile EUR palet (1200x800mm) taşımada yüksek performans. Endüstriyel depolarda dar koridor operasyonlarında mükemmel manevra kabiliyeti sunan, 2.5 ton kapasiteli güç platformu.',

                'body' => <<<'HTML'
<section class="variant-intro">
<p><strong>1150mm çatal uzunluğu, F4 202'nin en yaygın kullanılan varyantıdır.</strong></p>
<p>EUR palet standardına tam uyum sağlar ve dar koridor operasyonlarında maksimum verimlilik sunar.</p>
</section>

<section class="variant-details">
<h4>Neden 1150mm Çatal?</h4>
<ul>
<li>EUR palet (1200x800mm) için ideal boyut</li>
<li>Dar koridorlarda kolay manevra</li>
<li>Yüksek stabilite ve güvenlik</li>
<li>Geniş uygulama yelpazesi</li>
</ul>

<h4>Teknik Özellikler</h4>
<p>Bu varyant, standart F4 202 teknik özelliklerine sahiptir. Çatal uzunluğu 1150mm olarak optimize edilmiştir.</p>
</section>
HTML,

                'use_cases' => [
                    'Endüstriyel depolarda EUR palet (1200x800mm) sevkiyat operasyonları',
                    'Otomotiv yedek parça depolarında standart palet yönetimi',
                    'Gıda lojistik merkezlerinde soğuk hava deposu malzeme transferi',
                    'İlaç ve kozmetik fabrikalarında temiz oda palet taşıma',
                    'Elektronik üretim tesislerinde hassas ekipman paletleme',
                    'Perakende zincir depolarında stok yönetimi ve sevkiyat',
                ],
            ],

            // ✅ VARYANT 2: 1220mm Çatal (Endüstriyel Palet)
            [
                'sku' => 'F4-202-1220',
                'variant_type' => 'fork-length',
                'title' => 'İXTİF F4 202 - 1220mm Çatal',
                'short_description' => 'Uzun 1220mm çatal ile endüstriyel palet (1200x1000mm) ve IBC tank taşımada üstün performans. Kimya, inşaat ve ağır sanayi uygulamalarında güvenli ve stabil operasyon sağlayan güçlü varyant.',

                'body' => <<<'HTML'
<section class="variant-intro">
<p><strong>1220mm çatal uzunluğu, endüstriyel paletler için optimize edilmiştir.</strong></p>
<p>IBC tank ve ağır yük uygulamalarında maksimum stabilite sağlar.</p>
</section>

<section class="variant-details">
<h4>Endüstriyel Uygulamalar İçin</h4>
<ul>
<li>Endüstriyel palet (1200x1000mm) tam destek</li>
<li>IBC tank taşımada yüksek stabilite</li>
<li>Ağır yük dengesi ve güvenlik</li>
<li>Kimya ve inşaat sektöründe ideal</li>
</ul>

<h4>IBC Tank Taşıma</h4>
<p>1000 litrelik IBC tankların güvenli taşınması için uzun çatal gereklidir. Bu varyant, tank paletlerinin tam desteğini sağlar.</p>
</section>
HTML,

                'use_cases' => [
                    'Kimya tesislerinde IBC tank (1000L) ve varil paletlerinin güvenli taşınması',
                    'İnşaat malzemeleri depolarında ağır yapı malzemesi paletleme',
                    'Boya ve reçine üretim tesislerinde endüstriyel palet yönetimi',
                    'Metal işleme fabrikalarında çelik levha ve profil paletlerinin transferi',
                    'Enerji santrallerinde yedek parça ve ekipman paletleme operasyonları',
                    'Madencilik lojistiğinde ağır ekipman aksesuarlarının taşınması',
                ],
            ],

            // ✅ VARYANT 3: Extended Battery (Uzun Vardiya)
            [
                'sku' => 'F4-202-EXT-BAT',
                'variant_type' => 'battery',
                'title' => 'İXTİF F4 202 - Extended Battery',
                'short_description' => 'Genişletilmiş 4 modül Li-Ion batarya sistemi (200Ah) ile 12-16 saat kesintisiz operasyon kapasitesi. Çift vardiya ve yoğun operasyonlar için tasarlanmış, uzun çalışma süresi gerektiren uygulamalarda ideal çözüm.',

                'body' => <<<'HTML'
<section class="variant-intro">
<p><strong>Extended Battery varyantı, uzun vardiya operasyonları için geliştirilmiştir.</strong></p>
<p>4 modül (4x 24V/25Ah = 200Ah) Li-Ion batarya sistemi ile 12-16 saat kesintisiz çalışma sunar.</p>
</section>

<section class="variant-details">
<h4>Uzun Vardiya Avantajları</h4>
<ul>
<li>12-16 saat kesintisiz operasyon</li>
<li>Çift vardiya için ideal</li>
<li>Ara şarj ihtiyacı yok</li>
<li>Yüksek performans sürdürülebilirliği</li>
</ul>

<h4>Batarya Sistemi</h4>
<p>4 adet 24V/25Ah Li-Ion modül, toplam 200Ah kapasite sağlar. Her modül çıkarılabilir ve değiştirilebilir.</p>

<h4>Operasyonel Verimlilik</h4>
<p>Çift vardiya çalışan depolarda, ara şarj ihtiyacı olmadan 12-16 saat kesintisiz operasyon sağlar. Bu, lojistik maliyetlerinizi düşürür ve verimliliği artırır.</p>
</section>
HTML,

                'use_cases' => [
                    '24 saat aktif e-ticaret fulfillment merkezlerinde çift vardiya operasyonları',
                    'Yoğun perakende depolarda gece-gündüz kesintisiz malzeme akışı',
                    'Liman ve terminal operasyonlarında uzun vardiya yükleme-boşaltma',
                    'Havaalanı kargo terminallerinde sürekli palet taşıma operasyonları',
                    'Büyük ölçekli üretim tesislerinde çift vardiya malzeme besleme',
                    '3PL lojistik merkezlerinde peak sezon yoğun operasyon desteği',
                ],
            ],
        ];

        // ========================================
        // 3. VARYANTLARI EKLE
        // ========================================

        $variantCount = 0;

        foreach ($variants as $v) {
            $variantId = DB::table('shop_products')->insertGetId([
                // Identifiers
                'sku' => $v['sku'],

                // Variant System
                'parent_product_id' => $masterProduct->product_id,
                'is_master_product' => false,
                'variant_type' => $v['variant_type'],

                // Basic Info (UNIQUE CONTENT)
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'body' => json_encode(['tr' => $v['body']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),

                // Relations (same as master)
                'category_id' => $masterProduct->category_id,
                'brand_id' => $masterProduct->brand_id,

                // Type & Condition (same as master)
                'product_type' => 'physical',
                'condition' => 'new',

                // Pricing (same as master or null for price_on_request)
                'price_on_request' => false,
                'base_price' => $masterProduct->base_price,
                'currency' => 'TRY',

                // Stock (optional, can be null)
                'stock_tracking' => true,
                'current_stock' => 0, // Varyantlar için stok ayrı tutulabilir
                'allow_backorder' => false,

                // Display & Status
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),

                // Timestamps
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $variantCount++;
            echo "  ✅ Varyant eklendi: {$v['sku']} (ID: {$variantId})\n";
        }

        // ========================================
        // 4. ÖZET
        // ========================================

        echo "\n";
        echo "🎉 F4 202 Transpalet Varyantları Tamamlandı!\n";
        echo "📊 İstatistik:\n";
        echo "   - Master Product: F4-202 (ID: {$masterProduct->product_id})\n";
        echo "   - Varyantlar: {$variantCount}\n";
        echo "   - Toplam: " . ($variantCount + 1) . " ürün\n";
        echo "\n";
        echo "📞 İletişim: 0216 755 3 555 | info@ixtif.com\n";
        echo "\n";
    }
}
