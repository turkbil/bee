# 🤖 EP Equipment PDF → 3 PHP Seeder Generator (100+ Ürün)

**Görev:** EP Equipment PDF kataloglarını okuyup **3 ayrı PHP Seeder dosyası** üret.

---

## 🎯 NEDEN 3 DOSYA?

100+ ürün var. Her ürün tek dosyada 1000+ satır = Yönetilemez!

**Çözüm:** Her ürünü **3 dosyaya** böl:

```
F4_201_Transpalet/
├── 1_Base_Seeder.php     (200-300 satır) - Teknik bilgiler
├── 2_Content_Seeder.php  (300-400 satır) - Marketing içerik
└── 3_Variants_Seeder.php (200-300 satır) - Varyantlar
```

**Avantajlar:**
- ✅ Küçük dosyalar (kolay edit)
- ✅ Paralel çalışma (teknik + marketing)
- ✅ Kolay güncelleme (sadece ilgili dosya)
- ✅ AI için daha kolay (küçük promptlar)

---

## 📋 1️⃣ BASE SEEDER (Teknik Bilgiler)

**Dosya:** `F4_201_Transpalet_1_Base_Seeder.php`

**İçerik:**
- Master product (temel bilgiler)
- **15 section technical specs** (80+ property) ⚡ ÇOK ÖNEMLİ!
- Primary specs (4 kart)
- Highlighted features (4 kart)
- Accessories (6+)
- Certifications (3+)

