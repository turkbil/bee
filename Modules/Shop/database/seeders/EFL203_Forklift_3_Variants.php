<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFL203_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFL203')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFL203');
            return;
        }

        $variants = [
            [
                'sku' => 'EFL203-230AH',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL203 - 80V 230Ah Li-Ion',
                'short_description' => 'Standart 80V 230Ah Li-Ion paket; fırsat şarjı ile gün boyu akış, düşük bakım ve sabit performans. Depo içi ve açık alan karma operasyonları için dengeli çözüm.',
                'body' => '<section><h3>Standart enerji paketi</h3><p>80V 230Ah Li-Ion batarya, kısa fırsat şarj molalarıyla uzun vardiya dilimlerini destekler. Düşük iç direnç ve akıllı BMS yönetimi, tutarlı akım beslemesi sağlayarak kaldırma ve sürüşte hissedilir bir süreklilik yaratır.</p></section><section><h3>Operasyonel uyum</h3><p>Günlük tek vardiya veya orta yoğunluklu iki vardiya akışlarında, 14/15 km/s hız, %15/%20 eğim ve 0.29/0.36 m/s kaldırma değerleri ile forklift hat besleme, yükleme-boşaltma ve depo içi transferlerde verimli çalışır.</p></section><section><h3>Bakım ve TCO</h3><p>Li-Ion kimya, eşitleme şarjı ve su ekleme gerektirmez; bu da servis duruşlarını minimuma indirir ve toplam sahip olma maliyetini düşürür.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Tek vardiya depo içi malzeme akışı'],
                    ['icon' => 'box-open', 'text' => 'Cross-dock yükleme ve palet dönüşü'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı besleme ve WIP taşıma'],
                    ['icon' => 'building', 'text' => 'Açık saha stok alanlarında sürekli besleme'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG dağıtım merkezlerinde inbound süreçleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv komponent kabul ve sevki']
                ]
            ],
            [
                'sku' => 'EFL203-460AH',
                'variant_type' => 'batarya-tipi',
                'title' => 'İXTİF EFL203 - 80V 460Ah Li-Ion',
                'short_description' => 'Yüksek kapasiteli 80V 460Ah paket; çok vardiyalı ve ağır görevlerde uzun otonomi. Daha az şarj durağı, daha yüksek üretkenlik ve stabil güç teslimi sağlar.',
                'body' => '<section><h3>Uzun otonomi</h3><p>80V 460Ah batarya çok vardiyalı, ara vermeyen süreçlerde şarj pencerelerini seyrekleştirir. Yüksek kapasite, voltaj düşümüne dirençli çizgisel güç karakteriyle kaldırma ve ivmelenmede performans sürekli kılınır.</p></section><section><h3>Ağır görev temposu</h3><p>Yoğun rampa trafiği, üst üste yükleme ve sık dur-kalk senaryolarında büyük paket, ısı yönetimi ve BMS ile birlikte verimliliği korur; soğukta dahi çıkış gücü stabil kalır.</p></section><section><h3>Planlama esnekliği</h3><p>Daha uzun çalışma pencereleri, filo yoğunluğu ve şarj altyapısı üzerinde esneklik sağlar; pik saatlerde kapasite darboğazlarını azaltır.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Çok vardiyalı 3PL operasyonları'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk zincirde uzun süreli besleme'],
                    ['icon' => 'industry', 'text' => 'Ağır üretim sahalarında tam gün hat besleme'],
                    ['icon' => 'car', 'text' => 'Otomotiv montaj öncesi sekanslama'],
                    ['icon' => 'store', 'text' => 'Perakende DC yüksek hacimli transfer'],
                    ['icon' => 'flask', 'text' => 'Kimya sahasında turlu dağıtım']
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
        $this->command->info('🧬 Variants eklendi: EFL203');
    }
}
