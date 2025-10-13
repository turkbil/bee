# CHATGPT SHOP SEEDER GENERATOR

**GÖREV**: PDF'den 3 seeder oluştur (1_Master, 2_Detailed, 3_Variants)

## ⚠️ KRİTİK KURALLAR (MUTLAKA UYULMALI)

### 📌 Genel Kurallar
- **İçerik:** PDF'den GERÇEK veri. Placeholder/örnek veri YASAK!
- **PHP Array:** `['key' => 'value']` formatı (JavaScript değil!)
- **Namespace:** `namespace Modules\Shop\Database\Seeders;` zorunlu
- **Timestamps:** `created_at`, `updated_at`, `published_at` her seeder'da olmalı
- **Kategori ID:** HARDCODE → 1=Forklift, 2=Transpalet, 3=İstif, 4=Sipariş, 5=Otonom, 6=Reach
- **JSON_UNESCAPED_UNICODE:** Tüm json_encode'larda kullanılmalı

### 🎨 ICON SİSTEMİ (YENİ - ÇOK ÖNEMLİ!)

**TÜM liste alanlarında her maddeye AYRI ICON eklenecek:**

```php
// ❌ YANLIŞ (eski sistem):
'use_cases' => json_encode(['Madde 1', 'Madde 2'], JSON_UNESCAPED_UNICODE)

// ✅ DOĞRU (yeni icon destekli):
'use_cases' => json_encode([
    ['icon' => 'box-open', 'text' => 'Madde 1'],
    ['icon' => 'store', 'text' => 'Madde 2']
], JSON_UNESCAPED_UNICODE)
```

**Icon eklenecek alanlar:**
- ✅ use_cases → `[['icon' => '...', 'text' => '...'], ...]`
- ✅ competitive_advantages → `[['icon' => '...', 'text' => '...'], ...]`
- ✅ target_industries → `[['icon' => '...', 'text' => '...'], ...]`
- ✅ features → `[['icon' => '...', 'text' => '...'], ...]`
- ✅ accessories → `[['icon' => '...', 'name' => '...', ...], ...]`
- ✅ certifications → `[['icon' => '...', 'name' => '...', ...], ...]`

**FontAwesome 7.1 Geçerli Icon İsimleri:**

```php
// Kullanım Alanları için öneriler:
'box-open', 'store', 'warehouse', 'snowflake', 'pills', 'car',
'tshirt', 'industry', 'couch', 'hammer', 'book', 'seedling'

// Rekabet Avantajları için:
'bolt', 'battery-full', 'arrows-alt', 'layer-group',
'shield-alt', 'shipping-fast', 'star', 'trophy'

// Sektörler için:
'briefcase', 'building', 'cart-shopping', 'wine-bottle',
'flask', 'microchip', 'tv', 'paw', 'print'

// Aksesuarlar için:
'cog', 'plug', 'charging-station', 'grip-lines-vertical',
'tachometer-alt', 'wrench', 'screwdriver'

// Sertifikalar için:
'certificate', 'award', 'shield-check', 'stamp', 'medal'

// Özellikler için:
'check-circle', 'check', 'circle-check', 'star', 'bolt'
```

**⚠️ KULLANILMAMASI GEREKEN (geçersiz) iconlar:**
- ❌ `battery-bolt` (kullan: `battery-full` veya `bolt`)
- ❌ `hand-paper` (kullan: `hand`)
- ❌ `weight` (kullan: `weight-hanging` veya `weight-scale`)
- ❌ `steering` (kullan: `steering-wheel` yoksa `circle-notch`)
- ❌ `wheels` (kullan: `circle` veya `cog`)

### 🚨 ÇOK ÖNEMLİ - ÖRNEKLER HAKKINDA

**AŞAĞIDA GÖSTERILEN TÜM ÖRNEKLER SADECE REFERANS İÇİNDİR!**

- ✅ Icon isimleri → PDF içeriğine UYGUN iconları seç (örnekleri aynen kopyalama!)
- ✅ Madde sayıları → PDF'de varsa 8'den fazla use_case ekle (örnek 8, sen 12 yapabilirsin)
- ✅ Metin içerikleri → PDF'den GERÇEK veriyi kullan (placeholder YASAK!)
- ✅ Madde sıralaması → PDF'deki öneme göre sırala (örnekteki sıra sabit değil)

