<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KPL201H_Transpalet_3_Variants extends Seeder {
    public function run(): void {
        $m = DB::table('shop_products')->where('sku', 'KPL201H')->first();
        if (!$m) {$this->command->error('❌ Master bulunamadı'); return; }

        $variants = [
            [
                'sku' => 'KPL201H-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF KPL201H - 1150 mm Çatal',
                'short_description' => '1150 mm çatal ile EUR paletlerde optimum denge ve manevra: 2000 kg kapasite, 24V/205Ah Li-Ion enerji, 9/12 km/s hız ve dönüşte otomatik hız azaltma.',
                'long_description' => '<section><h3>1150 mm çatal ile standart paletlerde kusursuz denge</h3><p>1150 mm çatal, Avrupa standart palet boyutlarıyla tam uyumlu çalışarak yük dağılımını dengeler ve dar koridorlarda manevrayı kolaylaştırır. 24V/205Ah Li-Ion enerji, fırsat şarjına uyum ve emisyonsuz çalışma avantajıyla kesintisiz akış sağlar. 3.0 kW AC tahrik motoru, yük altında bile seri ivmelenme ve hassas hız kontrolü sunar. Güç destekli direksiyon ve dönüşte otomatik hız azaltma, hem hız hem güvenliği aynı anda sağlar.</p><p>Süspansiyonlu platform ve dolgulu sırt dayama, sık rampa geçişleri ve eşiksiz zemin değişimlerinde operatör konforunu artırır. 734 mm gövde genişliği ve kısa şasi kombinasyonu, yoğun dönüş noktalarında çarpmaları azaltır; elektromanyetik fren sistemi, hedef noktada net ve tekrarlanabilir duruşlar üretir.</p></section><section><h3>Operasyon senaryoları</h3><p>Fulfillment alanlarında sipariş konsolidasyonu, 3PL merkezlerinde hat besleme ve gıda lojistiğinde soğuk oda çevrimleri bu konfigürasyonun öne çıktığı alanlardır. Otomotiv ve elektronik depolarında, raf arası mikrologistik görevlerde 1150 mm çatal çeviklik ve stabilite dengesini kurar.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR paletlerde toplama ve konsolidasyon'],
                    ['icon' => 'warehouse', 'text' => '3PL içi hat besleme ve tampon stok hareketi'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG’de yüksek tempolu mal kabul'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş-çıkış operasyonları'],
                    ['icon' => 'microchip', 'text' => 'Elektronik bileşen depolarında WIP akışı'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça koridor içi transfer']
                ]
            ],
            [
                'sku' => 'KPL201H-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF KPL201H - 1220 mm Çatal',
                'short_description' => '1220 mm çatal, uzun yüklerde ekstra temas ve stabilite sağlar. 2000 kg kapasite, 24V/205Ah Li-Ion ve 3.0 kW AC motor ile verimli ve güvenli taşıma.',
                'long_description' => '<section><h3>1220 mm çatal ile uzun yüklerde daha fazla temas</h3><p>1220 mm çatal, uzun ve esnek paketlerde ek temas yüzeyi sağlayarak bükülme riskini azaltır. KPL201H’nin kısa şasisi ve düşük ağırlık merkezi, uzayan moment kolunu dengeleyerek dar alanlarda dahi güvenli manevra imkânı sunar. Dönüşte otomatik hız azaltma ve elektromanyetik fren, geniş kütleli yüklerde kontrolü sürdürür.</p><p>Li-Ion enerji sistemi fırsat şarjı ile vardiya planlarınızdaki boşlukları değerlendirir; 3.0 kW AC tahrik motoru hat sonu hızlanmalarında akışı toparlar. Operatör konforu sağlayan süspansiyonlu platform ve dolgulu sırt dayama, uzun transferlerde titreşim etkisini azaltır.</p></section><section><h3>Operasyon senaryoları</h3><p>Mobilya ve ev dekorasyonu, ambalaj-karton ve tekstil ruloları gibi hacimli yüklerde 1220 mm konfigürasyon ideal tercihtir. 3PL ve perakende dağıtımda, toplama-yerleştirme görevlerinde stabil ve güvenli bir akış üretir.</p></section>',
                'use_cases' => [
                    ['icon' => 'couch', 'text' => 'Mobilya ve hacimli yük transferi'],
                    ['icon' => 'print', 'text' => 'Ambalaj ve karton palet taşımaları'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil rulo ve hacimli paket hareketi'],
                    ['icon' => 'store', 'text' => 'Perakende DC toplama ve yerleşim'],
                    ['icon' => 'industry', 'text' => 'Üretimden depoya yarı mamul taşıma'],
                    ['icon' => 'briefcase', 'text' => 'Genel depo içi destek görevleri']
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
            $this->command->info('✅ Variant eklendi: ' . $v['sku']);
        }
    }
}
