# V4 SEEDER - TRANSPALET

PDF → TEK Seeder (ShopProduct + SeoSettings) | TR + EN

## KURALLAR

**Genel:** PDF veri | namespace Modules\Shop\Database\Seeders\V4 | JSON_UNESCAPED_UNICODE | Category: 2=Transpalet

**DİL:** TR + EN | `json_encode(['tr'=>'...', 'en'=>'...'])`

**MARKA:** ❌ "EP" → ✅ "iXtif" (X büyük)

**PRIMARY SPECS (5 ZORUNLU):** capacity, stabilizing_wheel, battery, charger, turning_radius

**8 VARIATION:** technical, benefit, slogan, motto, short, long, comparison, keywords | TR+EN | ÖRNEK SEEDER'a bak

**ONE-LINE:** 120-150 karakter | TR+EN

**BODY:** 5 bölüm (800-1200 kel) | 1)Hook 2)Tanıtım 3)Teknik(PDF!) 4)Senaryo 5)Özet

**❌ UYDURMA:** Rakam/istatistik (PDF'de yoksa YAZMA!)

**FAQ:** 12-15 | Technical/Usage/Maintenance/Performance | TR+EN | ❌ Fiyat/Satın alma sorma

**SEO:** 9 robots_meta | Title 55-60 | Desc 150-160 | TR+EN

**EKSİK VERİ:** Boş bırak (null YAZMA)

## ÖRNEK

```php
<?php
namespace Modules\Shop\Database\Seeders\V4;
use Illuminate\Database\Seeder;
use Modules\Shop\app\Models\ShopProduct;
use Modules\SeoManagement\app\Models\SeoSetting;

class Transpalet{MODEL}Seeder extends Seeder
{
    public function run(): void
    {
        $product = ShopProduct::updateOrCreate(
            ['sku' => 'IXTIF-{MODEL}-{KG}'],
            [
                'sku' => 'IXTIF-{MODEL}-{KG}',
                'title' => json_encode(['tr'=>'iXtif {M} Transpalet', 'en'=>'iXtif {M} Pallet Truck'], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr'=>'...', 'en'=>'...'], JSON_UNESCAPED_UNICODE),
                'one_line_description' => json_encode(['tr'=>'{120-150}', 'en'=>'{120-150}'], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode(['tr'=>'{30-50}', 'en'=>'{30-50}'], JSON_UNESCAPED_UNICODE),
                'body' => json_encode(['tr'=>'<section>...</section>', 'en'=>'<section>...</section>'], JSON_UNESCAPED_UNICODE),
                'category_id' => 2,
                'brand_id' => 1,
                'is_active' => true,
                'base_price' => 0.00,
                'price_on_request' => true,

                'primary_specs' => json_encode([
                    'capacity' => '{1500 kg}',
                    'stabilizing_wheel' => '{Opsiyonel}',
                    'battery' => '{24V 20Ah Li-Ion}',
                    'charger' => '{Entegre}',
                    'turning_radius' => '{1360 mm}'
                ], JSON_UNESCAPED_UNICODE),

                'content_variations' => json_encode([
                    'li-ion-battery' => [
                        'technical' => ['tr'=>'{15-25 kel}', 'en'=>'{15-25 w}'],
                        'benefit' => ['tr'=>'{10-20 kel}', 'en'=>'{10-20 w}'],
                        'slogan' => ['tr'=>'{3-5}', 'en'=>'{3-5}'],
                        'motto' => ['tr'=>'{2-3}', 'en'=>'{2-3}'],
                        'short' => ['tr'=>'{10-15}', 'en'=>'{10-15}'],
                        'long' => ['tr'=>'{50-80}', 'en'=>'{50-80}'],
                        'comparison' => ['tr'=>'{15-25}', 'en'=>'{15-25}'],
                        'keywords' => ['tr'=>'li-ion, şarj', 'en'=>'li-ion, charge']
                    ]
                    // 7-8 özellik daha
                ], JSON_UNESCAPED_UNICODE),

                'technical_specs' => json_encode(['tr'=>['Kapasite'=>'...'], 'en'=>['Capacity'=>'...']], JSON_UNESCAPED_UNICODE),
                'features' => json_encode(['tr'=>['...'], 'en'=>['...']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode(['tr'=>['...'], 'en'=>['...']], JSON_UNESCAPED_UNICODE),
                'faq_data' => json_encode(['tr'=>[['category'=>'Technical','question'=>'...','answer'=>'...']], 'en'=>[...]], JSON_UNESCAPED_UNICODE),
                'keywords' => json_encode(['tr'=>['primary'=>[], 'synonyms'=>[], 'usage_jargon'=>[]], 'en'=>[...]], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]
        );

        SeoSetting::updateOrCreate(
            ['seoable_type' => 'Modules\Shop\app\Models\ShopProduct', 'seoable_id' => $product->product_id],
            [
                'titles' => json_encode(['tr'=>'{55-60}', 'en'=>'{55-60}'], JSON_UNESCAPED_UNICODE),
                'descriptions' => json_encode(['tr'=>'{150-160}', 'en'=>'{150-160}'], JSON_UNESCAPED_UNICODE),
                'og_titles' => json_encode(['tr'=>'{55-60}', 'en'=>'{55-60}'], JSON_UNESCAPED_UNICODE),
                'og_descriptions' => json_encode(['tr'=>'{150-160}', 'en'=>'{150-160}'], JSON_UNESCAPED_UNICODE),
                'og_image' => null,
                'robots_meta' => json_encode(['index'=>true, 'follow'=>true, 'max-snippet'=>-1, 'max-image-preview'=>'large', 'max-video-preview'=>-1, 'noarchive'=>false, 'noimageindex'=>false, 'notranslate'=>false, 'indexifembedded'=>true, 'noydir'=>true, 'noodp'=>true], JSON_UNESCAPED_UNICODE),
                'schema_type' => 'Product',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
```

## ÇIKTI

TEK DOSYA: `Transpalet{MODEL}Seeder.php` | SANDBOX İNDİRME LİNK VER!

**DETAYLI ÖRNEK SEEDER dosyasına bak!** Format, içerik, yapı oradan öğren!
