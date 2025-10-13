<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL403_HV_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFL403-HV')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı');
            return;
        }
        $variants = [
            [
                'sku' => 'EFL403-HV-1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL403-HV 1070 mm Çatal',
                'short_description' => 'Dar koridor odaklı, kısa çatal ile manevra üstünlüğü; rampa beslemede seri hareket.',
                'body' => '<section><h2>İXTİF EFL403-HV 1070 mm Çatal</h2><p>1070 mm çatal ile dar koridor ve kapı girişlerinde dönme yarıçapı avantajı belirginleşir. Kısa manevra zamanları, rampalı alanlara yaklaşmayı hızlandırır; mast amortisörü kırılgan yüklerde koruma sağlar. 309V/173Ah LFP akü 1C hızlı şarjla 1-1.2 saatte dolarken, PMSM tahrik düşük hızlarda dahi yüksek tork sunar. IPX4/IP67 koruma ile yağmur, çamur ve tozlu sahalarda güven veren bir performans elde edilir.</p></section><section><h3>Teknik</h3><p>1070 mm çatal ile dar koridor ve kapı girişlerinde dönme yarıçapı avantajı belirginleşir. Kısa manevra zamanları, rampalı alanlara yaklaşmayı hızlandırır; mast amortisörü kırılgan yüklerde koruma sağlar. 309V/173Ah LFP akü 1C hızlı şarjla 1-1.2 saatte dolarken, PMSM tahrik düşük hızlarda dahi yüksek tork sunar. IPX4/IP67 koruma ile yağmur, çamur ve tozlu sahalarda güven veren bir performans elde edilir. Teknik odakta; 309V/173Ah, 24/25 km/s ve %25/%30 gibi veriler, senaryonun gerektirdiği hız ve tırmanmayı sağlar.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Standart EUR paletlerde hızlı giriş-çıkış (kapı/koridor)'],
                    ['icon' => 'warehouse', 'text' => 'Dar raf aralıklarında konumlama ve düzeltme'],
                    ['icon' => 'car', 'text' => 'Treyler ve rampa üstü yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Üretim içi WIP taşıma ve ara stok besleme'],
                    ['icon' => 'building', 'text' => 'Açık saha stok alanlarında ağır palet transferi'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk hava koşullarında ısıtmalı şarj ile kesintisiz operasyon']
                ]
            ],
            [
                'sku' => 'EFL403-HV-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL403-HV 1150 mm Çatal',
                'short_description' => 'Standart paletlerde denge ve verim; açık alan stok sahalarında çok yönlülük.',
                'body' => '<section><h2>İXTİF EFL403-HV 1150 mm Çatal</h2><p>1150 mm çatal uzunluğu, EUR ve ISO paletlerin çoğunda optimum denge ve hızlı yerleştirme sağlar. VCU dönüş hız kontrolü ile dar alanlarda güven artar; çift su soğutma yaz aylarında güç düşüşünü engeller. Enerji verimliliğinde PMSM + yüksek voltaj kombinasyonu yaklaşık %15 tasarruf potansiyeli sunar.</p></section><section><h3>Teknik</h3><p>1150 mm çatal uzunluğu, EUR ve ISO paletlerin çoğunda optimum denge ve hızlı yerleştirme sağlar. VCU dönüş hız kontrolü ile dar alanlarda güven artar; çift su soğutma yaz aylarında güç düşüşünü engeller. Enerji verimliliğinde PMSM + yüksek voltaj kombinasyonu yaklaşık %15 tasarruf potansiyeli sunar. Teknik odakta; 309V/173Ah, 24/25 km/s ve %25/%30 gibi veriler, senaryonun gerektirdiği hız ve tırmanmayı sağlar.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Standart EUR paletlerde hızlı giriş-çıkış (kapı/koridor)'],
                    ['icon' => 'warehouse', 'text' => 'Dar raf aralıklarında konumlama ve düzeltme'],
                    ['icon' => 'car', 'text' => 'Treyler ve rampa üstü yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Üretim içi WIP taşıma ve ara stok besleme'],
                    ['icon' => 'building', 'text' => 'Açık saha stok alanlarında ağır palet transferi'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk hava koşullarında ısıtmalı şarj ile kesintisiz operasyon']
                ]
            ],
            [
                'sku' => 'EFL403-HV-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL403-HV 1220 mm Çatal',
                'short_description' => 'Uzun ve hacimli yüklerde dengeyi artıran çözüm; geniş raf açıklıklarında avantaj.',
                'body' => '<section><h2>İXTİF EFL403-HV 1220 mm Çatal</h2><p>1220 mm çatal, hacimli yüklerde ağırlık dağılımını iyileştirerek mast stresini azaltır. Açık alan sevkiyatlarında rüzgâr etkisine karşı yük stabilitesini artırır; hidrolik yağ soğutma ardışık kaldırmalarda ısınmayı kontrol eder. Telemetri ve çevrimiçi destek, performans parametrelerini izleyip proaktif bakım sağlar.</p></section><section><h3>Teknik</h3><p>1220 mm çatal, hacimli yüklerde ağırlık dağılımını iyileştirerek mast stresini azaltır. Açık alan sevkiyatlarında rüzgâr etkisine karşı yük stabilitesini artırır; hidrolik yağ soğutma ardışık kaldırmalarda ısınmayı kontrol eder. Telemetri ve çevrimiçi destek, performans parametrelerini izleyip proaktif bakım sağlar. Teknik odakta; 309V/173Ah, 24/25 km/s ve %25/%30 gibi veriler, senaryonun gerektirdiği hız ve tırmanmayı sağlar.</p></section><section><h3>Sonuç</h3><p>0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Standart EUR paletlerde hızlı giriş-çıkış (kapı/koridor)'],
                    ['icon' => 'warehouse', 'text' => 'Dar raf aralıklarında konumlama ve düzeltme'],
                    ['icon' => 'car', 'text' => 'Treyler ve rampa üstü yükleme/boşaltma'],
                    ['icon' => 'industry', 'text' => 'Üretim içi WIP taşıma ve ara stok besleme'],
                    ['icon' => 'building', 'text' => 'Açık saha stok alanlarında ağır palet transferi'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk hava koşullarında ısıtmalı şarj ile kesintisiz operasyon']
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
        }
        $this->command->info("🧩 Varyantlar eklendi: EFL403-HV");
    }
}
