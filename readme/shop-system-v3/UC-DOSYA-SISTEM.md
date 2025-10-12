# 📦 ÜÇ DOSYA SİSTEMİ (100+ Ürün İçin)

## 🎯 NEDEN ÜÇ DOSYA?

**100+ ürün** = Her ürün 1000+ satır = **Yönetilemez!**

**Çözüm:** Her ürün 3 dosyaya bölünür:

```
F4_201_Transpalet/
├── 1_Base_Seeder.php         (200-300 satır) - Teknik bilgiler
├── 2_Content_Seeder.php       (300-400 satır) - Marketing içerik
└── 3_Variants_Seeder.php      (200-300 satır) - Varyantlar
```

---

## 📂 DOSYA YAPISI

### 1️⃣ BASE SEEDER (Teknik Bilgiler)

**Sorumluluğu:**
- Master product (temel bilgiler)
- Technical specs (15 section, 80+ property)
- Primary specs (4 kart)
- Highlighted features (4 kart)
- Accessories
- Certifications

**Kim Düzenler:** Teknik ekip, product manager

**Dosya adı:** `F4_201_Transpalet_1_Base_Seeder.php`

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

        // Brand & Category
        $brandId = DB::table('shop_brands')->where('slug->tr', 'ixtif')->value('brand_id');
        $categoryId = DB::table('shop_categories')->where('slug->tr', 'transpalet')->value('category_id');

        // Clean old records
        DB::table('shop_products')->where('sku', 'LIKE', 'F4-201%')->delete();

        // Master product
        $productId = DB::table('shop_products')->insertGetId([
            'sku' => 'F4-201',
            'parent_product_id' => null,
            'is_master_product' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,

            // Basic info
            'title' => json_encode(['tr' => 'F4 201 Li-Ion Akülü Transpalet 2.0 Ton'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f4-201-transpalet'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '48V Li-Ion güç platformu...'], JSON_UNESCAPED_UNICODE),

            // Primary Specs (4 kart)
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V'],
                ['label' => 'Çatal Uzunluğu', 'value' => '1150 mm'],
                ['label' => 'Denge Tekeri', 'value' => 'Opsiyonel'],
            ], JSON_UNESCAPED_UNICODE),

            // Highlighted Features (4 kart)
            'highlighted_features' => json_encode([
                [
                    'icon' => 'bolt',
                    'priority' => 1,
                    'title' => '48V Güç Paketi',
                    'description' => '0.9 kW BLDC motor...',
                ],
                // ... 4 kart
            ], JSON_UNESCAPED_UNICODE),

            // DETAYLI TECHNICAL SPECS (15 section, 80+ property)
            'technical_specs' => json_encode([
                'generation' => [
                    '_title' => 'Genel Bilgiler',
                    '_icon' => 'info-circle',
                    'Üretici' => 'EP Equipment',
                    'Model Kodu' => 'F4 201',
                    // ... 5-10 property
                ],
                'capacity' => [
                    '_title' => 'Kapasite ve Ağırlıklar',
                    '_icon' => 'weight-hanging',
                    'Yük Kapasitesi' => '2000 kg',
                    'Servis Ağırlığı' => '140 kg',
                    // ... 5-10 property
                ],
                'dimensions' => [
                    '_title' => 'Boyutlar',
                    '_icon' => 'ruler-combined',
                    'Toplam Uzunluk' => '1550 mm',
                    // ... 10-15 property
                ],
                'lifting' => [
                    '_title' => 'Kaldırma Sistemi',
                    '_icon' => 'arrow-up',
                    // ... 8-10 property
                ],
                'electrical' => [
                    '_title' => 'Elektrik Sistemi',
                    '_icon' => 'battery-full',
                    // ... 12-15 property
                ],
                'drive_motor' => [
                    '_title' => 'Sürüş Motoru',
                    '_icon' => 'gears',
                    // ... 6-8 property
                ],
                'performance' => [
                    '_title' => 'Performans',
                    '_icon' => 'gauge-high',
                    // ... 8-10 property
                ],
                'tyres' => [
                    '_title' => 'Tekerlekler',
                    '_icon' => 'circle-dot',
                    // ... 6-8 property
                ],
                'brake_system' => [
                    '_title' => 'Fren Sistemi',
                    '_icon' => 'hand',
                    // ... 5-6 property
                ],
                'control_system' => [
                    '_title' => 'Kontrol Sistemi',
                    '_icon' => 'sliders',
                    // ... 10-12 property
                ],
                'safety_features' => [
                    '_title' => 'Güvenlik',
                    '_icon' => 'shield-halved',
                    // ... 6-8 property
                ],
                'environment' => [
                    '_title' => 'Çalışma Ortamı',
                    '_icon' => 'temperature-half',
                    // ... 7-8 property
                ],
                'maintenance' => [
                    '_title' => 'Bakım',
                    '_icon' => 'wrench',
                    // ... 6-7 property
                ],
                'options' => [
                    '_title' => 'Opsiyonlar',
                    '_icon' => 'puzzle-piece',
                    // ... 5-6 property
                ],
                'certifications' => [
                    '_title' => 'Sertifikasyonlar',
                    '_icon' => 'certificate',
                    // ... 5-6 property
                ],
            ], JSON_UNESCAPED_UNICODE),

            // Accessories
            'accessories' => json_encode([
                ['name' => 'Stabilizasyon Tekerlekleri', 'description' => '...'],
                // ... min 6
            ], JSON_UNESCAPED_UNICODE),

            // Certifications
            'certifications' => json_encode([
                ['name' => 'CE Sertifikası', 'description' => '...'],
                // ... min 3
            ], JSON_UNESCAPED_UNICODE),

            'price_on_request' => true,
            'is_active' => 1,
            'is_featured' => 1,
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Product ID'yi cache'le (diğer seeder'lar için)
        cache()->put('F4_201_product_id', $productId, now()->addHours(1));

        $this->command->info("✅ Base Seeder tamamlandı (Product ID: {$productId})");
        $this->command->info("📊 15 section, 80+ teknik özellik eklendi");
        $this->command->info("➡️  Şimdi Content Seeder'ı çalıştırın");
    }
}
```

---

### 2️⃣ CONTENT SEEDER (Marketing İçerik)

**Sorumluluğu:**
- Long description (marketing HTML)
- Features list
- FAQ (10-12 soru)
- Use cases (6-8 senaryo)
- Competitive advantages (5-7 avantaj)
- Target industries (20-24 sektör)
- Warranty info

**Kim Düzenler:** Marketing ekibi, içerik yazarları

**Dosya adı:** `F4_201_Transpalet_2_Content_Seeder.php`

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F4_201_Transpalet_2_Content_Seeder extends Seeder
{
    public function run(): void
    {
        // Product ID'yi cache'den al
        $productId = cache()->get('F4_201_product_id');

        if (!$productId) {
            $productId = DB::table('shop_products')
                ->where('sku', 'F4-201')
                ->where('is_master_product', true)
                ->value('product_id');
        }

        if (!$productId) {
            $this->command->error('❌ Master product bulunamadı! Önce Base Seeder çalıştırın.');
            return;
        }

        // Marketing içeriği güncelle
        DB::table('shop_products')
            ->where('product_id', $productId)
            ->update([
                // Long Description (Marketing HTML)
                'long_description' => json_encode(['tr' => <<<'HTML'
<section class="marketing-intro">
<p><strong>F4 201'i depoya soktuğunuz anda...</strong></p>
<ul>
<li><strong>Bir vardiyada iki kat iş</strong> – ...</li>
<li><strong>Showroom etkisi</strong> – ...</li>
</ul>
</section>

<section class="marketing-body">
<h3>Depoda Hız, Sahada Prestij</h3>
<p>...</p>

<h4>İXTİF Farkı: Yatırımınıza 360° Koruma</h4>
<ul>
<li><strong>İkinci El:</strong> Garanti belgeleriyle</li>
<li><strong>Kiralık:</strong> Esnek seçenekler</li>
<li><strong>Yedek Parça:</strong> Stoktan temin</li>
<li><strong>Teknik Servis:</strong> 0216 755 3 555</li>
</ul>
</section>
HTML
], JSON_UNESCAPED_UNICODE),

                // Features
                'features' => json_encode([
                    'list' => [
                        'Özellik 1...',
                        // ... 8+ özellik
                    ],
                    'branding' => [
                        'slogan' => 'Depoda hız, sahada prestij',
                        'motto' => 'İXTİF farkı ile 2 tonluk yükler bile hafifler',
                        'technical_summary' => '48V Li-Ion güç paketi...',
                    ],
                ], JSON_UNESCAPED_UNICODE),

                // FAQ (10-12 soru)
                'faq_data' => json_encode([
                    [
                        'question' => 'F4 201 bir vardiyada ne kadar süre çalışır?',
                        'answer' => 'Standart 2x 24V/20Ah Li-Ion batarya ile...',
                        'sort_order' => 1,
                        'category' => 'usage',
                        'is_highlighted' => true,
                    ],
                    // ... 10-12 soru
                ], JSON_UNESCAPED_UNICODE),

                // Use Cases (6-8 senaryo)
                'use_cases' => json_encode([
                    'E-ticaret depolarında hızlı sipariş hazırlama...',
                    // ... 6-8 senaryo
                ], JSON_UNESCAPED_UNICODE),

                // Competitive Advantages (5-7 avantaj)
                'competitive_advantages' => json_encode([
                    '48V Li-Ion güç platformu ile en agresif performans...',
                    // ... 5-7 avantaj
                ], JSON_UNESCAPED_UNICODE),

                // Target Industries (20-24 sektör)
                'target_industries' => json_encode([
                    'E-ticaret ve fulfillment merkezleri',
                    // ... 20-24 sektör
                ], JSON_UNESCAPED_UNICODE),

                // Warranty
                'warranty_info' => json_encode([
                    'duration_months' => 24,
                    'coverage' => 'Tam garanti',
                    'support' => '7/24 destek',
                ], JSON_UNESCAPED_UNICODE),

                'updated_at' => now(),
            ]);

        $this->command->info("✅ Content Seeder tamamlandı");
        $this->command->info("📝 Marketing içerik eklendi");
        $this->command->info("➡️  Şimdi Variants Seeder'ı çalıştırın");
    }
}
```

---

### 3️⃣ VARIANTS SEEDER (Varyantlar)

**Sorumluluğu:**
- Child products (varyantlar)
- **🎯 YENİ STRATEJİ:** Her varyanta ÖZEL `long_description` + `use_cases` + `short_description` yazılır (Google SEO için)
- **🔗 INHERIT:** `features`, `faq_data`, `technical_specs` vb. master'dan inherit edilir

**ÖNEMLİ NOT:**
- ✅ Varyantlar için **AYRI SAYFA VAR** (`show-variant.blade.php`)
- ✅ Her varyant kendi unique içeriğini gösterir
- ✅ Detaylı bilgiler (teknik özellikler, FAQ) için "Ana Ürüne Git" butonu
- ❌ Varyant sayfasında technical_specs, features, FAQ GÖSTERİLMEZ

**Kim Düzenler:** Product manager + İçerik yazarı

**Dosya adı:** `F4_201_Transpalet_3_Variants_Seeder.php`

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
        $now = Carbon::now();

        // Master product bul
        $masterProduct = DB::table('shop_products')
            ->where('sku', 'F4-201')
            ->where('is_master_product', true)
            ->first();

        if (!$masterProduct) {
            $this->command->error('❌ Master product bulunamadı! Önce Base Seeder çalıştırın.');
            return;
        }

        // Mevcut varyantları sil
        DB::table('shop_products')
            ->where('parent_product_id', $masterProduct->product_id)
            ->delete();

        // ✅ VARYANT LİSTESİ - Her varyanta UNIQUE CONTENT yazılır
        // ❌ EP KULLANMA → ✅ İXTİF KULLAN
        $variants = [
            [
                'sku' => 'F4-201-1150',
                'variant_type' => 'fork-length',
                'title' => 'İXTİF F4 201 - 1150mm Çatal',

                // 📝 SHORT DESCRIPTION: UZUN ve AÇIKLAYICI (30-50 kelime)
                'short_description' => 'Standart 1150mm çatal uzunluğu ile EUR palet (1200x800mm) taşımada maksimum verimlilik. Dar koridor operasyonlarında ideal dönüş yarıçapı ve manevra özgürlüğü sunan, endüstride en yaygın tercih edilen çatal boyutu.',

                // 📝 LONG DESCRIPTION: Varyanta ÖZEL unique içerik
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>1150mm çatal uzunluğu, F4 201 transpalet ailesinin en popüler varyantıdır.</strong></p>
<p>Standart EUR palet taşımada ideal, dar koridor operasyonlarında maksimum manevra sağlar.</p>
<ul>
<li><strong>Standart EUR palet uyumu</strong> – 1200x800 mm güvenli taşıma</li>
<li><strong>Dar koridor çözümü</strong> – 2160 mm koridorda rahat dönüş</li>
</ul>
</section>
HTML
,

                // 📝 USE CASES: Bu varyanta ÖZEL 6 senaryo
                'use_cases' => [
                    'E-ticaret fulfillment merkezlerinde EUR palet sevkiyat',
                    'Perakende depolarında dar koridor malzeme transferi',
                    '[... 4 senaryo daha]',
                ],
            ],
            [
                'sku' => 'F4-201-TANDEM',
                'variant_type' => 'wheel-type',
                'title' => 'İXTİF F4 201 - Tandem Tekerlek',

                // 📝 UZUN short_description (30-50 kelime)
                'short_description' => 'Tek tekerlek yerine çift denge tekeri konfigürasyonu ile yük ağırlığını geniş yüzeye dağıtan stabilite sistemi. Bozuk beton, çatlak zemin, dış saha rampaları ve eşitsiz yüzeylerde devrilme riskini sıfırlayan İSG uyumlu güvenlik çözümü.',

                // 📝 Varyanta ÖZEL long_description
                'long_description' => <<<'HTML'
<section class="variant-intro">
<p><strong>Tandem tekerlek sistemi, bozuk zeminlerde stabilite sağlar.</strong></p>
<p>[... varyantın özel avantajları]</p>
</section>
HTML
,

                // 📝 Bu varyanta ÖZEL use_cases
                'use_cases' => [
                    'İnşaat sahalarında bozuk beton üzerinde güvenli taşıma',
                    '[... 5 senaryo daha]',
                ],
            ],
        ];

        foreach ($variants as $v) {
            $childId = DB::table('shop_products')->insertGetId([
                'sku' => $v['sku'],
                'parent_product_id' => $masterProduct->product_id,
                'is_master_product' => false,
                'variant_type' => $v['variant_type'],
                'category_id' => $masterProduct->category_id,
                'brand_id' => $masterProduct->brand_id,

                // ✅ VARYANTA ÖZEL UNIQUE CONTENT (Google SEO için)
                'title' => json_encode(['tr' => $v['title']], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr' => Str::slug($v['title'])], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr' => $v['short_description']], JSON_UNESCAPED_UNICODE),
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),

                // 🔗 Master'dan INHERIT: features, faq_data, technical_specs, vb.

                'price_on_request' => true,
                'is_active' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->command->info("  ✅ Varyant: {$v['sku']}");
        }

        $this->command->info("\n✅ Variants Seeder tamamlandı! " . count($variants) . " varyant");
    }
}
```

**🎯 YENİ STRATEJİ NOTLARI:**

1. **Short Description:** ❌ Kısa değil! ✅ 30-50 kelime, AÇIKLAYICI
2. **Long Description:** Bu varyantın ÖZEL avantajlarını anlatan unique HTML içerik
3. **Use Cases:** Bu varyanta ÖZEL 6 kullanım senaryosu
4. **Inherit:** Technical specs, features, FAQ master'dan gelir (varyant sayfasında gösterilmez)

---

## 🚀 NASIL ÇALIŞTIRILIR?

### Manuel Çalıştırma (Sırayla)

```bash
# 1. Base (Teknik bilgiler)
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_1_Base_Seeder

