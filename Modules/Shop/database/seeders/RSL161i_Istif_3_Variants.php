<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RSL161i_Istif_3_Variants extends Seeder
{
    public function run(): void
    {
        $m = DB::table('shop_products')->where('sku', 'RSL161i')->first();
        if (!$m) {
            $this->command->error('❌ Master bulunamadı: RSL161i');
            return;
        }
        $variants = [[
            'sku' => 'RSL161i-1150',
            'variant_type' => 'catal-uzunlugu',
            'title' => 'İXTİF RSL161i - 1150 mm Çatal',
            'short_description' => '1150 mm standart çatal uzunluğu ile EUR paletlerde yüksek hızda istif ve transfer için optimize edildi. Dar raf aralarında çevik, uzun mesafede platform konforlu.',
            'body' => '<section><h2>İXTİF RSL161i 1150 mm: Ağır Hizmet Li-Ion İstif Makinesi</h2><p>Depolarda mesafe uzadıkça operatör konforu ve enerji sürekliliği kritik hâle gelir. Katlanır platformu dar koridor çevikliğiyle birleştiren İXTİF RSL161i 1150 mm, gün boyu istifleme ve taşıma işlerinde yüksek hız, düşük yorgunluk ve tutarlı güvenlik sağlar. EUR palet uyumu öncelikli operasyonlar için 1150 mm çatal optimum denge sunar; dar alan dönüşlerini kolaylaştırır. </p></section><section><h3>Teknik Güç ve Verimlilik</h3><p>205Ah Li-Ion batarya, fırsat şarjına uygun kimyası sayesinde plan dışı duruşları azaltır; harici şarj soketi batarya bölmesini açmadan bağlantı imkânı verir. AC sürüş motoru 3 kW gücüyle 9/11 km/saate varan seyir sağlar; 4.5 kW kaldırma motoru ise 0.26 m/s seviyesinde hassas ve hızlı kaldırma/indirme kabiliyeti sunar. Elektronik güç destekli direksiyon, dönüşlerde otomatik hız düşürme ile stabiliteyi artırır. 850 mm toplam genişlik ve kompakt şasi yapısı, 1000×1200 çapraz paletlerde dar alan manevrasını kolaylaştırır. Yeni renkli gösterge, akü ve sistem parametrelerinin anlık takibini sağlar; oransal hidrolik kontrol, yükü rafa zarar vermeden milimetrik konumlandırır. </p></section><section><h3>Operasyonel Kazançlar</h3><p>Uzun mesafe iç taşıma ile sık istifin bir arada yürütüldüğü tesislerde İXTİF RSL161i 1150 mm, operatörün ayakta veya platform üstünde güvenli biçimde ilerlemesini sağlar. Titreşim sönümlü platform sürüş konforunu yükseltir; ergonomik timon başı farklı el boyutlarına uygundur. Planlı bakım aralıkları uzundur ve Li-Ion batarya bakım gerektirmez; bu sayede toplam sahip olma maliyeti düşer. VDI 2198’e göre 54.4 t/saat işlem hacmi ve 37 t/kWh enerji verimliliği, yoğun vardiyalarda hedeflenen throughput’u destekler. Depo güvenliği açısından elektromanyetik fren ve yokuş kalkış kontrolü, riskli alanlarda ek koruma katmanı oluşturur. </p></section><section><h3>Sonuç</h3><p>İster üretim beslemesi ister sevkiyat öncesi konsolidasyon olsun, İXTİF RSL161i 1150 mm iş akışını hızlandırır, operatör yorgunluğunu azaltır ve enerji masrafını kontrol altında tutar. Demo ve teknik danışmanlık için 0216 755 3 555. </p></section>',
            'use_cases' => [['icon' => 'box-open', 'text' => 'E-ticaret merkezlerinde tek paletli hızlı toplama'], ['icon' => 'warehouse', 'text' => '3PL yüksek devirli hat besleme'], ['icon' => 'industry', 'text' => 'Üretim hücresinde WIP palet taşıma'], ['icon' => 'car', 'text' => 'Otomotiv yedek parça koli transferi'], ['icon' => 'flask', 'text' => 'Kimyasal varil paleti konumlama'], ['icon' => 'store', 'text' => 'Perakende DC sevk konsolidasyonu']]
        ], [
            'sku' => 'RSL161i-1220',
            'variant_type' => 'catal-uzunlugu',
            'title' => 'İXTİF RSL161i - 1220 mm Çatal',
            'short_description' => '1220 mm çatal, kaplama ve içecek gibi hacimli yüklerde palet stabilitesini artırır; yüksek kaldırma hassasiyetiyle güvenli istif sağlar.',
            'body' => '<section><h2>İXTİF RSL161i 1220 mm: Ağır Hizmet Li-Ion İstif Makinesi</h2><p>Depolarda mesafe uzadıkça operatör konforu ve enerji sürekliliği kritik hâle gelir. Katlanır platformu dar koridor çevikliğiyle birleştiren İXTİF RSL161i 1220 mm, gün boyu istifleme ve taşıma işlerinde yüksek hız, düşük yorgunluk ve tutarlı güvenlik sağlar. Uzun yüklerde ağırlık dağılımını iyileştirir; yüksek raflarda oransal hidrolikle milimetrik yavaşlatma imkânı verir. </p></section><section><h3>Teknik Güç ve Verimlilik</h3><p>205Ah Li-Ion batarya, fırsat şarjına uygun kimyası sayesinde plan dışı duruşları azaltır; harici şarj soketi batarya bölmesini açmadan bağlantı imkânı verir. AC sürüş motoru 3 kW gücüyle 9/11 km/saate varan seyir sağlar; 4.5 kW kaldırma motoru ise 0.26 m/s seviyesinde hassas ve hızlı kaldırma/indirme kabiliyeti sunar. Elektronik güç destekli direksiyon, dönüşlerde otomatik hız düşürme ile stabiliteyi artırır. 850 mm toplam genişlik ve kompakt şasi yapısı, 1000×1200 çapraz paletlerde dar alan manevrasını kolaylaştırır. Yeni renkli gösterge, akü ve sistem parametrelerinin anlık takibini sağlar; oransal hidrolik kontrol, yükü rafa zarar vermeden milimetrik konumlandırır. </p></section><section><h3>Operasyonel Kazançlar</h3><p>Uzun mesafe iç taşıma ile sık istifin bir arada yürütüldüğü tesislerde İXTİF RSL161i 1220 mm, operatörün ayakta veya platform üstünde güvenli biçimde ilerlemesini sağlar. Titreşim sönümlü platform sürüş konforunu yükseltir; ergonomik timon başı farklı el boyutlarına uygundur. Planlı bakım aralıkları uzundur ve Li-Ion batarya bakım gerektirmez; bu sayede toplam sahip olma maliyeti düşer. VDI 2198’e göre 54.4 t/saat işlem hacmi ve 37 t/kWh enerji verimliliği, yoğun vardiyalarda hedeflenen throughput’u destekler. Depo güvenliği açısından elektromanyetik fren ve yokuş kalkış kontrolü, riskli alanlarda ek koruma katmanı oluşturur. </p></section><section><h3>Sonuç</h3><p>İster üretim beslemesi ister sevkiyat öncesi konsolidasyon olsun, İXTİF RSL161i 1220 mm iş akışını hızlandırır, operatör yorgunluğunu azaltır ve enerji masrafını kontrol altında tutar. Demo ve teknik danışmanlık için 0216 755 3 555. </p></section>',
            'use_cases' => [['icon' => 'wine-bottle', 'text' => 'İçecek kasaları ve shrink paket paletleri'], ['icon' => 'couch', 'text' => 'Mobilya komponent paletleri'], ['icon' => 'snowflake', 'text' => 'Soğuk depoda geniş tabanlı yükler'], ['icon' => 'pills', 'text' => 'Medikal sarf ve cihaz kutuları'], ['icon' => 'print', 'text' => 'Ambalaj-karton palet istifi'], ['icon' => 'cart-shopping', 'text' => 'FMCG miks palet transferi']]
        ]];

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
        $this->command->info("✅ Variants eklendi: RSL161i");
    }
}
