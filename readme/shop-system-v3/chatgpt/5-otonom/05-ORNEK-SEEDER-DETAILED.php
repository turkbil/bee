<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F4-202 DETAILED SEEDER
 *
 * Sorumluluğu: Detaylı içerik ekler
 * - long_description (HTML)
 * - features (array)
 * - faq_data (array)
 * - technical_specs (object)
 * - competitive_advantages, target_industries, use_cases
 * - primary_specs, highlighted_features
 */
class F4_202_Transpalet_Detailed extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 1. MASTER PRODUCT KONTROLÜ
        // ========================================

        $product = DB::table('shop_products')->where('sku', 'F4-202')->first();

        if (!$product) {
            echo "❌ Master ürün bulunamadı! Önce Master seeder'ı çalıştırın.\n";
            return;
        }

        // ========================================
        // 2. LONG DESCRIPTION (HTML)
        // ========================================

        $longDescription = <<<'HTML'
<section class="marketing-intro">
<p><strong>F4 202, ağır yük taşımada yeni standartlar belirleyen güç platformudur.</strong></p>
<p>2.5 ton taşıma kapasitesi ile endüstriyel paletlerin taşınmasında ideal çözümdür. 48V Li-Ion akü sistemi ile uzun vardiya performansı sunar.</p>
<ul>
<li><strong>Güçlü Motor</strong> – 1.1 kW sürüş motoru ile rampalarda zorlanmadan çıkar.</li>
<li><strong>Dayanıklı Yapı</strong> – 180 kg servis ağırlığı ile yüksek stabilite sağlar.</li>
<li><strong>Li-Ion Akü</strong> – Tak-çıkar batarya sistemi ile kesintisiz operasyon.</li>
<li><strong>Geniş Çatal Seçenekleri</strong> – 1150mm'den 1500mm'ye kadar çatal uzunlukları.</li>
</ul>
</section>

<section class="marketing-body">
<h3>Ağır Yük Taşımada Güvenilir Ortak: F4 202</h3>
<p>Standart teslimat paketinde 2 adet 24V/25Ah Li-Ion modül bulunur. İhtiyaca göre 4 adede kadar modül ekleyerek vardiya süresini uzatabilirsiniz.</p>

<p><strong>48V BLDC motorlu sürüş sistemi</strong> sayesinde F4 202, %10 rampalarda yükle bile zorlanmadan çıkar. 1.1 kW sürüş motoru ve 0.85 kW kaldırma motoru kombinasyonu, elektromanyetik fren ile birleşerek size acil durumlarda bile tam kontrol sağlar.</p>

<h4>İXTİF Farkı: Satış Sonrası Tam Destek</h4>
<p>İXTİF'in <strong>ikinci el, kiralık, yedek parça ve teknik servis</strong> ekosistemi ile F4 202 yatırımınız tam koruma altında.</p>