**ÖRNEK:**
```php
// ❌ YANLIŞ (örnekleri aynen kopyalamak):
'use_cases' => [
    ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment...'], // Örneği kopyaladın!
    ['icon' => 'store', 'text' => 'Perakende dağıtım...']        // Örneği kopyaladın!
]

// ✅ DOĞRU (PDF'den gerçek içerik):
'use_cases' => [
    ['icon' => 'industry', 'text' => '{PDF: Ağır sanayi tesislerinde...}'],    // PDF'den
    ['icon' => 'flask', 'text' => '{PDF: Kimya fabrikalarında...}'],           // PDF'den
    ['icon' => 'warehouse', 'text' => '{PDF: Soğuk hava depolarında...}']      // PDF'den
]
```

**İcon seçimi PDF içeriğine göre yapılmalı:**
- Gıda/Soğuk → `snowflake`
- Kimya/İlaç → `flask`, `pills`
- Otomotiv → `car`
- Tekstil → `tshirt`
- Ağır sanayi → `industry`
- E-ticaret → `box-open`

---

## 1️⃣ MASTER SEEDER (1_Master.php)

```php
<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class {MODEL}_{KATEGORİ}_1_Master extends Seeder {
    public function run(): void {
        $categoryId = 2; // PDF klasöründen tespit et
        $brandId = 1; // İXTİF
        $sku = '{PDF: Model numarası - örn: F4-201}';

        $titleTr = '{PDF: İXTİF + Model + Kapasite - örn: İXTİF F4 201 - 2.0 Ton Li-Ion Transpalet}';
        $shortTr = '{PDF'den 30-50 kelimelik özet: kapasite, voltaj, boyutlar, hız, öne çıkan özellikler}';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => $shortTr], JSON_UNESCAPED_UNICODE),
            'base_price' => 0.00,
            'price_on_request' => true,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'is_master_product' => true,
            'product_type' => 'physical',
            'condition' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),

            // TECHNICAL SPECS: PDF'deki TÜM teknik özellikleri ekle (key => value)
            'technical_specs' => json_encode([
                'Kapasite' => '{PDF}',
                'Yük Merkez Mesafesi' => '{PDF}',
                'Servis Ağırlığı' => '{PDF}',
                'Sürüş Tipi' => '{PDF}',
                'Akü' => '{PDF}',
                'Sürüş Motoru' => '{PDF}',
                // ... PDF'deki diğer tüm teknik özellikler
            ], JSON_UNESCAPED_UNICODE),

            // FEATURES: 8 madde, her biri icon + text formatında
            'features' => json_encode([
                ['icon' => 'battery-full', 'text' => '{PDF özellik 1}'],
                ['icon' => 'bolt', 'text' => '{PDF özellik 2}'],
                ['icon' => 'compress', 'text' => '{PDF özellik 3}'],
                ['icon' => 'weight-hanging', 'text' => '{PDF özellik 4}'],
                ['icon' => 'shield-alt', 'text' => '{PDF özellik 5}'],
                ['icon' => 'cog', 'text' => '{PDF özellik 6}'],
                ['icon' => 'layer-group', 'text' => '{PDF özellik 7}'],
                ['icon' => 'check-circle', 'text' => '{PDF özellik 8}']
            ], JSON_UNESCAPED_UNICODE),
        ]);
        $this->command->info("✅ Master: {$sku}");
    }
}
```

**Master Kontrol Listesi:**
- [ ] Kategori ID doğru mu? (klasör adından tespit et)
- [ ] Short description 30-50 kelime mi?
- [ ] Technical specs PDF'den eksiksiz alındı mı?
- [ ] Features 8 madde mi ve her madde `['icon' => '...', 'text' => '...']` formatında mı?

---

## 2️⃣ DETAILED SEEDER (2_Detailed.php)

