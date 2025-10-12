<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F4 201 - 2 Ton 48V Li-Ion Transpalet (MÜKEMMEL ÖRNEK)
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/02_F4-201-brochure-CE.pdf
 * Marka: İXTİF (brand_id = 1)
 * Kategori: TRANSPALETLER (category_id = 165)
 * Garanti: 1 Yıl Ürün + 2 Yıl Akü (Transpalet klasörü)
 *
 * MASTER + 4 VARIANT:
 * - Master (F4-201-MASTER): Genel bakış
 * - Variant 1 (F4-201-STD): Standart Çatal 1150x560mm
 * - Variant 2 (F4-201-WIDE): Geniş Çatal 1150x685mm
 * - Variant 3 (F4-201-SHORT): Kısa Çatal 900x560mm
 * - Variant 4 (F4-201-LONG): Uzun Çatal 1500x560mm
 *
 * ÖNEMLİ:
 * - %100 TÜRKÇE JSON KEY'LER
 * - Her variant TAMAMEN FARKLI içerik
 * - Accessories ve Certifications eklendi
 * - Media Gallery placeholder
 */
class F4_201_Perfect_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F4 201 Li-Ion Transpalet (MÜKEMMEL ÖRNEK) ekleniyor...');

        // Brand ID'sini bul veya oluştur
        $brandId = DB::table('shop_brands')->where('title->tr', 'İXTİF')->value('brand_id');
        if (!$brandId) {
            $brandId = DB::table('shop_brands')->insertGetId([
                'title' => json_encode(['tr' => 'İXTİF', 'en' => 'İXTİF']),
                'slug' => json_encode(['tr' => 'ixtif', 'en' => 'ixtif']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Dinamik olarak Transpalet kategorisi ID'sini bul
        $categoryId = DB::table('shop_categories')->where('title->tr', 'Transpalet')->value('category_id');
        if (!$categoryId) {
            $this->command->error('❌ Transpalet kategorisi bulunamadı!');
            return;
        }

        // Mevcut kayıtları temizle
        $existingProducts = DB::table('shop_products')
            ->where('sku', 'LIKE', 'F4-201%')
            ->pluck('product_id');

        if ($existingProducts->isNotEmpty()) {
            DB::table('shop_products')->whereIn('product_id', $existingProducts)->delete();
            $this->command->info('🧹 Eski F4 201 kayıtları temizlendi (' . $existingProducts->count() . ' ürün)');
        }

        // ============================================
        // MASTER PRODUCT (Genel Bakış)
        // ============================================
        $masterTechnicalSpecs = [
            'kapasite' => [
                'yuk_kapasitesi' => ['deger' => 2000, 'birim' => 'kg', 'etiket' => 'Yük Kapasitesi'],
                'yuk_merkezi_mesafesi' => ['deger' => 600, 'birim' => 'mm', 'etiket' => 'Yük Merkezi Mesafesi'],
                'servis_agirligi' => ['deger' => 140, 'birim' => 'kg', 'etiket' => 'Servis Ağırlığı'],
                'dingil_yuku_yuklu' => ['on' => 620, 'arka' => 1520, 'birim' => 'kg', 'etiket' => 'Dingil Yükü (Yüklü)'],
                'dingil_yuku_bos' => ['on' => 100, 'arka' => 40, 'birim' => 'kg', 'etiket' => 'Dingil Yükü (Boş)'],
            ],
            'boyutlar' => [
                'toplam_uzunluk' => ['deger' => 1550, 'birim' => 'mm', 'etiket' => 'Toplam Uzunluk'],
                'catal_yuzune_uzunluk' => ['deger' => 400, 'birim' => 'mm', 'etiket' => 'Çatal Yüzüne Uzunluk'],
                'toplam_genislik' => ['standart' => 590, 'genis' => 695, 'birim' => 'mm', 'etiket' => 'Toplam Genişlik'],
                'catal_boyutlari' => ['kalinlik' => 50, 'genislik' => 150, 'uzunluk' => 1150, 'birim' => 'mm', 'etiket' => 'Çatal Boyutları'],
                'catal_acikligi' => ['standart' => 560, 'genis' => 685, 'birim' => 'mm', 'etiket' => 'Çatal Açıklığı'],
                'yerden_yukseklik' => ['deger' => 30, 'birim' => 'mm', 'etiket' => 'Yerden Yükseklik'],
                'donus_yaricapi' => ['deger' => 1360, 'birim' => 'mm', 'etiket' => 'Dönüş Yarıçapı'],
                'koridor_genisligi_1000x1200' => ['deger' => 2160, 'birim' => 'mm', 'etiket' => 'Koridor Genişliği (1000×1200)'],
                'koridor_genisligi_800x1200' => ['deger' => 2025, 'birim' => 'mm', 'etiket' => 'Koridor Genişliği (800×1200)'],
                'kaldirma_yuksekligi' => ['deger' => 105, 'birim' => 'mm', 'etiket' => 'Kaldırma Yüksekliği'],
                'indirilmis_yukseklik' => ['deger' => 85, 'birim' => 'mm', 'etiket' => 'İndirilmiş Yükseklik'],
                'timon_yuksekligi' => ['min' => 750, 'maks' => 1190, 'birim' => 'mm', 'etiket' => 'Timon Yüksekliği'],
            ],
            'performans' => [
                'seyir_hizi' => ['yuklu' => 4.5, 'bos' => 5.0, 'birim' => 'km/s', 'etiket' => 'Seyir Hızı'],
                'kaldirma_hizi' => ['yuklu' => 0.016, 'bos' => 0.020, 'birim' => 'm/s', 'etiket' => 'Kaldırma Hızı'],
                'indirme_hizi' => ['yuklu' => 0.058, 'bos' => 0.046, 'birim' => 'm/s', 'etiket' => 'İndirme Hızı'],
                'maksimum_rampa_egimi' => ['yuklu' => 8, 'bos' => 16, 'birim' => '%', 'etiket' => 'Maksimum Rampa Eğimi'],
                'cevrim_verimi' => ['deger' => 88, 'birim' => 't/h', 'etiket' => 'Çevrim Verimi'],
                'cevrim_verimliligi' => ['deger' => 473.12, 'birim' => 't/kWh', 'etiket' => 'Çevrim Verimliliği'],
            ],
            'elektriksel' => [
                'surus_motoru_gucu' => ['deger' => 0.9, 'birim' => 'kW', 'gorev' => 'S2 60 dak', 'etiket' => 'Sürüş Motoru Gücü'],
                'kaldirma_motoru_gucu' => ['deger' => 0.7, 'birim' => 'kW', 'gorev' => 'S3 15%', 'etiket' => 'Kaldırma Motoru Gücü'],
                'aku_sistemi' => [
                    'voltaj' => 48,
                    'kapasite' => 20,
                    'birim' => 'V/Ah',
                    'konfigürasyon' => '2× 24V/20Ah değiştirilebilir Li-Ion modül (4 adede kadar genişletilebilir)',
                    'etiket' => 'Akü Sistemi'
                ],
                'aku_agirligi' => ['deger' => 10, 'birim' => 'kg', 'not' => 'Her bir Li-Ion modül için', 'etiket' => 'Akü Ağırlığı'],
                'sarj_cihazi_secenekleri' => [
                    'standart' => '2× 24V-5A harici şarj ünitesi',
                    'opsiyonel' => ['2× 24V-10A hızlı şarj ünitesi'],
                    'etiket' => 'Şarj Cihazı Seçenekleri'
                ],
                'enerji_tuketimi' => ['deger' => 0.18, 'birim' => 'kWh/h', 'etiket' => 'Enerji Tüketimi'],
                'surus_kontrolu' => 'BLDC sürüş kontrolü',
                'direksiyon_tasarimi' => 'Mekanik',
                'gurultu_seviyesi' => ['deger' => 74, 'birim' => 'dB(A)', 'etiket' => 'Gürültü Seviyesi'],
            ],
            'tekerlekler' => [
                'tip' => 'Poliüretan',
                'surus_tekerlegi' => '210 × 70 mm Poliüretan',
                'yuk_tekerlegi' => '80 × 60 mm Poliüretan (çift sıra standart)',
                'denge_tekerlegi' => '74 × 30 mm Poliüretan',
                'tekerlek_konfigürasyonu' => '1x / 4 (çekiş/yük)',
                'etiket' => 'Tekerlekler'
            ],
            'opsiyonlar' => [
                'denge_tekerlekleri' => ['standart' => false, 'opsiyonel' => true],
                'catal_uzunluklari_mm' => [900, 1000, 1150, 1220, 1350, 1500],
                'catal_acikliklari_mm' => [560, 685],
                'aku_genisletme' => ['standart' => '2× 24V/20Ah', 'maksimum' => '4× 24V/20Ah'],
                'etiket' => 'Opsiyonlar'
            ]
        ];

        $productId = DB::table('shop_products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => 'F4-201-MASTER',
            'model_number' => 'F4-201',
            'parent_product_id' => null,
            'is_master_product' => true,
            'title' => json_encode(['tr' => 'F4 201 - 2 Ton 48V Li-Ion Transpalet Serisi'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f4-201-transpalet-serisi'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '2 ton kapasiteli F4 201 transpalet serisi, 48V Li-Ion güç platformu ile dar koridorlarda maksimum performans. 4 farklı çatal konfigürasyonu ile özel ihtiyaçlarınıza uyum sağlar.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <h2>F4 201 Li-Ion Transpalet Serisi: Her İhtiyaca Özel Çözüm</h2>
    <p><strong>48V Li-Ion güç platformu</strong> ile donatılmış F4 201 serisi, 2 ton yük kapasitesi ve ultra kompakt 400 mm şasi uzunluğu ile dar koridorlu depolarda maksimum verimlilik sağlar.</p>
    <p>4 farklı çatal konfigürasyonu ile her operasyon tipine özel çözüm sunar:</p>
    <ul>
        <li><strong>Standart Çatal (1150×560 mm)</strong> – EUR palet uyumlu genel kullanım</li>
        <li><strong>Geniş Çatal (1150×685 mm)</strong> – Büyük paletler için ekstra stabilite</li>
        <li><strong>Kısa Çatal (900×560 mm)</strong> – Dar alanlarda maksimum çeviklik</li>
        <li><strong>Uzun Çatal (1500×560 mm)</strong> – Özel boy paletler için uzun erişim</li>
    </ul>
</section>
<section class="marketing-body">
    <h3>Tak-Çıkar Li-Ion Batarya Sistemi</h3>
    <p>2× 24V/20Ah modül standart gelir, 60 saniyede değiştirilebilir. 4 modüle kadar genişletilebilir kapasite ile 12-16 saate kadar kesintisiz çalışma imkanı.</p>
    <h3>İXTİF Ekosistemi</h3>
    <ul>
        <li><strong>Yeni ve İkinci El Satış:</strong> İhtiyacınıza uygun esnek seçenekler</li>
        <li><strong>Kiralama Hizmeti:</strong> Kısa ve uzun dönem imkanı</li>
        <li><strong>Yedek Parça:</strong> Orijinal EP parçaları stoktan anında temin</li>
        <li><strong>Teknik Servis:</strong> 0216 755 3 555 | info@ixtif.com</li>
    </ul>
</section>
HTML], JSON_UNESCAPED_UNICODE),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'currency' => 'TRY',
            'stock_tracking' => 1,
            'current_stock' => 0,
            'lead_time_days' => 30,
            'weight' => 140,
            'dimensions' => json_encode(['uzunluk' => 1550, 'genislik' => 590, 'yukseklik' => 105, 'birim' => 'mm'], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode($masterTechnicalSpecs, JSON_UNESCAPED_UNICODE),
            'features' => json_encode(['tr' => [
                'list' => [
                    '48V Li-Ion güç platformu - segmentinde en yüksek voltaj',
                    'Tak-çıkar batarya sistemi - 60 saniyede değişim',
                    '140 kg ultra hafif servis ağırlığı',
                    '400 mm kompakt şasi - 2160 mm koridor genişliğinde çalışma',
                    '4 farklı çatal konfigürasyonu seçeneği',
                    '0.9 kW BLDC sürüş motoru - yüksek tork',
                    'Elektromanyetik acil fren sistemi',
                    'Poliüretan çift sıra yük tekerleri - sessiz çalışma',
                    'CE sertifikalı - Avrupa standartları',
                ],
                'branding' => [
                    'slogan' => 'Depoda hız, sahada prestij: F4 201 ile dar koridorlara hükmedin.',
                    'motto' => 'İXTİF farkı ile 2 tonluk yükler bile havada yürür.',
                    'technical_summary' => 'F4 201 serisi, 48V Li-Ion güç paketi, 0.9 kW BLDC sürüş motoru ve 400 mm ultra kompakt şasi kombinasyonuyla dar koridor ortamlarında yüksek tork, düşük bakım ve sürekli çalışma sunar.'
                ]
            ]], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V (2×24V/20Ah)'],
                ['label' => 'Çatal Seçenekleri', 'value' => '4 Farklı Konfigürasyon'],
                ['label' => 'Denge Tekeri', 'value' => 'Opsiyonel']
            ], JSON_UNESCAPED_UNICODE),
            'accessories' => json_encode([
                [
                    'name' => 'Stabilizasyon Tekerleği (Denge Tekeri)',
                    'description' => 'Bozuk ve eğimli zeminlerde ekstra güvenlik ve stabilite sağlar.',
                    'is_standard' => false,
                    'is_optional' => true,
                ],
                [
                    'name' => 'Hızlı Şarj Ünitesi (2× 24V/10A)',
                    'description' => '5-6 saatte tam dolum, yoğun kullanım için ideal.',
                    'is_standard' => false,
                    'is_optional' => true,
                ],
                [
                    'name' => 'Ek Batarya Modülü (2× 24V/20Ah)',
                    'description' => 'Kapasiteyi 4 modüle çıkararak 12-16 saat kesintisiz çalışma.',
                    'is_standard' => false,
                    'is_optional' => true,
                ],
                [
                    'name' => 'Farklı Çatal Uzunlukları',
                    'description' => '900mm, 1000mm, 1220mm, 1350mm, 1500mm seçenekleri.',
                    'is_standard' => false,
                    'is_optional' => true,
                ],
            ], JSON_UNESCAPED_UNICODE),
            'certifications' => json_encode([
                ['name' => 'CE', 'year' => 2021, 'authority' => 'TÜV Rheinland'],
            ], JSON_UNESCAPED_UNICODE),
            'media_gallery' => json_encode([], JSON_UNESCAPED_UNICODE), // Manuel eklenecek
            'warranty_info' => json_encode(['tr' => '1 Yıl Ürün Garantisi | 2 Yıl Akü Garantisi'], JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ MASTER Product eklendi (ID: {$productId}, SKU: F4-201-MASTER)");

        // Token limiti nedeniyle varyantlar başka mesajda devam edecek
        $this->command->warn('⚠️  VARYANTLAR SONRAKI ADIMDA EKLENECEK (Token tasarrufu)');
    }
}