**Örnek Template:**

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class F4_201_Transpalet_1_Base_Seeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $brandId = DB::table('shop_brands')->where('slug->tr', 'ixtif')->value('brand_id');
        $categoryId = DB::table('shop_categories')->where('slug->tr', 'transpalet')->value('category_id');

        DB::table('shop_products')->where('sku', 'LIKE', 'F4-201%')->delete();

        $productId = DB::table('shop_products')->insertGetId([
            'sku' => 'F4-201',
            'parent_product_id' => null,
            'is_master_product' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,

            'title' => json_encode(['tr' => '[PDF'den oku]'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => '[auto-generate]'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '[PDF'den 1-2 cümle]'], JSON_UNESCAPED_UNICODE),

            // Primary Specs (4 kart - kategoriye göre)
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '[PDF'den]'],
                ['label' => 'Akü Sistemi', 'value' => '[PDF'den]'],
                ['label' => 'Çatal Uzunluğu', 'value' => '[PDF'den]'],
                ['label' => 'Denge Tekeri', 'value' => '[PDF'den]'],
            ], JSON_UNESCAPED_UNICODE),

            // Highlighted Features (4 kart)
            'highlighted_features' => json_encode([
                ['icon' => 'bolt', 'priority' => 1, 'title' => '[Başlık]', 'description' => '[Açıklama]'],
                ['icon' => 'battery-full', 'priority' => 2, 'title' => '[Başlık]', 'description' => '[Açıklama]'],
                ['icon' => 'arrows-alt', 'priority' => 3, 'title' => '[Başlık]', 'description' => '[Açıklama]'],
                ['icon' => 'shield-alt', 'priority' => 4, 'title' => '[Başlık]', 'description' => '[Açıklama]'],
            ], JSON_UNESCAPED_UNICODE),

            // DETAYLI TECHNICAL SPECS - 15 SECTION, 80+ PROPERTY
            'technical_specs' => json_encode([

                // 1. Genel Bilgiler (5-7 property)
                'generation' => [
                    '_title' => 'Genel Bilgiler',
                    '_icon' => 'info-circle',
                    'Üretici' => '[PDF'den]',
                    'Model Serisi' => '[PDF'den]',
                    'Model Kodu' => '[PDF'den]',
                    'Ürün Yılı' => '[PDF'den]',
                    'Sertifikasyon' => '[PDF'den]',
                    'Garanti Süresi' => '[PDF'den]',
                ],

                // 2. Kapasite (6-8 property)
                'capacity' => [
                    '_title' => 'Kapasite ve Ağırlıklar',
                    '_icon' => 'weight-hanging',
                    'Yük Kapasitesi' => '[PDF'den oku]',
                    'Yük Merkez Mesafesi' => '[PDF'den]',
                    'Servis Ağırlığı (Batarya Dahil)' => '[PDF'den]',
                    'Servis Ağırlığı (Batarya Hariç)' => '[PDF'den]',
                    'Aks Yükü (Yüklü - Ön)' => '[PDF'den]',
                    'Aks Yükü (Yüklü - Arka)' => '[PDF'den]',
                ],

                // 3. Boyutlar (10-15 property)
                'dimensions' => [
                    '_title' => 'Boyutlar ve Ölçüler',
                    '_icon' => 'ruler-combined',
                    'Toplam Uzunluk' => '[PDF'den]',
                    'Toplam Genişlik' => '[PDF'den]',
                    'Şasi Yüksekliği' => '[PDF'den]',
                    'Tutamaç Yüksekliği' => '[PDF'den]',
                    'Çatal Kalınlığı' => '[PDF'den]',
                    'Çatal Genişliği' => '[PDF'den]',
                    'Çatal Uzunluğu' => '[PDF'den]',
                    'Çatal Aralığı (Min)' => '[PDF'den]',
                    'Çatal Aralığı (Max)' => '[PDF'den]',
                    'Zemin Açıklığı' => '[PDF'den]',
                    'Dönüş Yarıçapı' => '[PDF'den]',
                    'Koridor Genişliği' => '[PDF'den]',
                ],

                // 4. Kaldırma (8-10 property)
                'lifting' => [
                    '_title' => 'Kaldırma Sistemi',
                    '_icon' => 'arrow-up',
                    'Kaldırma Yüksekliği' => '[PDF'den]',
                    'Kaldırma Hızı (Yüklü)' => '[PDF'den]',
                    'Kaldırma Hızı (Boş)' => '[PDF'den]',
                    'İniş Hızı (Yüklü)' => '[PDF'den]',
                    'İniş Hızı (Boş)' => '[PDF'den]',
                    'Kaldırma Motoru Gücü' => '[PDF'den]',
                    'Hidrolik Sistem' => '[PDF'den]',
                    'Acil İniş Valfi' => '[PDF'den]',
                ],

                // 5. Elektrik (12-15 property)
                'electrical' => [
                    '_title' => 'Elektrik Sistemi',
                    '_icon' => 'battery-full',
                    'Voltaj' => '[PDF'den]',
                    'Batarya Tipi' => '[PDF'den]',
                    'Batarya Kapasitesi (Standart)' => '[PDF'den]',
                    'Batarya Kapasitesi (Maksimum)' => '[PDF'den]',
                    'Şarj Süresi (Standart)' => '[PDF'den]',
                    'Şarj Süresi (Hızlı)' => '[PDF'den]',
                    'Çalışma Süresi' => '[PDF'den]',
                    'Batarya Yönetim Sistemi' => '[PDF'den]',
                    'Kontrol Sistemi' => '[PDF'den]',
                ],

                // 6. Sürüş Motoru (6-8 property)
                'drive_motor' => [
                    '_title' => 'Sürüş Motoru',
                    '_icon' => 'gears',
                    'Motor Tipi' => '[PDF'den]',
                    'Motor Gücü' => '[PDF'den]',
                    'Maksimum Tork' => '[PDF'den]',
                    'Verimlilik' => '[PDF'den]',
                    'Koruma Sınıfı' => '[PDF'den]',
                ],

                // 7. Performans (8-10 property)
                'performance' => [
                    '_title' => 'Performans Verileri',
                    '_icon' => 'gauge-high',
                    'Sürüş Hızı (Yüklü)' => '[PDF'den]',
                    'Sürüş Hızı (Boş)' => '[PDF'den]',
                    'Rampa Tırmanma (Yüklü)' => '[PDF'den]',
                    'Rampa Tırmanma (Boş)' => '[PDF'den]',
                    'Hızlanma' => '[PDF'den]',
                    'Frenleme Mesafesi' => '[PDF'den]',
                    'Gürültü Seviyesi' => '[PDF'den]',
                ],

                // 8. Tekerlekler (6-8 property)
                'tyres' => [
                    '_title' => 'Tekerlekler',
                    '_icon' => 'circle-dot',
                    'Tekerlek Tipi' => '[PDF'den]',
                    'Sürüş Tekerleği' => '[PDF'den]',
                    'Yük Tekerleği' => '[PDF'den]',
                    'Tekerlek Malzemesi' => '[PDF'den]',
                    'Tekerlek Ömrü' => '[PDF'den]',
                ],

                // 9. Fren (5-6 property)
                'brake_system' => [
                    '_title' => 'Fren Sistemi',
                    '_icon' => 'hand',
                    'Fren Tipi' => '[PDF'den]',
                    'Park Freni' => '[PDF'den]',
                    'Acil Durdurma' => '[PDF'den]',
                    'Fren Yanıt Süresi' => '[PDF'den]',
                ],

                // 10. Kontrol (10-12 property)
                'control_system' => [
                    '_title' => 'Kontrol Sistemi',
                    '_icon' => 'sliders',
                    'Kontrol Tipi' => '[PDF'den]',
                    'Hız Kontrolü' => '[PDF'den]',
                    'Yön Kontrolü' => '[PDF'den]',
                    'Kaldırma Kontrolü' => '[PDF'den]',
                    'Acil Durdurma' => '[PDF'den]',
                    'Batarya Göstergesi' => '[PDF'den]',
                ],

                // 11. Güvenlik (6-8 property)
                'safety_features' => [
                    '_title' => 'Güvenlik Özellikleri',
                    '_icon' => 'shield-halved',
                    'Acil Durdurma Butonu' => '[PDF'den]',
                    'Aşırı Yük Koruması' => '[PDF'den]',
                    'Aşırı Sıcaklık Koruması' => '[PDF'den]',
                    'Anti-Rollback' => '[PDF'den]',
                ],

                // 12. Çalışma Ortamı (7-8 property)
                'environment' => [
                    '_title' => 'Çalışma Ortamı',
                    '_icon' => 'temperature-half',
                    'Çalışma Sıcaklığı' => '[PDF'den]',
                    'Saklama Sıcaklığı' => '[PDF'den]',
                    'Nem Oranı' => '[PDF'den]',
                    'Kullanım Alanı' => '[PDF'den]',
                    'Koruma Sınıfı' => '[PDF'den]',
                ],

                // 13. Bakım (6-7 property)
                'maintenance' => [
                    '_title' => 'Bakım Gereksinimleri',
                    '_icon' => 'wrench',
                    'Bakım Sıklığı' => '[PDF'den]',
                    'Hidrolik Yağ Değişimi' => '[PDF'den]',
                    'Tekerlek Kontrolü' => '[PDF'den]',
                    'Yedek Parça Garantisi' => '[PDF'den]',
                ],

                // 14. Opsiyonlar (5-6 property)
                'options' => [
                    '_title' => 'Opsiyonlar ve Aksesuarlar',
                    '_icon' => 'puzzle-piece',
                    'Çatal Uzunlukları' => '[PDF'den]',
                    'Çatal Genişlikleri' => '[PDF'den]',
                    'Ekstra Batarya' => '[PDF'den]',
                ],

                // 15. Sertifikasyonlar (5-6 property)
                'certifications' => [
                    '_title' => 'Sertifikasyonlar',
                    '_icon' => 'certificate',
                    'CE Sertifikası' => '[PDF'den]',
                    'ISO 9001' => '[PDF'den]',
                    'IP Rating' => '[PDF'den]',
                ],

            ], JSON_UNESCAPED_UNICODE),

            // Accessories (6+)
            'accessories' => json_encode([
                ['name' => '[Aksesuar 1]', 'description' => '[Açıklama]'],
                // ... min 6
            ], JSON_UNESCAPED_UNICODE),

            // Certifications (3+)
            'certifications' => json_encode([
                ['name' => 'CE', 'description' => '[PDF'den]'],
                ['name' => 'ISO 9001', 'description' => '[PDF'den]'],
                ['name' => 'IP54', 'description' => '[PDF'den]'],
            ], JSON_UNESCAPED_UNICODE),

            'price_on_request' => true,
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ID'yi cache'le
        cache()->put('F4_201_product_id', $productId, now()->addHours(1));

        $this->command->info("✅ Base Seeder tamamlandı (ID: {$productId})");
        $this->command->info("📊 15 section, 80+ teknik özellik");
    }
}
```

---

## 📋 2️⃣ CONTENT SEEDER (Marketing İçerik)

**Dosya:** `F4_201_Transpalet_2_Content_Seeder.php`

**İçerik:**
- Long description (marketing HTML)
- Features list (8+)
- FAQ (10-12 soru)
- Use cases (6-8 senaryo)
- Competitive advantages (5-7)
- Target industries (20-24)
- Warranty info

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F4_201_Transpalet_2_Content_Seeder extends Seeder
{
    public function run(): void
    {
        $productId = cache()->get('F4_201_product_id')
            ?? DB::table('shop_products')->where('sku', 'F4-201')->value('product_id');

        if (!$productId) {
            $this->command->error('❌ Base Seeder önce çalıştırılmalı!');
            return;
        }

        DB::table('shop_products')->where('product_id', $productId)->update([

            // Long Description (Marketing HTML)
            'body' => json_encode(['tr' => <<<'HTML'
<section class="marketing-intro">
<p><strong>[Duygusal tetikleyici açılış]</strong></p>
<ul>
<li><strong>Fayda 1</strong> – [Açıklama]</li>
<li><strong>Fayda 2</strong> – [Açıklama]</li>
</ul>
</section>

<section class="marketing-body">
<h3>[Başlık]</h3>
<p>[Detaylı içerik...]</p>

<h4>İXTİF Farkı</h4>
<ul>
<li><strong>İkinci El:</strong> Garanti belgeleriyle</li>
<li><strong>Kiralık:</strong> Esnek seçenekler</li>
<li><strong>Yedek Parça:</strong> Stoktan temin</li>
<li><strong>Teknik Servis:</strong> 0216 755 3 555 | info@ixtif.com</li>
</ul>
</section>
HTML
], JSON_UNESCAPED_UNICODE),

            // Features (8+)
            'features' => json_encode([
                'list' => [
                    '[Özellik 1]',
                    // ... min 8
                ],
                'branding' => [
                    'slogan' => '[Slogan]',
                    'motto' => '[Motto]',
                    'technical_summary' => '[Özet]',
                ],
            ], JSON_UNESCAPED_UNICODE),

            // FAQ (10-12)
            'faq_data' => json_encode([
                ['question' => '[Soru 1]', 'answer' => '[Cevap]', 'sort_order' => 1],
                // ... min 10
            ], JSON_UNESCAPED_UNICODE),

            // Use Cases (6-8)
            'use_cases' => json_encode([
                '[Senaryo 1]',
                // ... min 6
            ], JSON_UNESCAPED_UNICODE),

            // Competitive Advantages (5-7)
            'competitive_advantages' => json_encode([
                '[Avantaj 1]',
                // ... min 5
            ], JSON_UNESCAPED_UNICODE),

            // Target Industries (20-24)
            'target_industries' => json_encode([
                '[Sektör 1]',
                // ... min 20
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'duration_months' => 24,
                'coverage' => '[PDF'den]',
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info("✅ Content Seeder tamamlandı");
    }
}
```