```php
<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {MODEL}_{KATEGORİ}_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', '{SKU}')->first();
        if (!$p) { $this->command->error('❌ Master bulunamadı'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([

            // ========================================
            // LONG DESCRIPTION (800-1500 kelime HTML)
            // ========================================
            // 3 BÖLÜM YAPISI:
            // 1. HAVALİ GİRİŞ: Duygusal, dikkat çeken, hikaye anlatımı
            // 2. TEKNİK GELİŞME: Profesyonel, detaylı özellikler
            // 3. SICAK SONUÇ: Satışa yönlendiren, harekete geçiren

            'body' => json_encode(['tr' => '
<section class="hero-intro">
    <h2>{Çarpıcı başlık - örn: İXTİF F4 201: Lojistiğin Yeni Nabzı}</h2>
    <p><strong>Sabah 06:00.</strong> Depo kapıları açılıyor... {Hikaye anlatımı, duygusal bağ kurma, müşterinin kendini içinde görmesi. 100-150 kelime. Ürünün getirdiği değişimi betimle.}</p>
</section>

<section class="technical-power">
    <h3>Teknik Güç</h3>
    <p>{PDF'den teknik detaylar. Voltaj, kapasite, hız, boyutlar. Profesyonel dil. 200-300 kelime.}</p>
</section>

<section class="operations">
    <h3>Operasyonel Avantajlar</h3>
    <p>{Günlük kullanımda nasıl fark yaratır? Zaman tasarrufu, maliyet düşürme. 150-200 kelime.}</p>
</section>

<section class="battery-system">
    <h3>Enerji Sistemi</h3>
    <p>{Batarya özellikleri, şarj süreleri, vardiya optimizasyonu. 100-150 kelime.}</p>
</section>

<section class="closing">
    <h3>Neden Şimdi?</h3>
    <p>Lojistik sektörü hızla değişiyor. {Aciliyet yarat, harekete geçir. 100-150 kelime.}</p>
    <p><strong>Bugün harekete geçin.</strong> Teknik detaylar için bizi arayın: <strong>0216 755 3 555</strong> | <a href="mailto:info@ixtif.com">info@ixtif.com</a></p>
</section>
'], JSON_UNESCAPED_UNICODE),

            // ========================================
            // PRIMARY SPECS (4 ana özellik kartı)
            // ========================================
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '{PDF - örn: 2000 kg}'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '{PDF - örn: 48V Li-Ion}'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '{PDF - örn: 4.5/5 km/s}'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '{PDF - örn: 1360 mm}']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // HIGHLIGHTED FEATURES (6 madde)
            // ========================================
            // ⚠️ FontAwesome 7.1 geçerli icon isimleri kullan!
            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '{PDF özellik 1}', 'description' => '{10-15 kelime açıklama}'],
                ['icon' => 'weight-scale', 'title' => '{PDF özellik 2}', 'description' => '{10-15 kelime açıklama}'],
                ['icon' => 'compress', 'title' => '{PDF özellik 3}', 'description' => '{10-15 kelime açıklama}'],
                ['icon' => 'circle-notch', 'title' => '{PDF özellik 4}', 'description' => '{10-15 kelime açıklama}'],
                ['icon' => 'hand', 'title' => '{PDF özellik 5}', 'description' => '{10-15 kelime açıklama}'],
                ['icon' => 'dolly', 'title' => '{PDF özellik 6}', 'description' => '{10-15 kelime açıklama}']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // USE CASES (8 madde - icon + text)
            // ========================================
            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret fulfillment merkezlerinde EUR palet akışı ve çapraz sevkiyat'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında raf arası malzeme transferi'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve içecek depolarında soğuk oda giriş-çıkış operasyonları'],
                ['icon' => 'pills', 'text' => 'İlaç ve kozmetik lojistiğinde hassas ürün taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça depolarında rampa yaklaşımı'],
                ['icon' => 'warehouse', 'text' => '3PL merkezlerinde yoğun vardiya içi besleme hatları'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve hazır giyimde koli paletleme ve hat besleme'],
                ['icon' => 'industry', 'text' => 'Endüstriyel üretim hücrelerinde WIP taşıma']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // COMPETITIVE ADVANTAGES (4-6 madde - icon + text)
            // ========================================
            // Her maddeye FARKLI icon!
            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '{PDF avantaj 1 - teknik üstünlük}'],
                ['icon' => 'battery-full', 'text' => '{PDF avantaj 2 - enerji/akü sistemi}'],
                ['icon' => 'arrows-alt', 'text' => '{PDF avantaj 3 - boyut/manevra}'],
                ['icon' => 'layer-group', 'text' => '{PDF avantaj 4 - platform/varyant}'],
                ['icon' => 'shield-alt', 'text' => '{PDF avantaj 5 - güvenlik}'],
                ['icon' => 'shipping-fast', 'text' => '{PDF avantaj 6 - lojistik/teslimat}']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // TARGET INDUSTRIES (MİNİMUM 20 MADDE - icon + text)
            // ========================================
            // ⚠️ 20'den az olursa RED! Her sektör için uygun icon seç
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Lojistik Hizmetleri'],
                ['icon' => 'store', 'text' => 'Perakende Zincirler'],
                ['icon' => 'cart-shopping', 'text' => 'FMCG Dağıtım'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimyasal Depolama'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Teknoloji'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya Dağıtım'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Hazır Giyim'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Pet Shop ve Hayvan Ürünleri']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // WARRANTY INFO (Garanti Bilgisi)
            // ========================================
            // ⚠️ KATEGORİ İSMİ YAZMA! (örn: "Kategori 2 Transpalet:" YASAK)
            // ⚠️ 12 ay makine, 24 ay akü NET belirt
            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li-Ion batarya modülleri ise satın alım tarihinden itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarında üretim hatalarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // ACCESSORIES (4-6 madde - icon dahil)
            // ========================================
            // ⚠️ is_standard: true olanların price'ı NULL olmalı!
            // ⚠️ is_standard: false olanlar 'Talep üzerine'
            'accessories' => json_encode([
                ['icon' => 'cog', 'name' => '{Opsiyonel aksesuar}', 'description' => '{15-20 kelime}', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => '{Standart aksesuar}', 'description' => '{15-20 kelime}', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'charging-station', 'name' => '{Opsiyonel aksesuar}', 'description' => '{15-20 kelime}', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'grip-lines-vertical', 'name' => '{Opsiyonel aksesuar}', 'description' => '{15-20 kelime}', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // CERTIFICATIONS (3-5 madde - icon dahil)
            // ========================================
            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'European Union'],
                ['icon' => 'award', 'name' => 'ISO 9001', 'year' => '2023', 'authority' => 'ISO'],
                ['icon' => 'shield-check', 'name' => 'EN 16796', 'year' => '2024', 'authority' => 'CEN']
            ], JSON_UNESCAPED_UNICODE),

            // ========================================
            // FAQ DATA (10-12 soru)
            // ========================================
            // ⚠️ HER SORUDA "İXTİF satış, servis, kiralama, parça" YAZMA!
            // ⚠️ SADECE SON SORUDA (garanti/servis sorusu) İXTİF bilgisi olsun
            'faq_data' => json_encode([
                ['question' => '{Teknik soru 1}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 2}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 3}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 4}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 5}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 6}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 7}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 8}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 9}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 10}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => '{Teknik soru 11}', 'answer' => '{20-40 kelime teknik yanıt - İXTİF bilgisi YOK}'],
                ['question' => 'Garanti kapsamı ve servis desteği nasıl sağlanır?', 'answer' => 'Makineye 12 ay, Li-Ion batarya modüllerine 24 ay fabrika garantisi verilir. İXTİF, Türkiye genelinde satış, servis, kiralama ve yedek parça desteği sağlar. 7/24 teknik danışmanlık hattı: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
        $this->command->info("✅ Detailed: {SKU}");
    }
}
```

