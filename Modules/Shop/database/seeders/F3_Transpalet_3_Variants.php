<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class F3_Transpalet_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'F3')->first();
        if (!$m) {
            echo "❌ Master bulunamadı";
            return;
        }

        $variants = [
            [
                'sku' => 'F3-1150x560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F3 - 1150×560 mm Çatal',
                'short_description' => 'EUR paletlerde standart kullanım için 1150×560 mm çatal seti; dar koridorlarda yüksek çeviklik ve hızlı manevra avantajı.',
                'body' => '<section><h3>1150×560 mm: Depo Standardı</h3><p>Bu varyant, 1150 mm çatal uzunluğu ve 560 mm çatal aralığıyla EUR palet (1200×800) odaklı operasyonların standart çözümüdür. 1360 mm dönüş yarıçapı ve 1550 mm toplam uzunluk, raf aralarında akıcı hareket sağlar. 24V/20Ah Li‑Ion tak‑çıkar batarya, kısa molalarda ara şarj ile vardiya sürekliliği sunar. PU teker takımı düşük gürültü ve zemin dostu sürüş üretir.</p></section><section><h4>Teknik Uyum</h4><p>55/150/1150 mm çatal profili, 105 mm kaldırma ve 82 mm alçaltılmış yükseklikle palet alma-verme sırasında kontrollü hareket imkânı verir. Elektromanyetik fren ile güvenli duruş, flip kapaklı batarya bölmesi ile korumalı enerji sistemi birleşir.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'E‑ticaret istasyonlarında EUR palet konsolidasyonu'],
                    ['icon' => 'warehouse', 'text' => '3PL inbound–outbound akışlarında standart palet transferi'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf beslemesi ve hat içi besleme'],
                    ['icon' => 'industry', 'text' => 'Üretim hücrelerinde WIP taşıma ve besleme'],
                    ['icon' => 'cart-shopping', 'text' => 'Cross‑dock alanlarında hızlı geçiş'],
                    ['icon' => 'car', 'text' => 'Otomotiv yedek parça paletlerinde çevik manevra']
                ]
            ],
            [
                'sku' => 'F3-1220x685',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F3 - 1220×685 mm Çatal',
                'short_description' => 'Geniş palet ve karışık yükler için 1220×685 mm; denge, yük desteği ve istif alanında esneklik sağlar.',
                'body' => '<section><h3>1220×685 mm: Geniş Destek</h3><p>1220 mm uzunluk ve 685 mm aralık, geniş tabanlı paletlerde daha iyi yük desteği sunar. Bu konfigürasyon, hassas yük merkezlerinde stabiliteyi artırır ve hat besleme operasyonlarında paletin tabanını daha dengeli kavrar.</p></section><section><h4>Operasyonel Katkı</h4><p>Geniş çatal aralığı, kırılgan/karışık kolilerde devrilme riskini azaltır. 4.0/4.5 km/s hız ve DC sürüş kontrolü, akışın sürekli kalmasını sağlar; PU tekerlek seti sessiz çalışır.</p></section>',
                'use_cases' => [
                    ['icon' => 'flask', 'text' => 'Kimya ve kozmetik kolilerinde geniş taban desteği'],
                    ['icon' => 'pills', 'text' => 'İlaç paletlerinde stabil taşıma'],
                    ['icon' => 'couch', 'text' => 'Mobilya parçalarında geniş yüzeyli paletleme'],
                    ['icon' => 'print', 'text' => 'Ambalaj ve matbaa ürünlerinde ağır‑hafif karışık yükler'],
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasaları ve shrink paketlerde güvenli sevk'],
                    ['icon' => 'warehouse', 'text' => 'Depo içinde ara stok alanlarına taşıma']
                ]
            ],
            [
                'sku' => 'F3-1500x560',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF F3 - 1500×560 mm Çatal',
                'short_description' => 'Uzun yükler ve IBC/tank paletleri için 1500×560 mm; daha uzun destek mesafesi ile güvenli taşıma.',
                'body' => '<section><h3>1500×560 mm: Uzun Yük Uzmanı</h3><p>1500 mm çatal, uzun koliler, boru demetleri veya sıvı konteyner (IBC) paletleri gibi yüklerde gerekli temas alanını sağlar. 1.5 ton kapasiteyi 600 mm yük merkezinde sağlayan yapı, uzun paletlerde dengeyi korumaya yardımcı olur.</p></section><section><h4>Güvenlik ve Verim</h4><p>Flip kapaklı batarya bölmesi ve elektromanyetik fren ile güvenlik üst seviyededir. 0.017/0.020 m/s kaldırma hızları yükün kontrollü yönetimine olanak tanır.</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Endüstriyel boru ve profil demetlerinde taşıma'],
                    ['icon' => 'flask', 'text' => 'Kimya tesislerinde IBC tank palet desteği'],
                    ['icon' => 'hammer', 'text' => 'Yapı malzemesi paletlerinde uzun ürünler'],
                    ['icon' => 'car', 'text' => 'Otomotiv gövde parçalarında geniş temas ihtiyacı'],
                    ['icon' => 'warehouse', 'text' => 'Ara stok alanları arası uzun palet transferi'],
                    ['icon' => 'box-open', 'text' => 'Büyük koli ve özel palet lojistiği']
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
        echo "✅ Variants: F3 (3 varyant)\n";
    }
}
