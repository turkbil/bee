<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFX4_301_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFX4-301')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFX4-301');
            return;
        }

        $variants = [
            [
                'sku' => 'EFX4-301-2700',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFX4 301 - 2700 mm Direk',
                'short_description' => '2700 mm kaldırma yüksekliği, alçak kapı geçişleri ve konteyner içi yükleme boşaltmalarda ideal; modüler Li-Ion enerji ile minimum duruş, yüksek manevra kabiliyeti.',
                'body' => '<section><h3>2700 mm Mast: Alçak Geçişlere Uyum</h3><p>Alçak girişli üretim alanları, konteyner içleri ve düşük asma tavanlı depolarda 2700 mm mast, EFX4 301’in kompakt geometresiyle birleşerek verimliliği artırır. 2428 mm dönüş yarıçapı ile dar koridorlarda yön değiştirme kolaydır. Modüler Li-Ion batarya seti, vardiya arasında hızlı değişim sayesinde planlı duruşları azaltır.</p></section><section><h3>Teknik Odak</h3><p>80V/100Ah standart set, PMS tahrik ile yüksek verim sunar; 11/12 km/s hız aralığı ve 0.29/0.36 m/s kaldırma performansı akıcı akış sağlar. Pnömatik lastikler açık saha ve rampa yaklaşımında güven verir.</p></section><section><h3>Kullanım Önerisi</h3><p>Alçak kapı geçişleri, konteyner içi operasyonlar ve mobil saha lojistiği için uygundur.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Konteyner içi yükleme ve boşaltma'],
                    ['icon' => 'industry', 'text' => 'Alçak tavanlı üretim bölgeleri'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret dönüş dağıtım alanları'],
                    ['icon' => 'car', 'text' => 'Rampa yaklaşımı ve dar manevra alanları'],
                    ['icon' => 'store', 'text' => 'Perakende arka alan mal kabulü'],
                    ['icon' => 'flask', 'text' => 'Kimyasal depolarda sınırlı yükseklikler']
                ]
            ],
            [
                'sku' => 'EFX4-301-3000',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFX4 301 - 3000 mm Direk (Standart)',
                'short_description' => '3000 mm standart mast, geniş görüş ve sağlam yapı ile genel depo operasyonlarının çoğunu kapsar; modüler batarya yapısı ile vardiya uzatma kolaydır.',
                'body' => '<section><h3>3000 mm Mast: Denge ve Görüş</h3><p>Standart 3000 mm konfigürasyon, görüşü artıran yeni mast tasarımıyla güvenli istif sağlar. 1228 mm genişlik ve 3735 mm toplam uzunluk, çoğu raf düzenine uyumludur. Modüler enerji mimarisi, uzaktan şarj ve akü kiralama senaryolarına uygundur.</p></section><section><h3>Teknik Odak</h3><p>11/12 km/s yürüyüş, 0.29/0.36 m/s kaldırma, 15% eğim kabiliyeti ile yoğun tempoda stabil performans sunar. PMS motor ve akıllı BMS düşük tüketim hedefler.</p></section><section><h3>Kullanım Önerisi</h3><p>Genel depo, 3PL, üretim içi taşıma ve perakende dağıtım merkezleri için çok yönlü seçimdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => '3PL koridor içi malzeme akışı'],
                    ['icon' => 'industry', 'text' => 'Üretim hücreleri arası WIP transferi'],
                    ['icon' => 'box-open', 'text' => 'Fulfillment alanında palet besleme'],
                    ['icon' => 'store', 'text' => 'DC’den mağazalara yükleme'],
                    ['icon' => 'car', 'text' => 'Yedek parça rampalarında çevik manevra'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ambalaj alanları']
                ]
            ],
            [
                'sku' => 'EFX4-301-3300',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFX4 301 - 3300 mm Direk',
                'short_description' => '3300 mm mast, orta yükseklikte raflı depolarda daha fazla esneklik sağlar; koridor değerleri ve dönüş yarıçapı dar alanlarda avantaj sunar.',
                'body' => '<section><h3>3300 mm Mast: Orta Seviye İstif</h3><p>Raf yüksekliği 3.0 metreyi biraz aşan depolarda 3300 mm mast daha esnek çözüm sunar. Geliştirilmiş LED farlar ve ferah kabin, operatörün yorgunluğunu azaltır; modüler akü setleri ile vardiya planı esnekleşir.</p></section><section><h3>Teknik Odak</h3><p>Standart şasi geometri ve pnömatik lastikler, rampalarda stabilite sağlar. Akıllı BMS, döngü ve sıcaklık yönetimi ile pil ömrünü uzatır.</p></section><section><h3>Kullanım Önerisi</h3><p>Orta yükseklikte raflı depo ve dağıtım merkezleri için uygundur.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Raflı depolarda orta yükseklik istifi'],
                    ['icon' => 'box-open', 'text' => 'E-ticaret toplama katları'],
                    ['icon' => 'industry', 'text' => 'Endüstriyel hammadde taşıma'],
                    ['icon' => 'store', 'text' => 'Bölgesel dağıtım merkezleri'],
                    ['icon' => 'car', 'text' => 'Rampalı sevkiyat sahaları'],
                    ['icon' => 'flask', 'text' => 'Kimyasal ara stok alanları']
                ]
            ],
            [
                'sku' => 'EFX4-301-3500',
                'variant_type' => 'direk-yuksekligi',
                'title' => 'İXTİF EFX4 301 - 3500 mm Direk',
                'short_description' => '3500 mm mast, daha yüksek raflarda erişim ve operasyon esnekliği sunarken modüler enerji mimarisiyle kesintisiz vardiya imkânı verir.',
                'body' => '<section><h3>3500 mm Mast: Yüksek Erişim</h3><p>Daha yüksek raf ve saha istifi gerektiren operasyonlar için 3500 mm mast, görüşü artırılmış direk yapısıyla güvenle kaldırma sağlar. Modüler batarya, sahadan bağımsız şarj ve kiralama senaryolarını destekler.</p></section><section><h3>Teknik Odak</h3><p>Yüksek erişimle beraber 11/12 km/s yürüyüş ve 15% eğim değeri korunur. PMS motor düşük tüketim ve sessiz çalışmayı bir araya getirir.</p></section><section><h3>Kullanım Önerisi</h3><p>Yüksek raflı depo, konsolidasyon ve bölgesel hub’lar için idealdir.</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yüksek raf istifi ve erişim'],
                    ['icon' => 'industry', 'text' => 'Üretim sonrası bitmiş ürün stokları'],
                    ['icon' => 'box-open', 'text' => 'Hub konsolidasyon alanları'],
                    ['icon' => 'store', 'text' => 'Büyük DC operasyonları'],
                    ['icon' => 'car', 'text' => 'Rampa arkası yoğun yükleme'],
                    ['icon' => 'flask', 'text' => 'Tehlikesiz kimyasal depolama']
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
