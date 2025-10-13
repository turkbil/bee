<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EFXZ_251_Forklift_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'EFXZ-251')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: EFXZ-251');
            return;
        }

        $variants = [
            [
                'sku' => 'EFXZ-251-1070',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFXZ 251 - 1070 mm Çatal',
                'short_description' => '1070 mm çatal, kompakt koridorlarda standart paletler için ideal denge sunar. 2.5 ton kapasite, 2316 mm dönüş yarıçapı ve 80V Li-Ion enerji ile hızlı ve güvenli akış sağlar.',
                'body' => '<section><h2>1070 mm: Standart Paletlerde Maksimum Çeviklik</h2><p>EFXZ 251\'in 1070 mm çatal konfigürasyonu, EUR ve ISO paletlerde denge ve çeviklik arasında kusursuz bir denge kurar. Kısa çıkıntı, dar koridorda daha iyi dönüş, rampa yaklaşımında ise kontrollü ağırlık transferi sağlar. 80V 150Ah Li-Ion enerji sistemi fırsat şarjı ile vardiya planlarını esnekleştirir; 11/12 km/s seyir ve 0.29/0.36 m/s kaldırma profiliyle akışı hızlandırır.</p></section><section><h3>Teknik Odak</h3><p>2500 kg kapasite (c=500 mm), 1595 mm dingil mesafesi ve 2316 mm dönüş yarıçapı, 3000 mm standart kaldırma ile birleştiğinde depo içi operasyonların omurgasını oluşturur. Pnömatik lastikler (7.00-12-12PR / 18×7-8-14PR) düzensiz zeminlerde sarsıntıyı azaltır; hidrolik hizmet freni ve mekanik park freni güvenli duruş sağlar.</p></section><section><h3>Sonuç</h3><p>Standart palet akışları için hızlı, güvenli ve ekonomik bir çözüm. 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'warehouse', 'text' => 'Yakın koridor mesafelerinde yoğun WMS akışları'],
                    ['icon' => 'box-open', 'text' => 'Cross-dock alanlarında hızlı palet çevrimi'],
                    ['icon' => 'store', 'text' => 'Perakende DC rampa operasyonları'],
                    ['icon' => 'industry', 'text' => 'Üretim hattı besleme ve çekme'],
                    ['icon' => 'flask', 'text' => 'Kimyasal varil ve IBC palet hareketi'],
                    ['icon' => 'car', 'text' => 'Otomotiv kısmi montaj hücreleri']
                ]
            ],
            [
                'sku' => 'EFXZ-251-1200',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFXZ 251 - 1200 mm Çatal',
                'short_description' => '1200 mm çatal, uzayan yüklerde burun desteğini artırır. 2.5 ton kapasite, Li-Ion enerji ve yenilenmiş gövdeyle ağır hizmette verim sağlar.',
                'body' => '<section><h2>1200 mm: Uzun Yüklerde Ek Destek</h2><p>1200 mm çatal seçeneği, uzun kutu ve sandık yüklerinde burun desteğini artırır, yükün ağırlık merkezini daha ileri taşımaya olanak verir. Bu, yavaş ve kontrollü manevrayı kolaylaştırırken operatörün görüşünü korur. Elektrikli tahrik ve 80V batarya mimarisi, sessiz ve emisyon içermeyen çalışma ortamı sunar.</p></section><section><h3>Teknik Odak</h3><p>2500 kg kapasite ve 500 mm yük merkezi referansıyla, uygulamaya göre izin verilen efektif kapasite tablo üzerinden değerlendirilmelidir. 0.29/0.36 m/s kaldırma ve 0.45/0.50 m/s indirme hızı, kırılgan yüklerde hassas kontrol sağlar.</p></section><section><h3>Sonuç</h3><p>Uzun paketlerde güven, standardın üzerinde burun desteği ve yüksek verim. 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Uzun sandık ve kutu taşımaları'],
                    ['icon' => 'warehouse', 'text' => 'Tampon stok alanı yerleştirme'],
                    ['icon' => 'industry', 'text' => 'Makine üretiminde yarı mamul transferi'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk zincirde uzun koli paletleri'],
                    ['icon' => 'pills', 'text' => 'Medikal ekipman kasaları'],
                    ['icon' => 'building', 'text' => 'Toplu teslimat yükleme alanları']
                ]
            ],
            [
                'sku' => 'EFXZ-251-1500',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF EFXZ 251 - 1500 mm Çatal',
                'short_description' => '1500 mm çatal, uzun ve esnek yükler için alan kazandırır. Manevra dikkat gerektirir; Li-Ion tahrik ve hidrolik fren ile kontrollü ve güvenli çalışma sağlar.',
                'body' => '<section><h2>1500 mm: Maksimum Erişim, Dikkatli Manevra</h2><p>1500 mm çatal, palet dışı büyük ambalajlarda veya çift istiflenmiş uzun yüklerde gerekli erişimi sağlar. Daha uzun kaldıraç kolu nedeniyle hızlar sınırlı tutulmalı, operatör görüşü ve yük sabitleme prosedürleri titizlikle uygulanmalıdır. Yeniden üretilmiş gövde ve elektrikli tahrik, titreşimi ve bakım ihtiyacını azaltır.</p></section><section><h3>Teknik Odak</h3><p>Efektif kapasite, yük merkezi uzadıkça düşer; bu sebeple güvenli çalışma diyagramlarına uyulmalıdır. 2316 mm dönüş yarıçapı, uzun çatallarda geniş dönüş alanı gerektirebilir; planlamada koridor rezervi önerilir.</p></section><section><h3>Sonuç</h3><p>Uzun yüklerde doğru çözüm: dikkatli planlama ve stabil enerji platformu. 0216 755 3 555</p></section>',
                'use_cases' => [
                    ['icon' => 'industry', 'text' => 'Profil ve boru paketleri'],
                    ['icon' => 'couch', 'text' => 'Mobilya ve hacimli ürün taşımaları'],
                    ['icon' => 'print', 'text' => 'Büyük bobin ve rulo ambalaj'],
                    ['icon' => 'hammer', 'text' => 'Yapı elemanı panelleri'],
                    ['icon' => 'car', 'text' => 'Otomotiv uzun komponent kasaları'],
                    ['icon' => 'flask', 'text' => 'Kimyasal IBC ve özel paletler']
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
            $this->command->info('✅ Varyant eklendi: ' . $v['sku']);
        }
    }
}
