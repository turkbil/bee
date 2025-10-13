<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSi201_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'RSi201')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: RSi201');
            return;
        }

        $variants = [
            [
                'sku' => 'RSi201-1150',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF RSi201 - 1150 mm Çatal',
                'short_description' => 'Standart 1150 mm çatal boyu, çift katlı taşıma ile tipik EUR palet akışlarında optimum denge sunar. Dar koridorlarda kompakt şasi, elektrikli direksiyon ve 120 mm ilk kaldırma ile hızlı ve güvenli manevra sağlar.',
                'body' => '<section><h2>1150 mm: Depo Standardında Çift Katlı Verim</h2><p>1150 mm çatal uzunluğu, Avrupa standardı palet boyutlarıyla kusursuz uyum sağlayarak RSi201’in çift katlı kabiliyetini günlük operasyonların merkezine taşır. Standart boy, operatörlerin raf aralarında daha kısa dönüş yarıçapı ile hızlı hareket etmesini, palet giriş-çıkışlarında ise daha kolay hizalama yapmasını mümkün kılar. 24V/205Ah Li-ion batarya ve 3 kW kaldırma motoru, 0.18 m/sn kaldırma ve 0.36 m/sn indirme hızlarıyla çevrim sürelerini aşağı çeker.</p></section><section><h3>Teknik Odağı</h3><p>8/8 km/sa seyir hızı, 1920 mm dönüş yarıçapı ve 734 mm genişlik dar koridor performansını güvenli kılar. 120 mm ilk kaldırma rampalarda palet tabanını korur; poliüretan tekerlekler sessiz çalışır. Şeffaf mast kalkanı ve oransal kaldırma sistemi, iki paletli senaryolarda dahi hassas kontrol sağlar. Entegre 24V/30A şarj cihazı fırsat şarja olanak tanır; istenirse 24V/100A harici hızlı şarj ile iki saate kadar tam dolum yapılabilir.</p></section><section><h3>Sonuç</h3><p>Standart 1150 mm konfigürasyonu, E-ticaret ve 3PL merkezlerinde depo akışlarını hızlandırmak için güvenli bir varsayılandır. Detay ve teklif için 0216 755 3 555.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'EUR paletli sipariş toplama ve ayrıştırma'],
                    ['icon' => 'warehouse', 'text' => '3PL cross-dock ve hub içi transfer'],
                    ['icon' => 'store', 'text' => 'Perakende DC raf arası dolaşım'],
                    ['icon' => 'industry', 'text' => 'WIP paletlerinin hat beslenmesi'],
                    ['icon' => 'snowflake', 'text' => 'Soğuk giriş-çıkış noktalarında hızlı geçiş'],
                    ['icon' => 'pills', 'text' => 'Hassas ürünlerde düşük titreşimli taşıma']
                ]
            ],
            [
                'sku' => 'RSi201-1220',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF RSi201 - 1220 mm Çatal',
                'short_description' => '1220 mm uzun çatal, çift katlı taşımada daha uzun palet ve karma yüklerde alan toleransı sağlar. Manevra kabiliyeti korunurken hizalama kolaylığı ve palet denge noktası iyileşir.',
                'body' => '<section><h2>1220 mm: Esnek Palet Boylarına Uyum</h2><p>1220 mm çatal, standart dışı paletlerde ve karma yüklerde operatöre ek tolerans sağlar. RSi201’in oransal kaldırma ve elektrikli direksiyon kombinasyonu, daha uzun çatalda dahi hassas manevraları mümkün kılar. 0.18 m/sn kaldırma ve 0.36 m/sn indirme değerleri, çift katlı taşımalarda çevrimleri hızlandırır.</p></section><section><h3>Teknik Odağı</h3><p>24V/205Ah Li-ion güç paketi fırsat şarjıyla kesintisiz akış sunar. 120 mm ilk kaldırma rampalarda paletin altını güvene alır, 1920 mm dönüş yarıçapı dar koridorda çevikliği korur. Şeffaf mast kalkanı ile geniş görüş, kat giriş-çıkışlarında çarpışma riskini azaltır.</p></section><section><h3>Sonuç</h3><p>1220 mm varyantı, içecek ve gıda lojistiği gibi farklı palet boyutlarının bir arada kullanıldığı operasyonlarda konforlu bir seçimdir. Bilgi için 0216 755 3 555.</p></section>',
                'use_cases' => [
                    ['icon' => 'wine-bottle', 'text' => 'İçecek kasalarında uzun palet uygulamaları'],
                    ['icon' => 'snowflake', 'text' => 'Gıda depolarında karma palet taşımaları'],
                    ['icon' => 'cart-shopping', 'text' => 'FMCG kampanya yüklerinde çift palet akışı'],
                    ['icon' => 'warehouse', 'text' => 'Hub konsolidasyon sahasında esnek dizilim'],
                    ['icon' => 'car', 'text' => 'Otomotiv kutu/palet karmasında denge yönetimi'],
                    ['icon' => 'flask', 'text' => 'Kimya kolilerinde uzun yük desteği']
                ]
            ],
            [
                'sku' => 'RSi201-1000',
                'variant_type' => 'catal-uzunlugu',
                'title' => 'İXTİF RSi201 - 1000 mm Çatal',
                'short_description' => '1000 mm kısa çatal, en dar koridorlarda min. salınım ve hızlanan dönüşlerle verimi artırır. İki paletli kısa mesafe transferlerinde hizalama ve hız avantajı sunar.',
                'body' => '<section><h2>1000 mm: Dar Koridorda Hız ve Kontrol</h2><p>En dar koridorlara sahip tesislerde 1000 mm çatal, palet giriş-çıkışlarında ekstra manevra alanı kazandırır. RSi201’in elektrikli direksiyon sistemi ve otomatik viraj yavaşlatması, kısa çatal boyunu güvenli ve öngörülebilir bir kontrolle birleştirir. Çift katlı taşıma, kısa mesafe yoğun hat beslemelerinde çevrimi hızlandırır.</p></section><section><h3>Teknik Odağı</h3><p>8/8 km/sa hız, 1920 mm dönüş yarıçapı ve 734 mm gövde genişliği ile raf arası geçişler akıcıdır. 120 mm ilk kaldırma, rampalarda ve eşiklerde sürtünmeyi azaltır. 24V/205Ah Li-ion güç sistemi, vardiya içi fırsat şarjlarıyla sürekli erişilebilirlik sağlar.</p></section><section><h3>Sonuç</h3><p>1000 mm konfigürasyonu, şehir içi mikro-fulfillment ve katlı tesislerde hız-çeviklik dengesi arayan operasyonlar için güçlü bir seçenektir. Detay için 0216 755 3 555.</p></section>',
                'use_cases' => [
                    ['icon' => 'box-open', 'text' => 'Mikro-fulfillment alanlarında hızlı çevrim'],
                    ['icon' => 'warehouse', 'text' => 'Çok katlı tesislerde kat içi transfer'],
                    ['icon' => 'store', 'text' => 'Perakende arka depo dar koridorları'],
                    ['icon' => 'industry', 'text' => 'Hücre içi kısa mesafe malzeme taşıma'],
                    ['icon' => 'print', 'text' => 'Ambalaj sahasında dar alan besleme'],
                    ['icon' => 'tshirt', 'text' => 'Tekstil raf arası kısa dönüşler']
                ]
            ],
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
