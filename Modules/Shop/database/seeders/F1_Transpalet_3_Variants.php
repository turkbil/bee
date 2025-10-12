<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F1_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'F1')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı (F1)'); return; }

        $variants = [
            [
                'sku' => 'F1-1150x685',
                'variant_type' => 'catal-genisligi',
                'title' => 'İXTİF F1 - 1150×685 mm Çatal',
                'short_description' => '1150×685 mm çatallar; geniş paletlerde daha güvenli denge ve kavrama sunar. Aynı F platformu ve AGM 24V/65Ah akü ile entegre şarj sistemi korunur.',
                'long_description' => '<section><h3>Geniş Paletler için Dengeli Kavrama</h3><p>1150×685 mm çatal konfigürasyonu, geniş tabanlı palet ve kasalarda ağırlık dağılımını iyileştirerek daha kararlı hareket sunar. F1 in platform temelli tasarımı bu varyantta da korunur; filo yönetiminde aynı kumanda kolu ve modüler parça yapısı sayesinde eğitim ve servis kolaylaşır.</p></section><section><h3>Teknik Uyum</h3><p>Temel performans değerleri (4.0/4.5 km/s yürüyüş, 0.020/0.026 m/s kaldırma, 1426 mm dönüş) F1 ile aynıdır. Poliüretan tahrik ve tandem yük tekerleri zemin korumasını artırır. 24V/65Ah AGM akü ve 24V/10A entegre şarj ile gün içinde dağınık şarj senaryolarında 5–6 saatlik gerçek çalışma korunur.</p></section><section><h3>Sonuç</h3><p>Geniş palet ağırlık merkezlerinde titreşimsiz ve dengeli taşıma arayan işletmeler için doğru seçimdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Standart EUR palet + kasa kombinasyonlarında stabil taşıma'],
                    ['icon' => 'warehouse', 'text' => '3PL operasyonlarında karma palet akışları'],
                    ['icon' => 'store', 'text' => 'Perakende DC’de kübik hacmi yüksek paletler'],
                    ['icon' => 'car', 'text' => 'Otomotivde kap genişlikleri yüksek paletler'],
                    ['icon' => 'industry', 'text' => 'Endüstriyel üretim hatlarında geniş kalıp paletleri'],
                    ['icon' => 'print', 'text' => 'Ambalaj-matbaa sektöründe geniş taban paletler']
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
                'long_description' => json_encode(['tr' => $v['long_description']], JSON_UNESCAPED_UNICODE),
                'use_cases' => json_encode($v['use_cases'], JSON_UNESCAPED_UNICODE),
                'is_master_product' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]);
        }

        $this->command->info('✅ Variants eklendi: F1');
    }
}