**Detailed Kontrol Listesi:**
- [ ] Long description 3 bölümden oluşuyor mu? (Giriş + Teknik + Sonuç)
- [ ] Long description 800-1500 kelime mi?
- [ ] Primary specs 4 madde mi ve icon + label + value var mı?
- [ ] Highlighted features 6 madde mi ve geçerli FontAwesome iconları mı?
- [ ] Use cases 8 madde mi ve her madde icon + text formatında mı?
- [ ] Competitive advantages 4-6 madde mi ve her maddeye farklı icon verilmiş mi?
- [ ] Target industries MİNİMUM 20 madde mi? (Daha az ise RED!)
- [ ] Warranty info'da kategori ismi YOK mu?
- [ ] Standart aksesuarların price değeri `null` mı?
- [ ] FAQ'de sadece son soruda İXTİF bilgisi var mı?
- [ ] Certifications her madde icon içeriyor mu?

---

## 3️⃣ VARIANTS SEEDER (3_Variants.php)

```php
<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class {MODEL}_{KATEGORİ}_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', '{SKU}')->first();
        if (!$m) { $this->command->error('❌ Master bulunamadı'); return; }

        $variants = [
            [
                'sku' => '{SKU-VARIANT - örn: F4-201-1150-560}',
                'variant_type' => 'catal-uzunlugu', // Türkçe kebab-case
                'title' => '{İXTİF + Model + Varyant özelliği}',
                'short_description' => '{30-50 kelime varyant özellikleri}',
                'body' => '{800-1200 kelime HTML - varyanta özel detaylar}',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => '{Varyanta özel kullanım 1}'],
                    ['icon' => 'store', 'text' => '{Varyanta özel kullanım 2}'],
                    ['icon' => 'warehouse', 'text' => '{Varyanta özel kullanım 3}'],
                    ['icon' => 'snowflake', 'text' => '{Varyanta özel kullanım 4}'],
                    ['icon' => 'car', 'text' => '{Varyanta özel kullanım 5}'],
                    ['icon' => 'industry', 'text' => '{Varyanta özel kullanım 6}']
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
                'product_type' => 'physical',
                'condition' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("✅ Varyant: {$v['sku']}");
        }
    }
}
```