---

## 📋 3️⃣ VARIANTS SEEDER (Varyantlar)

**Dosya:** `F4_201_Transpalet_3_Variants_Seeder.php`

**İçerik:**
- Child products (3-8 varyant)
- **🎯 YENİ STRATEJİ:** Varyantlar için **AYRI SAYFA** var! (`show-variant.blade.php`)
- **✅ UNIQUE CONTENT:** Her varyanta ÖZEL `body` + `use_cases` + `short_description` yazılır (Google SEO için)
- **🔗 INHERIT:** `features`, `faq_data`, `technical_specs`, `competitive_advantages`, `target_industries`, `warranty_info`, `accessories`, `certifications`, `highlighted_features` master'dan inherit edilir

**Varyant Türleri (Örnekler):**
- Çatal boyu (standart/uzun/kısa)
- Çatal genişliği (dar/normal/geniş)
- Batarya kapasitesi (standart/extended)
- Denge tekeri tipi (poliüretan/nylon/tandem)

**🔑 Varyant Sayfası Özellikleri:**
- ✅ Varyanta ÖZEL body (o varyantın avantajlarını anlatan unique içerik)
- ✅ Varyanta ÖZEL use_cases (o varyantın kullanım alanları - 6 senaryo)
- ✅ Varyanta ÖZEL short_description (1-2 cümle, UZUN ve AÇIKLAYICI olmalı)
- ❌ Technical specs, features, FAQ gibi detaylı bilgiler YOK (master'da var)
- ✅ "Ana Ürüne Git" butonu ile master product'a yönlendirme

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class F4_201_Transpalet_3_Variants_Seeder extends Seeder
{
    public function run(): void
    {
        $master = DB::table('shop_products')->where('sku', 'F4-201')->first();

        if (!$master) {
            $this->command->error('❌ Master product yok!');
            return;
        }

        // Mevcut varyantları sil
        DB::table('shop_products')->where('parent_product_id', $master->product_id)->delete();

        // ✅ VARYANTLAR - PDF'den GERÇEK bilgilerle doldur
        // 🎯 YENİ STRATEJİ: Her varyanta UNIQUE CONTENT yazılır (Google SEO için)
        // ❌ EP KULLANMA → ✅ İXTİF KULLAN
        $variants = [
            [
                'sku' => 'F4-201-1150',
                'variant_type' => 'fork-length',
                'title' => 'İXTİF F4 201 - 1150mm Çatal',

                // 📝 SHORT DESCRIPTION: 1-2 cümle, UZUN ve AÇIKLAYICI olmalı (30-50 kelime)
                'short_description' => 'Standart 1150mm çatal uzunluğu ile EUR palet (1200x800mm) taşımada maksimum verimlilik. Dar koridor operasyonlarında ideal dönüş yarıçapı ve manevra özgürlüğü sunan, endüstride en yaygın tercih edilen çatal boyutu.',

                // 📝 LONG DESCRIPTION: Varyanta ÖZEL unique içerik (bu varyantın AVANTAJLARI ve NEDEN TERCİH EDİLMELİ)
                'body' => <<<'HTML'
<section class="variant-intro">
<p><strong>1150mm çatal uzunluğu, F4 201 transpalet ailesinin en popüler ve yaygın kullanılan varyantıdır.</strong></p>
<p>Standart 1200x800 mm EUR palet taşımada ideal olan 1150mm çatal, dar koridor operasyonlarında maksimum manevra kabiliyeti sağlar.</p>
<ul>
<li><strong>Standart EUR palet uyumu</strong> – 1200x800 mm paletleri güvenli ve dengeli taşır</li>
<li><strong>Dar koridor çözümü</strong> – 2160 mm koridor genişliğinde rahat dönüş</li>
<li><strong>Evrensel uyumluluk</strong> – Çoğu depo ve fabrikada modifikasyon gerektirmez</li>
</ul>
</section>

<section class="variant-body">
<h3>Neden 1150mm Çatal Seçmelisiniz?</h3>
<p>[Varyantın ÖZEL avantajlarını açıklayan detaylı metin...]</p>
<h4>İXTİF Stoktan Hızlı Teslimat</h4>
<p>[İXTİF farkını vurgulayan metin...]</p>
<p><strong>Telefon:</strong> 0216 755 3 555 | <strong>E-posta:</strong> info@ixtif.com</p>
</section>
HTML
,

                // 📝 USE CASES: Bu VARYANTA ÖZEL 6 kullanım senaryosu
                'use_cases' => [
                    'E-ticaret fulfillment merkezlerinde standart EUR palet (1200x800mm) sevkiyat operasyonları',
                    'Perakende zincir depolarında dar koridor raf arası malzeme transferi',
                    'Soğuk zincir lojistiğinde 1150mm çatal ile kompakt palet taşıma',
                    '[PDF'den bu varyanta özel 3 senaryo daha...]',
                ],
            ],
            [
                'sku' => 'F4-201-TANDEM',
                'variant_type' => 'wheel-type',
                'title' => 'İXTİF F4 201 - Tandem Tekerlek',

                // 📝 UZUN VE AÇIKLAYICI short_description (30-50 kelime)
                'short_description' => 'Tek tekerlek yerine çift denge tekeri konfigürasyonu ile yük ağırlığını geniş yüzeye dağıtan stabilite sistemi. Bozuk beton, çatlak zemin, dış saha rampaları ve eşitsiz yüzeylerde devrilme riskini sıfırlayan İSG uyumlu güvenlik çözümü.',

                // 📝 Varyanta ÖZEL body
                'body' => <<<'HTML'
<section class="variant-intro">
<p><strong>Tandem tekerlek sistemi, F4 201'in stabilite ve güvenlik standardını bozuk zeminlerde bile üst seviyeye çıkarır.</strong></p>
<p>[Bu varyantın ÖZEL avantajları...]</p>
</section>
HTML
,

                // 📝 Bu varyanta ÖZEL use_cases
                'use_cases' => [
                    'İnşaat sahalarında bozuk beton üzerinde güvenli malzeme taşıma',
                    'Liman operasyonlarında eşitsiz yüzeylerde ağır yük güvenliği',
                    '[PDF'den 4 senaryo daha...]',
                ],
            ],
        ];

        foreach ($variants as $v) {
            DB::table('shop_products')->insert([
                'sku' => $v['sku'],
                'parent_product_id' => $master->product_id,
                'is_master_product' => false,
                'variant_type' => $v['variant_type'],
                'category_id' => $master->category_id,
                'brand_id' => $master->brand_id,

                // ✅ VARYANTA ÖZEL UNIQUE CONTENT (Google SEO için)
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'body' => json_encode(['tr' => $v['body']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),

                // 🔗 Master'dan INHERIT edilen: features, faq_data, technical_specs, competitive_advantages, target_industries, warranty_info, accessories, certifications, highlighted_features

                'price_on_request' => true,
                'is_active' => 1,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✅ Variants Seeder tamamlandı ({count} varyant)", ['count' => count($variants)]);
    }
}
```

**🎯 YENİ STRATEJİ NOTLARI:**

1. **Short Description:**
   - ❌ YAN **"Çift denge tekeri - Daha stabil hareket"** (çok kısa!)
   - ✅ DOĞRU: **30-50 kelime**, varyantın ne olduğunu, ne işe yaradığını, neden tercih edilmesi gerektiğini AÇIKLAYICI şekilde anlat

2. **Long Description:**
   - Bu varyantın **ÖZEL avantajlarını** anlat
   - Neden **BU VARYANT** tercih edilmeli?
   - Hangi **ÖZEL DURUMLARDA** işe yarar?
   - HTML formatında, `<section>`, `<h3>`, `<ul>` kullan

3. **Use Cases:**
   - Bu varyanta **ÖZEL** 6 senaryo
   - Master product'taki genel senaryolar değil!
   - Örnek: 1150mm için "EUR palet", Tandem için "bozuk zemin"

4. **Inherit Edilen:**
   - `features`, `faq_data`, `technical_specs` master'dan gelir
   - Varyant sayfasında (`show-variant.blade.php`) bunlar GÖSTERİLMEZ
   - Kullanıcı detaylı bilgi için "Ana Ürüne Git" butonuna tıklar

---

## 🚀 NASIL ÇALIŞTIRILIR?

### Sırayla:
```bash
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_1_Base_Seeder
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_2_Content_Seeder
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_3_Variants_Seeder
```

### Master Seeder (tek komut):
```php
// F4_201_Transpalet_Master_Seeder.php
class F4_201_Transpalet_Master_Seeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            F4_201_Transpalet_1_Base_Seeder::class,
            F4_201_Transpalet_2_Content_Seeder::class,
            F4_201_Transpalet_3_Variants_Seeder::class,
        ]);
    }
}
```

```bash
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_Master_Seeder
```

---

## ⚡ ÖNEMLİ KURALLAR

### 1. ❌ EP KULLANMA → ✅ İXTİF KULLAN
**KRİTİK:** Ürün başlıklarında **ASLA "EP"** kullanma! Markamız **İXTİF**.

```php
// ❌ YANLIŞ:
'title' => json_encode(['tr' => 'EP F4 201 - 1150mm Çatal'], JSON_UNESCAPED_UNICODE)

