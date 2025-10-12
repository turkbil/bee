<?php
namespace Modules\Shop\Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class F4_201_Transpalet_2_Detailed extends Seeder {
    public function run(): void {
        $p = DB::table('shop_products')->where('sku', 'F4-201')->first();
        if (!$p) {$this->command->error('❌ Master bulunamadı (F4-201)'); return; }

        DB::table('shop_products')->where('product_id', $p->product_id)->update([

            'long_description' => json_encode(['tr' => '
<section class="hero-intro">
  <h2>İXTİF F4 201: 48V gücün kompakt verimlilikle buluştuğu sınıf</h2>
  <p><strong>Depoda gün henüz başlarken</strong>, dar koridorlar ve sık değişen görevler net bir çözüm ister. F4 201, yalnızca 140 kg servis ağırlığı ve <em>l2=400 mm</em> çatala kadar uzunluk ile beklenmedik bir çeviklik sunar. Çıkarılabilir iki adet 24V/20Ah Li-Ion batarya, hızlı şarj ve sıfıra yakın bakım ihtiyacı sayesinde vardiya temposuna kolayca ayak uydurur. 48V elektrik sistemi, 2.0 tona kadar yükleri güvenle taşıyacak gücü sağlarken elektromanyetik fren ve sağlam kumanda kolu operatöre güven verir. Günün sonunda F4 201, güç–ölçek–maliyet dengesini işletmenizin lehine çevirir.</p>
</section>

<section class="technical-power">
  <h3>Teknik güç ve ölçüler (profesyonel bakış)</h3>
  <p>F4 201, <strong>2000 kg</strong> nominal kapasiteyi <strong>600 mm</strong> yük merkezinde sunar. Yaya tip (pedestrian) kullanım için tasarlanan elektrikli sürüş sistemi, <strong>BLDC</strong> kontrol ile verimliliği artırır. <strong>Toplam genişlik 590/695 mm</strong> seçenekleriyle raf hatlarına uyum sağlarken, <strong>50×150×1150 mm</strong> çatal ölçüsü standart paletlerle tam örtüşür. <strong>Toplam uzunluk 1550 mm</strong>, <strong>dönüş yarıçapı 1360 mm</strong> ve <strong>zemin boşluğu 30 mm</strong> gibi değerler sıkışık alanlarda kesintisiz manevra sağlar. Performans tarafında <strong>4.5/5 km/s</strong> sürüş hızı, <strong>0.016/0.020 m/s</strong> kaldırma ve <strong>0.058/0.046 m/s</strong> indirme hızları kayıtlıdır. <strong>8%/16%</strong> tırmanma kabiliyeti ile rampalar güvenle aşılır. Enerji altyapısı <strong>48 V / 20 Ah</strong> (2×24V/20Ah) Li-Ion modüllerden oluşur; modüller <em>plug-in/out</em> ile saniyeler içinde değiştirilebilir. Poliüretan teker kombinasyonu (210×70 mm ön, 80×60 mm arka) düşük zemin aşınması ve sessiz çalışma sağlar; opsiyonel stabilizasyon tekerleri düzensiz zeminlerde yük güvenliğini artırır.</p>
</section>

<section class="operations">
  <h3>Operasyonel avantajlar ve toplam sahip olma maliyeti</h3>
  <p>Platform F tabanlı tasarım, farklı şasi ve çatal varyantlarını aynı aile çatısı altında toplayarak filo standardizasyonu ve yedek parça ortaklığı oluşturur. <strong>4 ünite/kolide</strong> tedarik ve <strong>40’ konteynere 164 ünite</strong> sığdırabilen paketleme stratejisi, deniz navlunu kalemlerinde %50’ye varan tasarruf potansiyeli sunar. Bu tasarruf, satın alma ve stok maliyetleriyle birleştiğinde F4 201’i rekabetçi bir çözüm haline getirir. Günlük kullanımda ergonomik kumanda kolu, elektromanyetik fren ve kolay erişimli kapak yapısı operatör güvenliğini yükseltir. Düşük ağırlık ve kompakt ölçüler dar koridor, rampa yaklaşımı ve yoğun <abbr title="cross-dock">çapraz sevkiyat</abbr> istasyonlarında akışı hızlandırır.</p>
</section>

<section class="battery-system">
  <h3>Enerji sistemi ve vardiya sürekliliği</h3>
  <p>Li-Ion kimya sayesinde fırsat şarjına uygundur; ara molalarda hızlıca takviye edilerek vardiya bütünlüğünü korur. Modüler yapı bakım planlarını sadeleştirir; yedek modül yatırımıyla kesintisiz operasyon mümkündür. Flip kapak tasarımı su girişine karşı batarya yuvasını korur, elektriksel güvenliği artırır. 48V mimari, 2 tonluk kapasiteyi canlı tutarken sürüş motoru <strong>0.9 kW</strong> ve kaldırma motoru <strong>0.7 kW</strong> değerleriyle enerji/verim dengesini optimize eder.</p>
</section>

<section class="closing">
  <h3>Sonuç: ölçülebilir verimlilik, sürdürülebilir maliyet</h3>
  <p>F4 201; kompakt boyut, hızlı bakım, modüler enerji ve esnek paketleme avantajlarını bir araya getirerek hem ilk yatırımda hem de işletme sürecinde maliyetleri kontrol altına alır. Depo akışınıza sayısal fayda yazdırmak için doğru parametrelerle konfigüre edin ve hemen devreye alın.</p>
  <p><strong>Teknik destek ve teklif için:</strong> 0216 755 3 555</p>
</section>
'], JSON_UNESCAPED_UNICODE),

            'primary_specs' => json_encode([
                ['icon' => 'weight-hanging', 'label' => 'Kapasite', 'value' => '2000 kg (c=600 mm)'],
                ['icon' => 'battery-full', 'label' => 'Batarya', 'value' => '48 V / 20 Ah (2×24V/20Ah) Li-Ion, çıkarılabilir'],
                ['icon' => 'gauge', 'label' => 'Hız', 'value' => '4.5 / 5 km/s (yüklü/boş)'],
                ['icon' => 'arrows-turn-right', 'label' => 'Dönüş', 'value' => 'Wa=1360 mm']
            ], JSON_UNESCAPED_UNICODE),

            'highlighted_features' => json_encode([
                ['icon' => 'battery-full', 'title' => '48V Li‑Ion Sistem', 'description' => 'Yüksek güç yoğunluğu ve hızlı fırsat şarjı'],
                ['icon' => 'compress', 'title' => 'Kompakt Şasi', 'description' => 'l2=400 mm ile dar koridor manevrası'],
                ['icon' => 'plug', 'title' => 'Tak‑Çıkar Modüller', 'description' => '2×24V/20Ah batarya saniyeler içinde değişir'],
                ['icon' => 'shield-alt', 'title' => 'Güvenli Frenleme', 'description' => 'Elektromanyetik fren ve sağlam kumanda kolu'],
                ['icon' => 'layer-group', 'title' => 'Platform F', 'description' => 'Ortak parça, esnek konfigürasyon ve servis kolaylığı'],
                ['icon' => 'box-open', 'title' => 'Lojistik Tasarrufu', 'description' => '4 ünite/kolide sevkiyat, 40’ta 164 ünite']
            ], JSON_UNESCAPED_UNICODE),

            'use_cases' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret merkezlerinde çapraz sevkiyat ve sipariş toplama beslemesi'],
                ['icon' => 'store', 'text' => 'Perakende dağıtım depolarında raf arası kısa mesafe transferi'],
                ['icon' => 'warehouse', 'text' => '3PL operasyonlarında yoğun vardiya içi hat besleme'],
                ['icon' => 'snowflake', 'text' => 'Soğuk oda giriş‑çıkış noktalarında hızlı palet hareketi'],
                ['icon' => 'pills', 'text' => 'İlaç/kozmetik depolarında hassas paket taşıma'],
                ['icon' => 'car', 'text' => 'Otomotiv yedek parça alanlarında rampa yaklaşımı'],
                ['icon' => 'tshirt', 'text' => 'Tekstil & hazır giyim kolileme ve paletleme süreçleri'],
                ['icon' => 'industry', 'text' => 'Üretim hücrelerinde yarı mamul (WIP) iç lojistik akışları']
            ], JSON_UNESCAPED_UNICODE),

            'competitive_advantages' => json_encode([
                ['icon' => 'bolt', 'text' => '48V güç mimarisi ile 2 ton yükte dinamik hız ve tırmanma'],
                ['icon' => 'battery-full', 'text' => 'Tak‑çıkar Li‑Ion modül: sıfıra yakın bakım, hızlı enerji değişimi'],
                ['icon' => 'arrows-alt', 'text' => 'Kompakt l2=400 mm gövdeyle dar alan çevikliği ve düşük ağırlık'],
                ['icon' => 'layer-group', 'text' => 'Platform F temeli: ortak parça havuzu ve kolay varyant yönetimi'],
                ['icon' => 'shield-alt', 'text' => 'Elektromanyetik fren ve sağlam şasi ile güvenli kullanım']
            ], JSON_UNESCAPED_UNICODE),

            'target_industries' => json_encode([
                ['icon' => 'box-open', 'text' => 'E‑ticaret ve Fulfillment'],
                ['icon' => 'warehouse', 'text' => '3PL ve Kontrat Lojistiği'],
                ['icon' => 'store', 'text' => 'Perakende Zincir Depoları'],
                ['icon' => 'cart-shopping', 'text' => 'Hızlı Tüketim (FMCG)'],
                ['icon' => 'snowflake', 'text' => 'Gıda ve Soğuk Zincir'],
                ['icon' => 'wine-bottle', 'text' => 'İçecek Lojistiği'],
                ['icon' => 'pills', 'text' => 'İlaç ve Medikal'],
                ['icon' => 'flask', 'text' => 'Kimya ve Tehlikesiz Kimyasallar'],
                ['icon' => 'spray-can', 'text' => 'Kozmetik ve Kişisel Bakım'],
                ['icon' => 'microchip', 'text' => 'Elektronik ve Bileşen'],
                ['icon' => 'tv', 'text' => 'Beyaz Eşya ve Tüketici Teknolojisi'],
                ['icon' => 'car', 'text' => 'Otomotiv Yedek Parça'],
                ['icon' => 'tshirt', 'text' => 'Tekstil ve Moda'],
                ['icon' => 'shoe-prints', 'text' => 'Ayakkabı ve Aksesuar'],
                ['icon' => 'couch', 'text' => 'Mobilya ve Ev Dekorasyon'],
                ['icon' => 'hammer', 'text' => 'Yapı Market ve DIY'],
                ['icon' => 'print', 'text' => 'Matbaa ve Ambalaj'],
                ['icon' => 'book', 'text' => 'Yayıncılık ve Kırtasiye'],
                ['icon' => 'seedling', 'text' => 'Tarım ve Bahçe Ürünleri'],
                ['icon' => 'paw', 'text' => 'Evcil Hayvan Ürünleri'],
                ['icon' => 'briefcase', 'text' => 'B2B Toptan Depoları'],
                ['icon' => 'building', 'text' => 'Şehir İçi Mikro Depolama']
            ], JSON_UNESCAPED_UNICODE),

            'warranty_info' => json_encode([
                'coverage' => 'Makineye satın alım tarihinden itibaren 12 ay fabrika garantisi verilir. Li‑Ion batarya modülleri satın alımdan itibaren 24 ay garanti kapsamındadır. Garanti, normal kullanım koşullarındaki üretim kusurlarını kapsar.',
                'duration_months' => 12,
                'battery_warranty_months' => 24
            ], JSON_UNESCAPED_UNICODE),

            'accessories' => json_encode([
                ['icon' => 'charging-station', 'name' => 'Harici hızlı şarj cihazı', 'description' => 'Vardiya aralarında hızlı enerji takviyesi için yüksek akımlı şarj ünitesi.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'plug', 'name' => 'Standart 48V şarj adaptörü', 'description' => 'Güvenli ve dengeli şarj için fabrika onaylı adaptör.', 'is_standard' => true, 'is_optional' => false, 'price' => null],
                ['icon' => 'cog', 'name' => 'Stabilizasyon teker seti', 'description' => 'Düzensiz zeminlerde yük stabilitesi ve denge artışı sağlar.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine'],
                ['icon' => 'battery-full', 'name' => 'Ek Li‑Ion batarya modülü', 'description' => 'Kesintisiz çalışma için yedek 24V/20Ah modül.', 'is_standard' => false, 'is_optional' => true, 'price' => 'Talep üzerine']
            ], JSON_UNESCAPED_UNICODE),

            'certifications' => json_encode([
                ['icon' => 'certificate', 'name' => 'CE', 'year' => '2024', 'authority' => 'EU']
            ], JSON_UNESCAPED_UNICODE),

            'faq_data' => json_encode([
                ['question' => '48V sistemin 24V’a göre operasyonel avantajı nedir?', 'answer' => '48V mimari daha düşük akım ile aynı gücü sağlar; kablolama ısınması azalır, verim artar ve 2 ton yükte hız düşümü daha sınırlı kalır.'],
                ['question' => 'Çıkarılabilir Li‑Ion bataryalar nasıl değiştiriliyor?', 'answer' => 'Flip kapak açılır, modül konnektörü ayrılır ve modül dışarı alınır. Tak‑çıkar tasarım birkaç saniyede güvenli değişim sağlar.'],
                ['question' => 'Standart çatal ölçüsü ve palet uyumluluğu nasıldır?', 'answer' => '50×150×1150 mm çatal yapısı EUR ve standart TR paletleriyle uyumlu; 560/685 mm çatal aralığı farklı paletlerde esneklik sağlar.'],
                ['question' => 'Dönüş yarıçapı ve dar koridor performansı nedir?', 'answer' => 'Wa=1360 mm dönüş yarıçapı ve l2=400 mm gövde sayesinde dar koridorlarda keskin manevralar güvenle yapılır.'],
                ['question' => 'Rampada tırmanma kabiliyeti değerleri nelerdir?', 'answer' => 'Maksimum %8 (yüklü) ve %16 (boş) tırmanma kabiliyeti ile depo rampalarında güvenli geçiş sunar.'],
                ['question' => 'Fren sistemi ve güvenlik donanımı neleri kapsar?', 'answer' => 'Elektromanyetik servis freni, acil durdurma ve sağlam kumanda kolu yapısı ile güvenli duruş ve kontrol sağlar.'],
                ['question' => 'Bakım periyotları ve tüketim parçaları hangileri?', 'answer' => 'Li‑Ion modüller bakım gerektirmez; periyodik kontrollerde tekerlek, frenleme ve bağlantı noktaları gözden geçirilir.'],
                ['question' => 'Sevkiyat ve yer tasarrufu sağlayan paketleme nasıl çalışır?', 'answer' => '4 ünite tek kolide toptan sevk edilebilir; 40’ konteynere 164 ünite sığar ve navlun maliyetlerinde ciddi avantaj sağlar.'],
                ['question' => 'Opsiyonel stabilizasyon tekerleri ne zaman önerilir?', 'answer' => 'Düzgün olmayan zeminler veya yüksek ağırlık merkezli yüklerde yan dengeyi artırmak için önerilir.'],
                ['question' => 'Enerji verimliliği ve fırsat şarjı uygulaması mümkün mü?', 'answer' => 'Li‑Ion kimya fırsat şarjına uygundur; kısa molalarda şarj edilerek vardiya kesintisiz sürdürülebilir.'],
                ['question' => 'Garanti kapsamı ve süresi nedir?', 'answer' => 'Makine 12 ay, Li‑Ion batarya modülleri 24 ay garanti altındadır. İXTİF satış‑servis: 0216 755 3 555.']
            ], JSON_UNESCAPED_UNICODE),

            'updated_at' => now(),
        ]);

        $this->command->info('✅ Detailed: F4-201 içerikleri güncellendi');
    }
}