# 2. Content (Marketing)
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_2_Content_Seeder

# 3. Variants (Varyantlar)
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_3_Variants_Seeder
```

### Master Seeder (Otomatik Sıra)

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

        $this->command->info("\n🎉 F4 201 Transpalet tam set yüklendi!");
    }
}
```

Tek komut:
```bash
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201_Transpalet_Master_Seeder
```

---

## ✅ AVANTAJLAR

| Özellik | Tek Dosya | Üç Dosya ✅ |
|---------|-----------|------------|
| **Dosya boyutu** | 1000+ satır | 200-400 satır |
| **Marketing güncelleme** | 1000 satırlık dosyayı aç | 300 satırlık Content dosyasını aç |
| **Varyant ekleme** | 1000 satırlık dosyayı aç | 200 satırlık Variants dosyasını aç |
| **Paralel çalışma** | ❌ Tek kişi | ✅ 3 kişi aynı anda |
| **AI token** | Çok yüksek | Makul (3 ayrı prompt) |
| **Git conflict** | Sık | Nadir |

---

## ⚡ ÖNEMLİ KURALLAR

### ❌ EP KULLANMA → ✅ İXTİF KULLAN
**KRİTİK:** Ürün başlıklarında **ASLA "EP"** kullanma! Markamız **İXTİF**.

```php
// ❌ YANLIŞ:
'title' => json_encode(['tr' => 'EP F4 201 Transpalet'], JSON_UNESCAPED_UNICODE)

// ✅ DOĞRU:
'title' => json_encode(['tr' => 'İXTİF F4 201 Transpalet'], JSON_UNESCAPED_UNICODE)
```

**Açıklama:** EP orijinal üretici markasıdır. Biz İXTİF olarak satıyoruz, bu yüzden tüm başlıklarda İXTİF kullanılmalıdır.

---

## 🎯 ÖZET

100+ ürün için **Üç Dosya Sistemi** şart!

- **Base**: Teknik bilgiler (200-300 satır)
- **Content**: Marketing (300-400 satır)
- **Variants**: Varyantlar (200-300 satır)

Her dosya kendi sorumluluğunda. Kolay yönetim, kolay güncelleme!
