<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL603_HV_6_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFL603-HV-6')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFL603-HV-6');
            return;
        }
        $variants = [
            [
                'sku' => 'EFL603-HV-6-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL603 HV - 1220 mm Çatal',
                'short_description' => '1220 mm standart çatal uzunluğu ile 6 ton sınıfında dış mekân operasyonlarında hız, stabilite ve yüksek voltajlı Li-ion verimliliği sunar. Rampa ve dar koridorlarda kontrollü manevra sağlar.',
                'body' => '<section><h3>1220 mm Standart: Çok Yönlü Kullanım</h3><p>1220 mm çatal, genel sanayi palet ölçülerinde optimum denge sağlar. 1845 mm çatal taşıyıcı genişliğiyle birlikte iri yüklerin kavraması kolaylaşır. 309V Li‑Ion batarya ve PMSM sürüş paketi, 25/26 km/s hız ile dış sahada akışı hızlandırır; %30/%34 eğim performansı ise rampaları tek seferde aşar. Su/yağ soğutmalı ısı yönetimi, uzun vardiyalarda performans sürekliliği sunar.</p></section><section><h3>Operasyonel Kazanımlar</h3><p>IPX4 genel koruma ve HV bileşenlerinde IP67 sızdırmazlık, yağmur ve sıçrama sularına karşı güven verir. VCU dönüş hızını açıya göre sınırlandırırken aşırı hız uyarısı operatör hatalarını azaltır. Mast tamponlama yük hasarını minimize eder. 1C hızlı şarj ile vardiya aralarında ~1–1.2 saatte tam dolum mümkündür.</p></section><section><h3>Uygulamalar</h3><p>Metal işleme, inşaat lojistiği, ağır paketleme ve liman sahasında genel amaçlı kullanımlar için ideal uzunluktur.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Genel sanayide palet ve kasa taşıma'],
                    ['icon' => 'warehouse', 'text' => 'Depo rampalarında hızlı yükleme'],
                    ['icon' => 'building', 'text' => 'Şantiye içi malzeme akışı'],
                    ['icon' => 'box-open', 'text' => 'Büyük hacimli palet transferi'],
                    ['icon' => 'car', 'text' => 'Otomotiv parça lojistiği'],
                    ['icon' => 'flask', 'text' => 'Kimyasal içeren ambalajların güvenli taşınması']
                ]
            ],
            [
                'sku' => 'EFL603-HV-6-1520',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFL603 HV - 1520 mm Çatal',
                'short_description' => '1520 mm uzun çatal, esnek olmayan yüklerde dengeyi artırır; geniş tabanlı palet ve kalın levhalarda güvenli kavrama sunar. Yük merkezi yönetimi için operatör uyarıları ve VCU avantajlıdır.',
                'body' => '<section><h3>1520 mm Uzun: Geniş Yükler İçin</h3><p>Standart 1220 mm’ye göre uzatılmış 1520 mm çatal, geniş tabanlı palet ve homojen olmayan yüklerde temas yüzeyini artırarak devrilme riskini azaltır. 600 mm yük merkezi korunarak planlanan yüklerde denge iyileşir; ataşmanla birlikte efektif kapasite için uygulama analizi önerilir. PMSM çekiş ve su soğutmalı termal mimari, yüksek kütleli yüklerde dahi akıcı hızlanma sağlar.</p></section><section><h3>Dayanıklılık ve Verim</h3><p>IPX4/IP67 koruma, yağış altında dış sahada çalışmayı güvenli kılar. 1C hızlı şarj ile vardiya kesintileri kısa tutulur. Mast tamponlama ve VCU kontrollü dönüş sayesinde uzun çatalın moment etkisi operatör tarafından daha kontrollü yönetilir.</p></section><section><h3>Uygulamalar</h3><p>Döküm parçalar, kalın sac paketleri, geniş paletli makine kasaları ve liman konteyner iç destek işlerinde tercih edilir.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Kalıp ve kalın levha taşıma'],
                    ['icon' => 'building', 'text' => 'Prefabrik panel ve kasa hareketleri'],
                    ['icon' => 'warehouse', 'text' => 'Geniş paletli makinelerin transferi'],
                    ['icon' => 'snowflake', 'text' => 'Dış sahada değişken hava koşullarında kullanım'],
                    ['icon' => 'box-open', 'text' => 'Ağır paketlerin hassas yerleşimi'],
                    ['icon' => 'briefcase', 'text' => 'Kamu altyapı projelerinde ağır parça sevki']
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