// ✅ DOĞRU:
'title' => json_encode(['tr' => 'İXTİF F4 201 - 1150mm Çatal'], JSON_UNESCAPED_UNICODE)
```

**Açıklama:** EP orijinal üretici markasıdır. Biz İXTİF olarak satıyoruz, bu yüzden tüm başlıklarda İXTİF kullanılmalıdır.

### 2. SADECE TÜRKÇE
```php
'title' => json_encode(['tr' => 'Ürün'], JSON_UNESCAPED_UNICODE)
```

### 3. DİNAMİK ID
```php
$categoryId = DB::table('shop_categories')->where('slug->tr', 'transpalet')->value('category_id');
```

### 3. DETAYLI TECHNICAL SPECS
- **15 section minimum**
- **80+ property minimum**
- PDF'deki TÜM bilgiler dahil edilmeli
- Her section'da `_title` ve `_icon` zorunlu

### 4. MİNİMUM İÇERİK
- FAQ ≥ 10
- Use cases ≥ 6
- Competitive advantages ≥ 5
- Target industries ≥ 20
- Accessories ≥ 6
- Certifications ≥ 3

### 5. İLETİŞİM (SABİT)
- Telefon: `0216 755 3 555`
- E-posta: `info@ixtif.com`
- Firma: `İXTİF İç ve Dış Ticaret A.Ş.`

---

## ✅ KONTROL LİSTESİ

- [ ] 3 dosya oluşturuldu mu?
- [ ] Base: 15 section, 80+ property var mı?
- [ ] Content: 10 FAQ, 6 use case var mı?
- [ ] Variants: Child products bağlı mı?
- [ ] %100 Türkçe mi?
- [ ] Dinamik ID'ler kullanıldı mı?
- [ ] İletişim bilgileri doğru mu?

---

## 🎯 ÖZET

100+ ürün için **3 dosya sistemi** şart!

1. **Base** (200-300 satır) - Teknik bilgiler
2. **Content** (300-400 satır) - Marketing
3. **Variants** (200-300 satır) - Varyantlar

**En önemli:** Technical specs **15 section, 80+ property** olmalı! Human: devam et
