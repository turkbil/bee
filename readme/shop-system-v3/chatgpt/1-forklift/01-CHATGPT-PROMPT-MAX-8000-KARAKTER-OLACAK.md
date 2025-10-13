# CHATGPT SHOP SEEDER

PDF'den 3 seeder oluştur: 1_Master, 2_Detailed, 3_Variants

## ⚠️ KURALLAR

**Genel:** PDF veri | `['key'=>'value']` | namespace Modules\Shop\Database\Seeders | 1=Forklift, 2=Transpalet, 3=İstif, 4=Sipariş, 5=Otonom, 6=Reach | JSON_UNESCAPED_UNICODE

**ICON:** `[['icon'=>'...', 'text'=>'...']]` | use_cases, competitive_advantages, target_industries, features, accessories, certifications

**FA7.1:** box-open, store, warehouse, snowflake, pills, car, industry, bolt, battery-full, arrows-alt, shield-alt, star, briefcase, building, cart-shopping, flask, microchip, cog, plug, certificate, award | ❌ battery-bolt, hand-paper, weight, steering, wheels

**MARKA:** ASLA "EP"! → "İXTİF" | ❌ "EP F4 201" → ✅ "İXTİF F4 201"

**VARYANT:** Her varyant UNIQUE (body + use_cases özel)

**ÇOKLU MODEL:** PDF'de 2+ model varsa (F4-201, F4-202), her biri için AYRI 3 dosya! (örn: F4_201_1/2/3 + F4_202_1/2/3)

**AKSESUAR/SERTİFİKA:** PDF'de yoksa STANDART ekle → 4 aksesuar (şarj, tekerlek, batarya) | CE sertifika zorunlu

**ÖRNEKLER FORMAT!** PDF'e uygun icon, kopyalama YASAK!

---

## 1️⃣ MASTER

```php
<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class {MODEL}_{KATEGORİ}_1_Master extends Seeder {
    public function run(): void {
        $sku = '{F4-201}';
        $titleTr = '{İXTİF F4 201 - 2.0 Ton Li-Ion Transpalet}';

        DB::table('shop_products')->updateOrInsert(['sku' => $sku], [
            'sku' => $sku,
            'title' => json_encode(['tr' => $titleTr], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => Str::slug($titleTr)], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => '{30-50}'], JSON_UNESCAPED_UNICODE),
            'category_id' => 1,
            'brand_id' => 1,
            'is_master_product' => true,
            'is_active' => true,
            'base_price' => 0.00,
            'price_on_request' => true,
            'created_at' => now(),
            'updated_at' => now(),
            'published_at' => now(),
            'technical_specs' => json_encode(['Kapasite' => '...'], JSON_UNESCAPED_UNICODE),
            'features' => json_encode([['icon' => 'battery-full', 'text' => '...']], JSON_UNESCAPED_UNICODE), // 8x
        ]);
    }
}
```

## 2️⃣ DETAILED

```php
<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {MODEL}_{KATEGORİ}_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', '{SKU}')->first();
        DB::table('shop_products')->where('product_id', $p->product_id)->update([

            // LONG: 800-1500, 3 bölüm
            'body' => json_encode(['tr' => '<section><h2>{Başlık}</h2><p>{100-150}</p></section><section><h3>Teknik</h3><p>{200-300}</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>'], JSON_UNESCAPED_UNICODE),

            // 4 madde
            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '...'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '...'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '...'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => '...']
            ], JSON_UNESCAPED_UNICODE),

            // 6 madde
            'highlighted_features' => json_encode([['icon' => 'battery-full', 'title' => '...', 'description' => '...']], JSON_UNESCAPED_UNICODE),

            // 8 madde
            'use_cases' => json_encode([['icon' => 'box-open', 'text' => '...']], JSON_UNESCAPED_UNICODE),

            // 4-6 madde
            'competitive_advantages' => json_encode([['icon' => 'bolt', 'text' => '...']], JSON_UNESCAPED_UNICODE),

            // MİN 20!
            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E-ticaret'],
                ['icon' => 'warehouse', 'text' => '3PL'],
                ['icon' => 'store', 'text' => 'Perakende'],
                ['icon' => 'snowflake', 'text' => 'Gıda'],
                ['icon' => 'pills', 'text' => 'İlaç'],
                ['icon' => 'car', 'text' => 'Otomotiv'],
                ['icon' => 'tshirt', 'text' => 'Tekstil'],
                ['icon' => 'industry', 'text' => 'Sanayi'],
                ['icon' => 'flask', 'text' => 'Kimya'],
                ['icon' => 'microchip', 'text' => 'Elektronik'] // MIN 20
            ], JSON_UNESCAPED_UNICODE),

            // KATEGORİ YOK!
            'warranty_info' => json_encode(['coverage' => 'Makine 12 ay, Li-Ion batarya 24 ay garanti.', 'duration_months' => 12, 'battery_warranty_months' => 24], JSON_UNESCAPED_UNICODE),

            // standart → price=null
            'accessories' => json_encode([
                ['icon' => 'cog', 'name' => '{Opsiyonel}', 'description' => '...', 'is_standard' => false, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => '{Standart}', 'description' => '...', 'is_standard' => true, 'price' => null]
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']], JSON_UNESCAPED_UNICODE),

            // SON SORUDA İXTİF!
            'faq_data' => json_encode([
                ['question' => '{Soru 1}', 'answer' => '{Yanıt - İXTİF YOK}'],
                ['question' => 'Garanti?', 'answer' => 'Makine 12 ay, akü 24 ay. İXTİF 0216 755 3 555.'] // 10-12, son İXTİF
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);
    }
}
```

## 3️⃣ VARIANTS

```php
<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class {MODEL}_{KATEGORİ}_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', '{SKU}')->first();
        $variants = [
            [
                'sku' => '{SKU-VARIANT}',
                'variant_type' => 'catal-uzunlugu',
                'title' => '{İXTİF + Model + Varyant}',
                'short_description' => '{30-50}',
                'body' => '{800-1200 HTML}',
                'use_cases' => [['icon' => 'box-open', 'text' => '...']] // 6x
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
    }
}
```

## ✅ KONTROL

**Format:** `[['icon'=>'...','text'=>'...']]` | FA7.1 | JSON_UNESCAPED_UNICODE

**İçerik:** Target MIN 20 | Warranty kategori YOK | Standart price=null | FAQ son İXTİF | Long 3 bölüm

**PDF:** Metinler PDF'den | İcon uygun | Madde PDF'e göre | Placeholder YOK

## ÇIKTI

3 DOSYA: `{MODEL}_{KATEGORİ}_1_Master.php`, `_2_Detailed.php`, `_3_Variants.php` | SANDBOX LİNK!