<h4>İletişim</h4>
<p><strong>Telefon:</strong> 0216 755 3 555<br>
<strong>E-posta:</strong> info@ixtif.com<br>
<strong>Firma:</strong> İXTİF İç ve Dış Ticaret A.Ş.</p>
</section>
HTML;

        // ========================================
        // 3. FEATURES (Array)
        // ========================================

        $features = [
            '48V 100Ah Li-Ion akü sistemi (2 modül)',
            '2.5 ton kaldırma kapasitesi',
            '1.1 kW BLDC sürüş motoru',
            '0.85 kW kaldırma motoru',
            'Elektromanyetik fren sistemi',
            'Polyurethane tekerlek sistemi',
            'Tak-çıkar batarya modülleri',
            'Ergonomik kontrol kolu',
            '%10 rampa çıkma kapasitesi',
            '1450 mm dönüş yarıçapı',
        ];

        // ========================================
        // 4. FAQ DATA (Array)
        // ========================================

        $faqData = [
            [
                'question' => 'F4 202 ne kadar süre çalışır?',
                'answer' => 'Standart 2x 24V/25Ah batarya ile orta yoğunluklu kullanımda 6-8 saat çalışır. 4 modül ile 12-14 saate kadar uzatılabilir.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Hangi palet tiplerinde kullanılabilir?',
                'answer' => 'EUR palet (1200x800mm), endüstriyel palet (1200x1000mm) ve özel boyutlu paletlerde kullanılabilir.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Garanti süresi nedir?',
                'answer' => 'Standart üretici garantisi ile teslim edilir. İXTİF ek garanti paketleri sunar.',
                'sort_order' => 3,
            ],
            [
                'question' => 'İkinci el veya kiralık seçeneği var mı?',
                'answer' => 'Evet, İXTİF hem ikinci el garanti belgeleriyle yenilenmiş modeller, hem de kısa ve orta vadeli kiralama seçenekleri sunar.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Teknik servis desteği var mı?',
                'answer' => 'İXTİF, Türkiye genelinde 7/24 mobil teknik servis hizmeti sunar. 0216 755 3 555 numaralı hattımızdan acil servis talebi oluşturabilirsiniz.',
                'sort_order' => 5,
            ],
        ];

        // ========================================
        // 5. TECHNICAL SPECS (Object)
        // ========================================

        $technicalSpecs = [
            'Kapasite' => '2.5 Ton (2500 kg)',
            'Kaldırma Yüksekliği' => '205 mm',
            'Akü' => '48V 100Ah Li-Ion (2 modül)',
            'Boyutlar' => '1650 x 690 x 1950 mm (U x G x Y)',
            'Ağırlık' => '180 kg',
            'Çatal Uzunluğu' => '1150 mm (standart)',
            'Çatal Genişliği' => '160 / 540 mm',
            'Sürüş Motoru' => '1.1 kW BLDC',
            'Kaldırma Motoru' => '0.85 kW',
            'Maksimum Hız (Yüklü)' => '4.8 km/h',
            'Maksimum Hız (Yüksüz)' => '5.3 km/h',
            'Rampa Performansı (Yüklü)' => '%10',
            'Rampa Performansı (Yüksüz)' => '%18',
            'Dönüş Yarıçapı' => '1450 mm',
            'Tekerlek Tipi' => 'Polyurethane',
            'Fren Sistemi' => 'Elektromanyetik',
        ];

        // ========================================
        // 6. USE CASES (Array)
        // ========================================

        $useCases = [
            'Endüstriyel üretim tesislerinde ağır palet taşıma operasyonları',
            'İnşaat malzemeleri depolarında yüksek tonajlı yük yönetimi',
            'Otomotiv fabrikalarında motor ve parça paletlerinin taşınması',
            'Metal işleme tesislerinde ağır hammadde transferi',
            'Kimya endüstrisinde IBC tank ve varil taşıma operasyonları',
            'Soğuk zincir lojistiğinde ağır gıda paletlerinin yönetimi',
        ];

        // ========================================
        // 7. COMPETITIVE ADVANTAGES (Array)
        // ========================================

        $competitiveAdvantages = [
            '2.5 ton kapasite ile segmentinde üstün taşıma gücü',
            '48V Li-Ion akü sistemi ile düşük işletme maliyeti',
            '1.1 kW güçlü motor ile rampalarda yüksek performans',
            'Tak-çıkar batarya ile 7/24 kesintisiz operasyon',
            'İXTİF Türkiye genelinde hızlı servis ağı',
            '180 kg dayanıklı gövde ile uzun ömür garantisi',
        ];

        // ========================================
        // 8. TARGET INDUSTRIES (Array)
        // ========================================

        $targetIndustries = [
            'Endüstriyel üretim tesisleri',
            'İnşaat malzemeleri depoları',
            'Otomotiv fabrikaları',
            'Metal işleme tesisleri',
            'Kimya endüstrisi',
            'Soğuk zincir lojistik',
            'Ağır makine üretimi',
            'Döküm ve hadde tesisleri',
        ];

        // ========================================
        // 9. PRIMARY SPECS (Array)
        // ========================================

        $primarySpecs = [
            ['label' => 'Yük Kapasitesi', 'value' => '2.5 Ton'],
            ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V'],
            ['label' => 'Çatal Uzunluğu', 'value' => '1150 mm'],
            ['label' => 'Servis Ağırlığı', 'value' => '180 kg'],
        ];

        // ========================================
        // 10. DATABASE UPDATE
        // ========================================

        DB::table('shop_products')->where('product_id', $product->product_id)->update([
            'long_description' => json_encode(['tr' => $longDescription], JSON_UNESCAPED_UNICODE),
            'features' => json_encode($features, JSON_UNESCAPED_UNICODE),
            'faq_data' => json_encode($faqData, JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode($technicalSpecs, JSON_UNESCAPED_UNICODE),
            'use_cases' => json_encode($useCases, JSON_UNESCAPED_UNICODE),
            'competitive_advantages' => json_encode($competitiveAdvantages, JSON_UNESCAPED_UNICODE),
            'target_industries' => json_encode($targetIndustries, JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode($primarySpecs, JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        echo "✅ Detailed Content eklendi: F4-202\n";
        echo "📄 Long Description: " . strlen($longDescription) . " karakter\n";
        echo "📋 Features: " . count($features) . " özellik\n";
        echo "❓ FAQ: " . count($faqData) . " soru\n";
        echo "🔧 Technical Specs: " . count($technicalSpecs) . " alan\n";
        echo "\n";
    }
}
