<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JX1_HD_Siparis_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'JX1-HD')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı (JX1-HD)');
            return;
        }

        $variants = [
            [
                'sku' => 'JX1-HD-LI360',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF JX1-HD Li-Ion 48V/360Ah',
                'short_description' => 'Li-Ion 360Ah paket; fırsat şarjına uygun, düşük bakım ve tutarlı performans isteyen 7/24 operasyonlar için optimize edilmiştir. Yüksek kaldırma/indirme hızlarıyla toplama çevrimlerini kısaltır.',
                'body' => '<section><h3>Li-Ion 48V/360Ah ile Kesintisiz Operasyon</h3><p>Bu varyant, JX1-HD platformunu yüksek kapasiteli 48V/360Ah Li-ion batarya ile eşleştirir. Rejeneratif fren ve AC tahrik ile enerji geri kazanımı sağlanır; fırsat şarjı sayesinde vardiya arasında kısa duruşlarla yüksek kullanılabilirlik elde edilir. 39.4/55.1 fpm kaldırma ve 68.9/59.1 fpm indirme hızları, 6.5 mph yol hızıyla birleşerek sipariş toplama çevrimlerini hızlandırır. 210 inç operatör yükseltme ve 248.8 inç kaldırma yüksekliği, çok katlı raflara güvenli erişim sunar; 63 inç dönüş yarıçapı ise dar koridorlarda çevikliği korur.</p></section><section><h3>Teknik Odak</h3><p>Toplam 1200 lb kapasite (Q1 300 / Q2 700 / Q3 200) dengeli yük dağılımı sağlar. Poly tekerlekler, sessiz ve düşük yuvarlanma direnci ile iç mekân, düz zeminlerde optimum tutuş sunar. Elektronik direksiyon ve elektromanyetik park freni, hassas yaklaşım ve güvenli sabitlemeyi bir arada getirir. 30/50/60/160A şarj seçenekleri filo altyapılarına uyarlanabilir.</p></section><section><h3>Sonuç</h3><p>Yoğun vardiya düzenlerinde yüksek kullanılabilirlik ve düşük toplam sahip olma maliyeti için doğru tercihtir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Yüksek SKU yoğunluğunda hızlı toplama turları'],
                    ['icon' => 'warehouse', 'text' => '3PL içinde sürekli vardiya operasyonları'],
                    ['icon' => 'store', 'text' => 'Perakende DCM’de çok katlı raf erişimi'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG’de yoğun sipariş konsolidasyonu'],
                    ['icon' => 'industry', 'text' => 'Üretim besleme hatlarında kesintisiz ikmal'],
                    ['icon' => 'bolt', 'text' => 'Enerji verimliliği odaklı fırsat şarj planı']
                ]
            ],
            [
                'sku' => 'JX1-HD-LA210',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF JX1-HD Kurşun-Asit 48V/210Ah',
                'short_description' => 'Kurşun-asit 210Ah ekonomik çözüm; tek vardiya ve planlı şarj pencerelerine sahip operasyonlarda dengeli performans sunar. Bakım gerektiren sistemlerde düşük ilk yatırım avantajı sağlar.',
                'body' => '<section><h3>Uygun Bütçeyle Güvenilir Performans</h3><p>48V/210Ah kurşun-asit batarya, tek vardiya odaklı ve planlı şarj pencerelerine sahip tesisler için ekonomik bir çözüm sunar. JX1-HD’nin 6.5 mph yol hızı ve dengeli mast mimarisi ile gündelik toplama görevlerinde tutarlı sonuçlar elde edilir. Rejeneratif fren ve elektromanyetik park sistemi, güvenli sürüş ve sabitleme sağlar.</p></section><section><h3>Teknik Odak</h3><p>Toplam 1200 lb kapasite; 210 inç operatör yükseltme; 63 inç dönüş yarıçapı; 88.6 inç kapalı yükseklik. Poly tekerlekler düz ve pürüzsüz zeminlerde sessiz çalışır. Şarj altyapısında 30/50/60/160A alternatifleriyle esnek planlama yapılabilir.</p></section><section><h3>Sonuç</h3><p>Başlangıç maliyetini kontrol altında tutarken üretkenliği artırmak isteyen operasyonlar için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Tek vardiya e-ticaret toplama'],
                    ['icon' => 'warehouse', 'text' => 'Bölgesel depo raf ikmali'],
                    ['icon' => 'store', 'text' => 'Perakende geri toplama ve sayım'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG haftalık yoğunluklarında esnek kullanım'],
                    ['icon' => 'industry', 'text' => 'Küçük montaj hatlarında yardımcı görevler'],
                    ['icon' => 'battery-full', 'text' => 'Planlı şarj pencereleri ile düzenli çalışma']
                ]
            ],
            [
                'sku' => 'JX1-HD-210IN-OP',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF JX1-HD 210” Operatör Yükseltme',
                'short_description' => '210 inç operatör stand yüksekliği ile çok katlı raflarda maksimum erişim. LED far, elektronik direksiyon ve 6.5 mph hız ile net görüş ve akıcı hareket. İç mekân düz zemin için optimize edilmiştir.',
                'body' => '<section><h3>Maksimum Erişim</h3><p>Bu varyant, 210 inç operatör yükseltmesi ile çok katlı raflara güvenli erişim sağlar. 248.8 inç kaldırma ve 288.8 inç mast tam açık yüksekliği; yüksek arşiv, yedek parça ve e-ticaret uygulamalarında planlamayı kolaylaştırır.</p></section><section><h3>Teknik Odak</h3><p>63 inç dönüş yarıçapı dar koridorlarda çeviklik sunarken AC sürüş ve rejeneratif fren, hızlanma ve yavaşlamayı kontrollü hale getirir. Poly tekerlekler düz ve pürüzsüz yüzeylerde sessiz ve verimlidir.</p></section><section><h3>Sonuç</h3><p>Yüksek erişim gerektiren operasyonlarda toplama verimini artırmak için geliştirilmiştir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Yüksek raflı e-ticaret alanları'],
                    ['icon' => 'warehouse', 'text' => 'Bölgesel yedek parça arşivleri'],
                    ['icon' => 'store', 'text' => 'Perakende üst kat raf düzeni'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG yüksek raflı stok yönetimi'],
                    ['icon' => 'industry', 'text' => 'Üretimde yüksek lokasyon erişimi'],
                    ['icon' => 'shield-alt', 'text' => 'Güvenli yükselme ve iniş prosedürleri']
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
            $this->command->info("✅ Variant: {$v['sku']}");
        }
    }
}