**Variants Kontrol Listesi:**
- [ ] variant_type Türkçe ve kebab-case mi? (örn: catal-uzunlugu)
- [ ] parent_product_id, category_id, brand_id doğru aktarılmış mı?
- [ ] use_cases icon + text formatında mı?

---

## 📋 GENEL KONTROL LİSTESİ

### Master Seeder:
- [ ] Kategori ID doğru (1-6 arası)
- [ ] Short description 30-50 kelime
- [ ] Technical specs eksiksiz
- [ ] Features 8 madde ve icon formatında

### Detailed Seeder:
- [ ] Long description 3 bölüm (800-1500 kelime)
- [ ] Primary specs 4 madde
- [ ] Highlighted features 6 madde (geçerli FontAwesome iconları)
- [ ] Use cases 8 madde (icon + text)
- [ ] Competitive advantages 4-6 madde (farklı iconlar)
- [ ] Target industries MİNİMUM 20 madde (icon + text)
- [ ] Warranty info kategori ismi YOK
- [ ] Standart aksesuarlar price: null
- [ ] FAQ sadece son soruda İXTİF bilgisi
- [ ] Certifications icon dahil

### Variants Seeder:
- [ ] Variant_type Türkçe kebab-case
- [ ] Use cases icon + text formatında

---

## 🎯 ÇIKTI FORMATI

**3 DOSYA TEK SEFERDE OLUŞTUR:**

1. `{MODEL}_{KATEGORİ}_1_Master.php`
2. `{MODEL}_{KATEGORİ}_2_Detailed.php`
3. `{MODEL}_{KATEGORİ}_3_Variants.php`

**Dosya isimlendirme örneği:**
- `F4_201_Transpalet_1_Master.php`
- `F4_201_Transpalet_2_Detailed.php`
- `F4_201_Transpalet_3_Variants.php`

**SANDBOX İNDİRME LİNKİ VER!**

---

## 🚨 SON KONTROL - BU KURALLARA UYMAYAN SEEDER REDDEDİLİR

**Format Kontrolleri:**
- ✅ Tüm liste alanları icon formatında mı?
- ✅ FontAwesome iconları geçerli mi? (geçersiz icon yok mu?)
- ✅ JSON_UNESCAPED_UNICODE her yerde kullanıldı mı?
- ✅ Namespace doğru mu?
- ✅ Timestamps var mı?

**İçerik Kontrolleri:**
- ✅ Target industries 20+ madde mi?
- ✅ Warranty info'da kategori ismi YOK mu?
- ✅ Standart aksesuarlar price: null mı?
- ✅ FAQ'de her yanıtta İXTİF bilgisi YOK mu? (sadece son soruda)
- ✅ Long description 3 bölümlü yapıda mı?

**⚠️ PDF İçerik Kontrolü (EN ÖNEMLİ!):**
- ✅ Metinler PDF'den mi alındı? (örnekler kopyalanmadı mı?)
- ✅ İconlar içeriğe UYGUN mu? (örnekteki iconlar aynen kullanılmadı mı?)
- ✅ Madde sayıları PDF'e göre ayarlandı mı? (8 madde örnek, sen 12 yazabilirsin)
- ✅ Placeholder/örnek veri YOK mu?

**BU KURALLARA UYMAYAN SEEDER KABUL EDİLMEYECEK!**

---

## 🎯 SON HATIRLATMA

ChatGPT, **örnekleri referans al ama ASLA AYNEN KOPYALAMA!**

Her seeder **PDF'deki gerçek içeriği** yansıtmalı. Örnek iconlar, örnek metinler, örnek madde sayıları sadece **format göstermek için**. Sen PDF'i oku, içeriğe uygun iconları seç, gerçek verileri kullan!
